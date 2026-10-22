@extends('layouts.app')

@section('title', 'Chi tiết hợp đồng')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <a href="{{ route('contracts.index', $property) }}" class="text-decoration-none">Hợp đồng</a>
        <h1 class="h3 fw-bold mt-2 mb-0">Chi tiết hợp đồng #{{ $contract->id }}</h1>
    </div>
    <a href="{{ route('contracts.edit', ['property' => $property, 'contract' => $contract]) }}" class="btn btn-outline-primary">
        Chỉnh sửa
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h2 class="h5 mb-3">Thông tin hợp đồng</h2>
        <dl class="row mb-0">
            <dt class="col-sm-4">Khu trọ</dt>
            <dd class="col-sm-8">{{ $property->name }}</dd>

            <dt class="col-sm-4">Phòng</dt>
            <dd class="col-sm-8">{{ $contract->room->room_number ?? 'Không có dữ liệu' }}</dd>

            <dt class="col-sm-4">Người thuê</dt>
            <dd class="col-sm-8">{{ $contract->tenant->full_name ?? 'Không có dữ liệu' }}</dd>

            <dt class="col-sm-4">Ngày bắt đầu</dt>
            <dd class="col-sm-8">{{ $contract->start_date?->format('d/m/Y') }}</dd>

            <dt class="col-sm-4">Ngày kết thúc</dt>
            <dd class="col-sm-8">{{ $contract->end_date?->format('d/m/Y') ?? 'Chưa xác định' }}</dd>

            <dt class="col-sm-4">Tiền thuê hàng tháng</dt>
            <dd class="col-sm-8">{{ number_format((float) $contract->monthly_rent) }} VNĐ</dd>

            <dt class="col-sm-4">Tiền đặt cọc</dt>
            <dd class="col-sm-8">{{ number_format((float) $contract->deposit) }} VNĐ</dd>

            <dt class="col-sm-4">Trạng thái</dt>
            <dd class="col-sm-8">{{ $contract->status }}</dd>

            @if($contract->notes)
                <dt class="col-sm-4">Ghi chú</dt>
                <dd class="col-sm-8">{{ $contract->notes }}</dd>
            @endif
        </dl>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h2 class="h5 mb-3">Hóa đơn của hợp đồng</h2>
        @if($contract->invoices->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tháng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contract->invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->month }}</td>
                                <td>{{ number_format((float) $invoice->total) }} VNĐ</td>
                                <td>{{ $invoice->status }}</td>
                                <td class="text-end">
                                    <a href="{{ route('invoices.show', ['property' => $property, 'invoice' => $invoice]) }}" class="btn btn-sm btn-outline-primary">
                                        Xem hóa đơn
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted mb-0">Hợp đồng chưa có hóa đơn.</p>
        @endif
    </div>
</div>
@endsection
