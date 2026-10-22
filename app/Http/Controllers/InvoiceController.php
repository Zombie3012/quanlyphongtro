<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    public function index(Request $request, Property $property)
    {
        $query = Invoice::with(['contract.room', 'contract.tenant'])
            ->whereHas('contract', function ($query) use ($property): void {
                $query->where('property_id', $property->id);
            });

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($query) use ($search): void {
                $query->where('month', 'like', '%'.$search.'%')
                    ->orWhere('status', 'like', '%'.$search.'%')
                    ->orWhereHas('contract.room', function ($roomQuery) use ($search): void {
                        $roomQuery->where('room_number', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('contract.tenant', function ($tenantQuery) use ($search): void {
                        $tenantQuery->where('full_name', 'like', '%'.$search.'%');
                    });
            });
        }

        $invoices = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('invoices.index', compact('invoices', 'property'));
    }

    public function create(Property $property)
    {
        $contracts = $property->contracts()->with(['room', 'tenant'])->get();

        return view('invoices.create', compact('contracts', 'property'));
    }

    public function store(Request $request, Property $property)
    {
        $validated = $request->validate($this->invoiceRules());

        DB::transaction(function () use ($validated, $property): void {
            $contract = $property->contracts()
                ->whereKey($validated['contract_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureNoDuplicateInvoice($contract->id, $validated['month']);
            $rentAmount = (string) $contract->monthly_rent;

            $contract->invoices()->create([
                'rent_amount' => $rentAmount,
                'month' => $validated['month'],
                'electricity' => $validated['electricity'],
                'water' => $validated['water'],
                'service_fee' => $validated['service_fee'],
                'other_fee' => $validated['other_fee'],
                'total' => $this->calculateTotal(
                    $rentAmount,
                    $validated['electricity'],
                    $validated['water'],
                    $validated['service_fee'],
                    $validated['other_fee'],
                ),
                'status' => $validated['status'],
            ]);
        });

        return redirect()
            ->route('invoices.index', $property)
            ->with('success', 'Tạo hóa đơn thành công!');
    }

    public function show(Property $property, Invoice $invoice)
    {
        $this->ensureInvoiceBelongsToProperty($invoice, $property);
        $invoice->load(['contract.room', 'contract.tenant']);

        return view('invoices.show', compact('invoice', 'property'));
    }

    public function edit(Property $property, Invoice $invoice)
    {
        $this->ensureInvoiceBelongsToProperty($invoice, $property);

        return view('invoices.edit', compact('invoice', 'property'));
    }

    public function update(Request $request, Property $property, Invoice $invoice)
    {
        DB::transaction(function () use ($request, $property, $invoice): void {
            $invoice = Invoice::query()->whereKey($invoice->id)->lockForUpdate()->firstOrFail();
            $this->ensureInvoiceBelongsToProperty($invoice, $property);

            if ($invoice->status === 'Đã thanh toán') {
                throw ValidationException::withMessages([
                    'invoice' => 'Hóa đơn đã thanh toán không thể chỉnh sửa; hãy dùng quy trình điều chỉnh riêng.',
                ]);
            }

            if ($invoice->rent_amount === null) {
                throw ValidationException::withMessages([
                    'invoice' => 'Hóa đơn cũ chưa có snapshot tiền thuê; cần đối soát trước khi cập nhật.',
                ]);
            }

            $validated = $request->validate($this->updateRules());
            $rentAmount = (string) $invoice->rent_amount;

            $invoice->update([
                'electricity' => $validated['electricity'],
                'water' => $validated['water'],
                'service_fee' => $validated['service_fee'],
                'other_fee' => $validated['other_fee'],
                'total' => $this->calculateTotal(
                    $rentAmount,
                    $validated['electricity'],
                    $validated['water'],
                    $validated['service_fee'],
                    $validated['other_fee'],
                ),
                'status' => $validated['status'],
            ]);
        });

        return redirect()
            ->route('invoices.index', $property)
            ->with('success', 'Cập nhật hóa đơn thành công!');
    }

    public function destroy(Property $property, Invoice $invoice)
    {
        $this->ensureInvoiceBelongsToProperty($invoice, $property);

        if ($invoice->status === 'Đã thanh toán') {
            throw ValidationException::withMessages([
                'invoice' => 'Hóa đơn đã thanh toán không thể xóa.',
            ]);
        }

        if ($invoice->rent_amount === null) {
            throw ValidationException::withMessages([
                'invoice' => 'Hóa đơn lịch sử chưa có snapshot tiền thuê; cần đối soát trước khi xóa.',
            ]);
        }

        $invoice->delete();

        return redirect()
            ->route('invoices.index', $property)
            ->with('success', 'Xóa hóa đơn thành công!');
    }

    /** @return array<string, mixed> */
    private function invoiceRules(): array
    {
        return [
            'contract_id' => ['required', 'integer', 'exists:contracts,id'],
            'month' => ['required', 'date_format:Y-m'],
            ...$this->moneyRules(),
            'status' => ['required', 'in:Chưa thanh toán,Đã thanh toán'],
        ];
    }

    /** @return array<string, mixed> */
    private function updateRules(): array
    {
        return [
            ...$this->moneyRules(),
            'status' => ['required', 'in:Chưa thanh toán,Đã thanh toán'],
        ];
    }

    /** @return array<string, mixed> */
    private function moneyRules(): array
    {
        $rules = ['required', 'numeric', 'min:0', 'decimal:0,2', 'max:9999999999.99'];

        return [
            'electricity' => $rules,
            'water' => $rules,
            'service_fee' => $rules,
            'other_fee' => $rules,
        ];
    }

    private function ensureNoDuplicateInvoice(int $contractId, string $month): void
    {
        if (Invoice::query()->where('contract_id', $contractId)->where('month', $month)->exists()) {
            throw ValidationException::withMessages([
                'month' => 'Hợp đồng này đã có hóa đơn cho tháng được chọn.',
            ]);
        }
    }

    private function ensureInvoiceBelongsToProperty(Invoice $invoice, Property $property): void
    {
        abort_unless(
            $invoice->contract !== null
                && (int) $invoice->contract->property_id === (int) $property->id,
            404,
        );
    }

    private function calculateTotal(
        string|int|float $rentAmount,
        string|int|float $electricity,
        string|int|float $water,
        string|int|float $serviceFee,
        string|int|float $otherFee,
    ): string {
        $totalCents = $this->toCents($rentAmount)
            + $this->toCents($electricity)
            + $this->toCents($water)
            + $this->toCents($serviceFee)
            + $this->toCents($otherFee);

        if ($totalCents > 999999999999) {
            throw ValidationException::withMessages([
                'other_fee' => 'Tổng hóa đơn vượt giới hạn lưu trữ của cơ sở dữ liệu.',
            ]);
        }

        return intdiv($totalCents, 100).'.'.str_pad((string) ($totalCents % 100), 2, '0', STR_PAD_LEFT);
    }

    private function toCents(string|int|float $amount): int
    {
        $amount = (string) $amount;

        if (! preg_match('/\A\d+(?:\.\d{1,2})?\z/', $amount)) {
            throw new \LogicException('Validated money value has an unsupported format.');
        }

        [$whole, $fraction] = array_pad(explode('.', $amount, 2), 2, '');

        return ((int) $whole * 100) + (int) str_pad($fraction, 2, '0');
    }
}
