@extends('layouts.app')

@section('title', 'Chi tiết hóa đơn')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <a href="{{ route('invoices.index', $property) }}" class="text-decoration-none">Hóa đơn</a>
        <h1 class="h3 fw-bold mt-2 mb-0">Chi tiết hóa đơn #{{ $invoice->id }}</h1>
    </div>
    <a href="{{ route('invoices.edit', ['property' => $property, 'invoice' => $invoice]) }}" class="btn btn-outline-primary">Chỉnh sửa</a>
</div>

@if ($invoice->rent_amount === null)
    <div class="alert alert-warning" role="alert">Hóa đơn lịch sử chưa có snapshot tiền thuê. Tổng cũ được giữ nguyên và cần đối soát.</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h2 class="h5 mb-3">Thông tin hóa đơn</h2>
        <dl class="row mb-0">
            <dt class="col-sm-4">Khu trọ</dt><dd class="col-sm-8">{{ $property->name }}</dd>
            <dt class="col-sm-4">Phòng</dt><dd class="col-sm-8">{{ $invoice->contract?->room?->room_number ?? 'Không có dữ liệu' }}</dd>
            <dt class="col-sm-4">Người thuê</dt><dd class="col-sm-8">{{ $invoice->contract?->tenant?->full_name ?? 'Không có dữ liệu' }}</dd>
            <dt class="col-sm-4">Hợp đồng</dt>
            <dd class="col-sm-8">
                @if ($invoice->contract)
                    <a href="{{ route('contracts.show', ['property' => $property, 'contract' => $invoice->contract]) }}">Hợp đồng #{{ $invoice->contract->id }}</a>
                @else
                    Không có dữ liệu
                @endif
            </dd>
            <dt class="col-sm-4">Tháng</dt><dd class="col-sm-8">{{ $invoice->month }}</dd>
            <dt class="col-sm-4">Tiền thuê snapshot</dt><dd class="col-sm-8">{{ $invoice->rent_amount === null ? 'Chưa xác định — cần đối soát' : number_format((float) $invoice->rent_amount, 2, ',', '.').' VNĐ' }}</dd>
            <dt class="col-sm-4">Tiền điện</dt><dd class="col-sm-8">{{ number_format((float) $invoice->electricity, 2, ',', '.') }} VNĐ</dd>
            <dt class="col-sm-4">Tiền nước</dt><dd class="col-sm-8">{{ number_format((float) $invoice->water, 2, ',', '.') }} VNĐ</dd>
            <dt class="col-sm-4">Phí dịch vụ</dt><dd class="col-sm-8">{{ number_format((float) $invoice->service_fee, 2, ',', '.') }} VNĐ</dd>
            <dt class="col-sm-4">Phí khác</dt><dd class="col-sm-8">{{ number_format((float) $invoice->other_fee, 2, ',', '.') }} VNĐ</dd>
            <dt class="col-sm-4">Tổng tiền đã lưu</dt><dd class="col-sm-8 fw-bold">{{ number_format((float) $invoice->total, 2, ',', '.') }} VNĐ</dd>
            <dt class="col-sm-4">Trạng thái</dt><dd class="col-sm-8">{{ $invoice->status }}</dd>
            @if ($invoice->due_date)
                <dt class="col-sm-4">Hạn thanh toán</dt><dd class="col-sm-8">{{ $invoice->due_date->format('d/m/Y') }}</dd>
            @endif
            @if ($invoice->paid_at)
                <dt class="col-sm-4">Thời điểm thanh toán</dt><dd class="col-sm-8">{{ $invoice->paid_at->format('d/m/Y H:i') }}</dd>
            @endif
            @if ($invoice->notes)
                <dt class="col-sm-4">Ghi chú</dt><dd class="col-sm-8">{{ $invoice->notes }}</dd>
            @endif
        </dl>
    </div>
</div>
@endsection
