@extends('layouts.app')

@section('title', 'Tạo hóa đơn')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Tạo hóa đơn</h2>
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

<form action="{{ route('invoices.store', $property) }}" method="POST" id="invoice-form">
    @csrf

    <div class="mb-3">
        <label class="form-label" for="contract_id">Hợp đồng</label>
        <select name="contract_id" id="contract_id" class="form-select @error('contract_id') is-invalid @enderror" required>
            <option value="">-- Chọn hợp đồng --</option>
            @foreach ($contracts as $contract)
                <option value="{{ $contract->id }}" data-rent="{{ $contract->monthly_rent }}" @selected(old('contract_id') == $contract->id)>
                    Phòng {{ $contract->room->room_number ?? 'N/A' }} — {{ $contract->tenant->full_name ?? 'N/A' }}
                </option>
            @endforeach
        </select>
        @error('contract_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label" for="month">Tháng</label>
        <input type="month" name="month" id="month" class="form-control @error('month') is-invalid @enderror" value="{{ old('month') }}" required>
        @error('month')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Tiền thuê theo hợp đồng</label>
        <output id="rent-preview" class="form-control bg-light">0,00 VNĐ</output>
    </div>

    @foreach (['electricity' => 'Tiền điện', 'water' => 'Tiền nước', 'service_fee' => 'Phí dịch vụ', 'other_fee' => 'Phí khác'] as $field => $label)
        <div class="mb-3">
            <label class="form-label" for="{{ $field }}">{{ $label }}</label>
            <input type="number" step="0.01" min="0" max="9999999999.99" name="{{ $field }}" id="{{ $field }}" class="form-control @error($field) is-invalid @enderror" value="{{ old($field, '0.00') }}" required>
            @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @endforeach

    <div class="mb-3">
        <label class="form-label" for="total-preview">Tổng dự kiến (server sẽ tính lại khi lưu)</label>
        <output id="total-preview" class="form-control bg-light fw-bold">0,00 VNĐ</output>
    </div>

    <div class="mb-3">
        <label class="form-label" for="status">Trạng thái</label>
        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="Chưa thanh toán" @selected(old('status', 'Chưa thanh toán') === 'Chưa thanh toán')>Chưa thanh toán</option>
            <option value="Đã thanh toán" @selected(old('status') === 'Đã thanh toán')>Đã thanh toán</option>
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <button type="submit" class="btn btn-primary">Tạo hóa đơn</button>
    <a href="{{ route('invoices.index', $property) }}" class="btn btn-secondary">Hủy</a>
</form>

<script>
    (() => {
        const contract = document.getElementById('contract_id');
        const amounts = ['electricity', 'water', 'service_fee', 'other_fee'];
        const formatter = new Intl.NumberFormat('vi-VN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const cents = (value) => Math.round((Number(value) || 0) * 100);
        const render = () => {
            const option = contract.options[contract.selectedIndex];
            const rent = option ? cents(option.dataset.rent) : 0;
            document.getElementById('rent-preview').textContent = `${formatter.format(rent / 100)} VNĐ`;
            const total = rent + amounts.reduce((sum, field) => sum + cents(document.getElementById(field).value), 0);
            document.getElementById('total-preview').textContent = `${formatter.format(total / 100)} VNĐ`;
        };
        contract.addEventListener('change', render);
        amounts.forEach((field) => document.getElementById(field).addEventListener('input', render));
        render();
    })();
</script>
@endsection
