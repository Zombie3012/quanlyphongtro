@extends('layouts.app')

@section('content')
    <div class="container py-4">


        <div class="mb-4">
            <h2 class="fw-bold mb-1">
                {{ $property->name }}
            </h2>

            @if($property->address)
                <p class="text-muted mb-0">
                    <i class="bi bi-geo-alt me-1"></i>
                    {{ $property->address }}
                </p>
            @endif
        </div>

        <div class="row g-4 mb-4">

            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-2">
                            Tổng phòng
                        </div>

                        <h3 class="fw-bold mb-0">
                            {{ $totalRooms }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-2">
                            Phòng trống
                        </div>

                        <h3 class="fw-bold text-success mb-0">
                            {{ $emptyRooms }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-2">
                            Đang thuê
                        </div>

                        <h3 class="fw-bold text-primary mb-0">
                            {{ $rentedRooms }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-2">
                            Người thuê
                        </div>

                        <h3 class="fw-bold mb-0">
                            {{ $totalTenants }}
                        </h3>
                    </div>
                </div>
            </div>

        </div>

        <div class="row g-4">

            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-2">
                            Doanh thu đã thanh toán
                        </div>

                        <h3 class="fw-bold text-success mb-0">
                            {{ number_format($revenue, 0, ',', '.') }} đ
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-2">
                            Hóa đơn chưa thanh toán
                        </div>

                        <h3 class="fw-bold text-danger mb-0">
                            {{ $unpaidInvoices }}
                        </h3>
                    </div>
                </div>
            </div>

        </div>

        {{-- Chỉ Admin mới hiển thị các nút quản lý nghiệp vụ --}}
        @if(auth()->user()->role === 'admin')
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-gear me-2 text-primary"></i>
                        Quản lý khu trọ
                    </h5>

                    <p class="text-muted small mb-3">
                        Chọn chức năng cần quản lý cho khu trọ
                        {{ $property->name }}.
                    </p>

                    <div class="d-flex flex-wrap gap-2">

                        <a href="{{ route('rooms.index', $property) }}" class="btn btn-outline-primary">
                            <i class="bi bi-door-open me-1"></i>
                            Quản lý phòng
                        </a>

                        <a href="{{ route('tenants.index', $property) }}" class="btn btn-outline-success">
                            <i class="bi bi-people me-1"></i>
                            Quản lý người thuê
                        </a>

                        <a href="{{ route('contracts.index', $property) }}" class="btn btn-outline-info">
                            <i class="bi bi-file-earmark-text me-1"></i>
                            Quản lý hợp đồng
                        </a>

                        <a href="{{ route('invoices.index', $property) }}" class="btn btn-outline-warning">
                            <i class="bi bi-receipt me-1"></i>
                            Quản lý hóa đơn
                        </a>

                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection