<div>
    <!-- Very little is needed to make a happy life. - Marcus Aurelius -->
</div>
@extends('layouts.app')

@section('title', 'Khu vực quản trị')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Khu vực quản trị</h1>
            <p class="text-secondary mb-0">Quản lý khu trọ và tài khoản trên toàn hệ thống.</p>
        </div>
        <span class="badge text-bg-primary">Quản trị viên</span>
    </div>

    <div class="row g-3 mb-4">
        @foreach ([['Khu trọ', $totalProperties, 'bi-buildings'], ['Tài khoản', $totalUsers, 'bi-people'], ['Phòng', $totalRooms, 'bi-door-open'], ['Người thuê', $totalTenants, 'bi-person-vcard']] as [$label, $count, $icon])
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="rounded-circle bg-primary-subtle text-primary p-3" aria-hidden="true">
                            <i class="bi {{ $icon }} fs-5"></i>
                        </span>
                        <div>
                            <div class="text-secondary small">{{ $label }}</div>
                            <div class="fs-4 fw-bold">{{ number_format($count) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3">
        <div class="col-12 col-md-6">
            <a class="card h-100 border-0 shadow-sm text-decoration-none" href="{{ route('properties.index') }}">
                <div class="card-body">
                    <h2 class="h5 text-dark"><i class="bi bi-buildings me-2 text-primary"></i>Quản lý khu trọ</h2>
                    <p class="text-secondary mb-0">Thêm, tìm kiếm và cập nhật khu trọ.</p>
                </div>
            </a>
        </div>
        <div class="col-12 col-md-6">
            <a class="card h-100 border-0 shadow-sm text-decoration-none" href="{{ route('users.index') }}">
                <div class="card-body">
                    <h2 class="h5 text-dark"><i class="bi bi-people me-2 text-primary"></i>Quản lý tài khoản</h2>
                    <p class="text-secondary mb-0">Phân công manager cho từng khu trọ.</p>
                </div>
            </a>
        </div>
    </div>
@endsection
