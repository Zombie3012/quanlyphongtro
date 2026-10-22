<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    public function index(Request $request, Property $property)
    {
        $query = $property->contracts()
            ->with(['room', 'tenant']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->whereHas('room', function ($roomQuery) use ($search) {
                    $roomQuery->where(
                        'room_number',
                        'like',
                        '%' . $search . '%'
                    );
                });

                $q->orWhereHas('tenant', function ($tenantQuery) use ($search) {
                    $tenantQuery->where(
                        'full_name',
                        'like',
                        '%' . $search . '%'
                    );
                });

            });
        }

        $contracts = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('contracts.index', compact(
            'contracts',
            'property'
        ));
    }

    public function create(Property $property)
    {
        $rooms = $property->rooms()
            ->where('status', 'Trống')
            ->get();

        $tenants = $property->tenants()->get();

        return view('contracts.create', compact(
            'property',
            'rooms',
            'tenants'
        ));
    }


    public function store(Request $request, Property $property)
    {
        $validated = $request->validate([
            'room_id' => 'required|integer',
            'tenant_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'monthly_rent' => 'required|numeric|min:0',
            'deposit' => 'required|numeric|min:0',
            'status' => 'required|in:Nháp,Đang hiệu lực,Đã kết thúc,Đã hủy',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $property) {
            // Khóa và lấy phòng thuộc đúng khu trọ.
            $room = $property->rooms()
                ->whereKey($validated['room_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // Chỉ lấy người thuê thuộc đúng khu trọ.
            $tenant = $property->tenants()
                ->whereKey($validated['tenant_id'])
                ->firstOrFail();

            if ($validated['status'] === 'Đang hiệu lực') {
                $hasActiveContract = Contract::where(
                    'room_id',
                    $room->id
                )
                    ->where('status', 'Đang hiệu lực')
                    ->lockForUpdate()
                    ->exists();

                if (
                    $room->status !== 'Trống' ||
                    $hasActiveContract
                ) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'room_id' => 'Phòng không còn trống hoặc đã có hợp đồng đang hiệu lực.',
                    ]);
                }
            }

            $contract = $property->contracts()->create([
                'room_id' => $room->id,
                'tenant_id' => $tenant->id,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
                'monthly_rent' => $validated['monthly_rent'],
                'deposit' => $validated['deposit'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $this->syncRoomStatus($room->id);
        });

        return redirect()
            ->route('contracts.index', $property)
            ->with('success', 'Tạo hợp đồng thành công!');
    }

    public function show(
        Property $property,
        Contract $contract
    ) {
        $this->ensureContractBelongsToProperty($contract, $property);

        $contract->load([
            'room',
            'tenant',
            'invoices',
        ]);

        return view('contracts.show', compact(
            'contract',
            'property'
        ));
    }

    public function edit(
        Property $property,
        Contract $contract
    ) {
        $this->ensureContractBelongsToProperty($contract, $property);

        $rooms = $property->rooms()->get();

        $tenants = $property->tenants()->get();

        return view('contracts.edit', compact(
            'contract',
            'property',
            'rooms',
            'tenants'
        ));
    }


    public function update(
        Request $request,
        Property $property,
        Contract $contract
    ) {
        $this->ensureContractBelongsToProperty($contract, $property);

        $validated = $request->validate([
            'room_id' => 'required|integer',
            'tenant_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'monthly_rent' => 'required|numeric|min:0',
            'deposit' => 'required|numeric|min:0',
            'status' => 'required|in:Nháp,Đang hiệu lực,Đã kết thúc,Đã hủy',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $property, $contract) {
            $contract = Contract::whereKey($contract->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureContractBelongsToProperty($contract, $property);

            $oldRoomId = $contract->room_id;

            $room = $property->rooms()
                ->whereKey($validated['room_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $tenant = $property->tenants()
                ->whereKey($validated['tenant_id'])
                ->firstOrFail();

            if ($validated['status'] === 'Đang hiệu lực') {
                $hasOtherActiveContract = Contract::where(
                    'room_id',
                    $room->id
                )
                    ->where('status', 'Đang hiệu lực')
                    ->where('id', '<>', $contract->id)
                    ->lockForUpdate()
                    ->exists();

                $isSameRoomWithActiveContract =
                    (int) $room->id === (int) $oldRoomId
                    && $contract->status === 'Đang hiệu lực'
                    && $room->status === 'Đang thuê';

                if (
                    $hasOtherActiveContract ||
                    (
                        $room->status !== 'Trống'
                        && !$isSameRoomWithActiveContract
                    )
                ) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'room_id' => 'Phòng mới không còn trống hoặc đã có hợp đồng đang hiệu lực.',
                    ]);
                }
            }

            $contract->update([
                'room_id' => $room->id,
                'tenant_id' => $tenant->id,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
                'monthly_rent' => $validated['monthly_rent'],
                'deposit' => $validated['deposit'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Đồng bộ cả phòng cũ và phòng mới.
            $this->syncRoomStatus($oldRoomId);
            $this->syncRoomStatus($room->id);
        });

        return redirect()
            ->route('contracts.index', $property)
            ->with('success', 'Cập nhật hợp đồng thành công!');
    }


    public function destroy(
        Property $property,
        Contract $contract
    ) {
        $this->ensureContractBelongsToProperty($contract, $property);

        DB::transaction(function () use ($property, $contract) {
            $contract = Contract::whereKey($contract->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureContractBelongsToProperty($contract, $property);

            $roomId = $contract->room_id;

            // Xóa hợp đồng trước, sau đó kiểm tra các hợp đồng còn lại.
            $contract->delete();

            $this->syncRoomStatus($roomId);
        });

        return redirect()
            ->route('contracts.index', $property)
            ->with('success', 'Xóa hợp đồng thành công!');
    }

    private function ensureContractBelongsToProperty(
        Contract $contract,
        Property $property
    ): void {
        abort_unless(
            (int) $contract->property_id === (int) $property->id,
            404
        );
    }

    private function syncRoomStatus(int $roomId): void
    {
        $room = Room::find($roomId);

        if (!$room) {
            return;
        }

        $hasActiveContract = Contract::where('room_id', $roomId)
            ->where('status', 'Đang hiệu lực')
            ->exists();

        if ($hasActiveContract) {
            $room->update([
                'status' => 'Đang thuê',
            ]);

            return;
        }

        // Chỉ trả phòng về Trống nếu trạng thái hiện tại là Đang thuê.
        // Không ghi đè trạng thái Đang sửa.
        if ($room->status === 'Đang thuê') {
            $room->update([
                'status' => 'Trống',
            ]);
        }
    }


}
