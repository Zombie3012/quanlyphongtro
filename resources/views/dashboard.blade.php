@extends('layouts.app')

@section('title', 'Trang chủ Admin')

@section('content')

    <div class="container-fluid px-0">

        {{-- Header --}}
        <div class="d-flex flex-wrap justify-content-between
                    align-items-center gap-3 mb-4">

            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-grid-1x2-fill text-primary me-2"></i>
                    Trang chủ Admin
                </h3>

                <p class="text-secondary mb-0">
                    Tổng quan hoạt động quản lý hệ thống nhà trọ
                </p>
            </div>

        </div>

        {{-- Statistics --}}
        <div class="row g-3 mb-4">

            {{-- Tổng khu trọ --}}
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between
                                    align-items-center">

                            <div>
                                <p class="text-secondary mb-2">
                                    Tổng khu trọ
                                </p>

                                <h2 class="fw-bold mb-0">
                                    {{ number_format($totalProperties ?? 0) }}
                                </h2>
                            </div>

                            <div class="rounded-3 bg-primary bg-opacity-10
                                        text-primary p-3">
                                <i class="bi bi-buildings fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tổng phòng --}}
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between
                                    align-items-center">

                            <div>
                                <p class="text-secondary mb-2">
                                    Tổng số phòng
                                </p>

                                <h2 class="fw-bold mb-0">
                                    {{ number_format($totalRooms ?? 0) }}
                                </h2>
                            </div>

                            <div class="rounded-3 bg-info bg-opacity-10
                                        text-info p-3">
                                <i class="bi bi-door-closed fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Phòng đang thuê --}}
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between
                                    align-items-center">

                            <div>
                                <p class="text-secondary mb-2">
                                    Phòng đang thuê
                                </p>

                                <h2 class="fw-bold mb-0">
                                    {{ number_format($rentedRooms ?? 0) }}
                                </h2>
                            </div>

                            <div class="rounded-3 bg-success bg-opacity-10
                                        text-success p-3">
                                <i class="bi bi-house-check fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Phòng trống --}}
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between
                                    align-items-center">

                            <div>
                                <p class="text-secondary mb-2">
                                    Phòng trống
                                </p>

                                <h2 class="fw-bold mb-0">
                                    {{ number_format($emptyRooms ?? 0) }}
                                </h2>
                            </div>

                            <div class="rounded-3 bg-warning bg-opacity-10
                                        text-warning p-3">
                                <i class="bi bi-door-open fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Người thuê --}}
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between
                                    align-items-center">

                            <div>
                                <p class="text-secondary mb-2">
                                    Tổng người thuê
                                </p>

                                <h2 class="fw-bold mb-0">
                                    {{ number_format($totalTenants ?? 0) }}
                                </h2>
                            </div>

                            <div class="rounded-3 bg-secondary bg-opacity-10
                                        text-secondary p-3">
                                <i class="bi bi-people fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Doanh thu --}}
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between
                                    align-items-center">

                            <div>
                                <p class="text-secondary mb-2">
                                    Tổng doanh thu
                                </p>

                                <h4 class="fw-bold text-success mb-0">
                                    {{ number_format($revenue ?? 0) }}
                                    <small class="fs-6">VNĐ</small>
                                </h4>
                            </div>

                            <div class="rounded-3 bg-success bg-opacity-10
                                        text-success p-3">
                                <i class="bi bi-cash-stack fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Unpaid invoices notice --}}
        <div class="alert alert-warning border-0 shadow-sm
                    d-flex align-items-center gap-3 mb-4">

            <i class="bi bi-exclamation-circle-fill fs-4"></i>

            <div>
                <div class="fw-semibold">
                    Hóa đơn chưa thanh toán
                </div>

                <div>
                    Hiện có
                    <strong>{{ number_format($unpaidInvoices ?? 0) }}</strong>
                    hóa đơn cần theo dõi trên toàn hệ thống.
                </div>
            </div>

        </div>

        {{-- Property list --}}
        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white border-0 px-4 pt-4 pb-3">

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                    <div>
                        <h5 class="fw-bold mb-1">
                            <i class="bi bi-buildings text-primary me-2"></i>
                            Danh sách khu trọ
                        </h5>

                        <p class="text-secondary small mb-0">
                            Theo dõi và truy cập khu trọ cần quản lý
                        </p>
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-2">

                        <span class="badge rounded-pill bg-primary bg-opacity-10
                                   text-primary px-3 py-2">
                            {{ $properties->count() }} khu trọ
                        </span>

                    </div>

                </div>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 py-3">Khu nhà trọ</th>
                                <th>Địa chỉ</th>
                                <th>Thông tin khác</th>
                                <th class="text-center">Tổng phòng</th>
                                <th class="text-center">Đang thuê</th>
                                <th class="text-end pe-4">Doanh thu</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($properties as $property)

                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">

                                            <div class="rounded-3 bg-primary
                                                        bg-opacity-10 text-primary
                                                        p-2">
                                                <i class="bi bi-house-door fs-5"></i>
                                            </div>

                                            <div>
                                                <div class="fw-semibold">
                                                    {{ $property->name }}
                                                </div>

                                                <small class="text-secondary">
                                                    Mã khu: #{{ $property->id }}
                                                </small>
                                            </div>

                                        </div>
                                    </td>

                                    <td>
                                        {{ $property->address ?: '—' }}
                                    </td>

                                    <td>—</td>

                                    <td class="text-center">
                                        {{ number_format($property->rooms_count ?? 0) }}
                                    </td>

                                    <td class="text-center">
                                        <span class="badge bg-success bg-opacity-10
                                                   text-success">
                                            {{ number_format($property->rented_rooms_count ?? 0) }}
                                        </span>
                                    </td>

                                    <td class="text-end pe-4 text-nowrap">
                                        <span class="fw-semibold">
                                            {{ number_format($property->revenue_total ?? 0) }}
                                        </span>
                                        <small class="text-secondary">đ</small>
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bi bi-buildings fs-1
                                                  text-secondary"></i>

                                        <p class="fw-semibold mt-3 mb-1">
                                            Chưa có khu nhà trọ
                                        </p>

                                        <p class="text-secondary small mb-0">
                                            Danh sách khu trọ sẽ hiển thị tại đây
                                            khi có dữ liệu.
                                        </p>
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

            @if(method_exists($properties, 'links'))
                <div class="card-footer bg-white border-0 px-4 py-3">
                    {{ $properties->links() }}
                </div>
            @endif

        </div>

    </div>

@endsection