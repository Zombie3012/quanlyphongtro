@extends('layouts.app')

@section('title', 'Sửa hóa đơn')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Sửa hóa đơn #{{ $invoice->id }}</h2>
    <a href="{{ route('invoices.index', $property) }}" class="btn btn-secondary">Quay lại</a>
</div>

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if ($invoice->rent_amount === null)
    <div class="alert alert-warning">Hóa đơn lịch sử chưa có snapshot tiền thuê. Tổng cũ được giữ nguyên; cần đối soát trước khi sửa.</div>
@elseif ($invoice->status === 'Đã thanh toán')
    <div class="alert alert-warning">Hóa đơn đã thanh toán được khóa để bảo toàn lịch sử.</div>
@else
    <form action="{{ route('invoices.update', ['property' => $property->id, 'invoice' => $invoice->id]) }}" method="POST" id="invoice-form">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Hợp đồng (không thể đổi trên hóa đơn đã lập)</label>
            <div class="form-control bg-light">#{{ $invoice->contract_id }} — Phòng {{ $invoice->contract?->room?->room_number ?? 'N/A' }} — {{ $invoice->contract?->tenant?->full_name ?? 'N/A' }}</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Tháng (không thể đổi trên hóa đơn đã lập)</label>
            <div class="form-control bg-light">{{ $invoice->month }}</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Tiền thuê snapshot</label>
            <output id="rent-preview" data-rent="{{ $invoice->rent_amount }}" class="form-control bg-light">{{ number_format((float) $invoice->rent_amount, 2, ',', '.') }} VNĐ</output>
        </div>

        @foreach (['electricity' => 'Tiền điện', 'water' => 'Tiền nước', 'service_fee' => 'Phí dịch vụ', 'other_fee' => 'Phí khác'] as $field => $label)
            <div class="mb-3">
                <label class="form-label" for="{{ $field }}">{{ $label }}</label>
                <input type="number" step="0.01" min="0" max="9999999999.99" name="{{ $field }}" id="{{ $field }}" class="form-control @error($field) is-invalid @enderror" value="{{ old($field, $invoice->$field) }}" required>
                @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        @endforeach

        <div class="mb-3">
            <label class="form-label" for="total-preview">Tổng dự kiến (server sẽ tính lại khi lưu)</label>
            <output id="total-preview" class="form-control bg-light fw-bold">{{ number_format((float) $invoice->total, 2, ',', '.') }} VNĐ</output>
        </div>

        <div class="mb-3">
            <label class="form-label" for="status">Trạng thái</label>
            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                <option value="Chưa thanh toán" @selected(old('status', $invoice->status) === 'Chưa thanh toán')>Chưa thanh toán</option>
                <option value="Đã thanh toán" @selected(old('status', $invoice->status) === 'Đã thanh toán')>Đã thanh toán</option>
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('invoices.index', $property) }}" class="btn btn-secondary">Hủy</a>
    </form>

    <script>
        (() => {
            const rent = Math.round(Number(document.getElementById('rent-preview').dataset.rent) * 100);
            const amounts = ['electricity', 'water', 'service_fee', 'other_fee'];
            const formatter = new Intl.NumberFormat('vi-VN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            const render = () => {
                const total = rent + amounts.reduce((sum, field) => sum + Math.round((Number(document.getElementById(field).value) || 0) * 100), 0);
                document.getElementById('total-preview').textContent = `${formatter.format(total / 100)} VNĐ`;
            };
            amounts.forEach((field) => document.getElementById(field).addEventListener('input', render));
            render();
        })();
    </script>
@endif
@endsection
