@extends('layouts.app')

@section('title', 'Quản lý hóa đơn')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Danh sách hóa đơn</h1>
        <p class="text-muted mb-0">Tiền thuê, điện, nước, phí dịch vụ và phí khác theo tháng.</p>
    </div>
    <a href="{{ route('invoices.create', $property) }}" class="btn btn-primary">Tạo hóa đơn mới</a>
</div>

@if (session('success'))
    <div class="alert alert-success" role="alert">{{ session('success') }}</div>
@endif

<form action="{{ route('invoices.index', $property) }}" method="GET" class="row g-2 mb-4">
    <div class="col-md-8">
        <input type="search" name="search" class="form-control" placeholder="Tìm theo tháng, phòng, người thuê hoặc trạng thái" value="{{ request('search') }}">
    </div>
    <div class="col-auto"><button class="btn btn-outline-primary" type="submit">Tìm kiếm</button></div>
    @if (request('search'))
        <div class="col-auto"><a class="btn btn-outline-secondary" href="{{ route('invoices.index', $property) }}">Xóa lọc</a></div>
    @endif
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th><th>Phòng</th><th>Người thuê</th><th>Tháng</th>
                    <th>Tiền thuê</th><th>Điện</th><th>Nước</th><th>Dịch vụ</th><th>Phí khác</th>
                    <th>Tổng tiền</th><th>Trạng thái</th><th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoices as $invoice)
                    <tr>
                        <td>#{{ $invoice->id }}</td>
                        <td>{{ $invoice->contract?->room?->room_number ?? 'N/A' }}</td>
                        <td>{{ $invoice->contract?->tenant?->full_name ?? 'N/A' }}</td>
                        <td>{{ $invoice->month }}</td>
                        <td>{{ $invoice->rent_amount === null ? 'Chưa đối soát' : number_format((float) $invoice->rent_amount, 2, ',', '.').' VNĐ' }}</td>
                        <td>{{ number_format((float) $invoice->electricity, 2, ',', '.') }} VNĐ</td>
                        <td>{{ number_format((float) $invoice->water, 2, ',', '.') }} VNĐ</td>
                        <td>{{ number_format((float) $invoice->service_fee, 2, ',', '.') }} VNĐ</td>
                        <td>{{ number_format((float) $invoice->other_fee, 2, ',', '.') }} VNĐ</td>
                        <td class="fw-bold">{{ number_format((float) $invoice->total, 2, ',', '.') }} VNĐ</td>
                        <td>{{ $invoice->status }}</td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-outline-info btn-sm" href="{{ route('invoices.show', ['property' => $property, 'invoice' => $invoice]) }}">Chi tiết</a>
                            <a class="btn btn-outline-warning btn-sm" href="{{ route('invoices.edit', ['property' => $property, 'invoice' => $invoice]) }}">Sửa</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="12" class="text-center py-5 text-muted">Chưa có hóa đơn nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white border-0 px-4 py-3">
        {{ $invoices->links() }}
    </div>
</div>
@endsection
