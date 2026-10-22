<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{
    /**
     * Danh sách khu trọ.
     */
    public function index(Request $request)
    {
        // Tải sẵn quan hệ 'managers' để tránh truy vấn lặp (N+1 query)
        $query = Property::with('managers');

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('address', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $properties = $query
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('properties.index', compact('properties'));
    }

    /**
     * Form thêm khu trọ.
     */
    public function create()
    {
        // Chỉ hiển thị Manager chưa được phân công.
        $managers = User::where('role', 'manager')
            ->whereNull('property_id')
            ->orderBy('name')
            ->get();

        return view('properties.create', compact('managers'));
    }

    /**
     * Lưu khu trọ mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            'manager_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
        ]);

        $managerId = $validated['manager_id'] ?? null;
        unset($validated['manager_id']);

        DB::transaction(function () use ($validated, $managerId) {
            $property = Property::create($validated);

            if ($managerId !== null) {
                $manager = User::where('id', $managerId)
                    ->where('role', 'manager')
                    ->whereNull('property_id')
                    ->lockForUpdate()
                    ->first();

                if (!$manager) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'manager_id' => 'Manager không hợp lệ hoặc đã được phân công cho khu khác.',
                    ]);
                }

                $manager->update([
                    'property_id' => $property->id,
                ]);
            }
        });

        return redirect()
            ->route('properties.index')
            ->with('success', 'Thêm khu trọ thành công!');
    }

    /**
     * Form chỉnh sửa khu trọ.
     */
    public function edit(Property $property)
    {
        // Manager chưa được phân công hoặc đang quản lý khu hiện tại.
        $managers = User::where('role', 'manager')
            ->where(function ($query) use ($property) {
                $query->whereNull('property_id')
                    ->orWhere('property_id', $property->id);
            })
            ->orderBy('name')
            ->get();

        $currentManager = User::where('role', 'manager')
            ->where('property_id', $property->id)
            ->first();

        return view('properties.edit', compact(
            'property',
            'managers',
            'currentManager'
        ));
    }

    /**
     * Cập nhật khu trọ.
     */
    public function update(Request $request, Property $property)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            'manager_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
        ]);

        $managerId = $validated['manager_id'] ?? null;
        unset($validated['manager_id']);

        DB::transaction(function () use ($validated, $managerId, $property) {
            // Khóa khu trọ để tránh cập nhật đồng thời.
            $property = Property::whereKey($property->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($managerId !== null) {
                $manager = User::where('id', $managerId)
                    ->where('role', 'manager')
                    ->where(function ($query) use ($property) {
                        $query->whereNull('property_id')
                            ->orWhere('property_id', $property->id);
                    })
                    ->lockForUpdate()
                    ->first();

                if (!$manager) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'manager_id' => 'Manager không hợp lệ hoặc đang quản lý khu khác.',
                    ]);
                }
            }

            // Gỡ phân công Manager cũ của khu này.
            User::where('role', 'manager')
                ->where('property_id', $property->id)
                ->when(
                    $managerId !== null,
                    fn ($query) => $query->where('id', '!=', $managerId)
                )
                ->update(['property_id' => null]);

            $property->update($validated);

            // Phân công Manager mới nếu đã chọn.
            if ($managerId !== null) {
                User::where('id', $managerId)
                    ->where('role', 'manager')
                    ->update(['property_id' => $property->id]);
            }
        });

        return redirect()
            ->route('properties.index')
            ->with('success', 'Cập nhật khu trọ thành công!');
    }

    /**
     * Tạm thời không cho xóa khu trọ.
     * Tránh làm mất dữ liệu liên quan khi chưa kiểm tra khóa ngoại.
     */
    public function destroy(Property $property)
{
    // Không cho xóa khu trọ đang có phòng.
    if ($property->rooms()->exists()) {
        return redirect()
            ->route('properties.index')
            ->with('error', 'Không thể xóa khu trọ vì vẫn còn phòng.');
    }

    // Không cho xóa khu trọ đang có người thuê.
    if ($property->tenants()->exists()) {
        return redirect()
            ->route('properties.index')
            ->with('error', 'Không thể xóa khu trọ vì vẫn còn người thuê.');
    }

    // Không cho xóa khu trọ đang có hợp đồng.
    if ($property->contracts()->exists()) {
        return redirect()
            ->route('properties.index')
            ->with('error', 'Không thể xóa khu trọ vì vẫn còn hợp đồng.');
    }

    // Gỡ liên kết Manager trước khi xóa.
    \App\Models\User::where('property_id', $property->id)
        ->where('role', 'manager')
        ->update(['property_id' => null]);

    $property->delete();

    return redirect()
        ->route('properties.index')
        ->with('success', 'Xóa khu trọ thành công!');
}
}