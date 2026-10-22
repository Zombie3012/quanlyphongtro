@extends('layouts.app')

@section('title', 'Quản lý người thuê')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="fw-bold text-dark mb-0">
            <i class="bi bi-people-fill text-primary me-2"></i>Danh sách người thuê
        </h2>
        <p class="text-muted small mb-0">Quản lý thông tin cá nhân và liên lạc của khách thuê trọ</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <a href="{{ route('tenants.create', $property) }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Thêm người thuê
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Form tìm kiếm cải tiến -->
<div class="card shadow-sm border-0 mb-4 bg-light">
    <div class="card-body">
        <form action="{{ route('tenants.index', $property) }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input 
                        type="text" 
                        name="search" 
                        class="form-control border-start-0 ps-0" 
                        placeholder="Tìm theo họ tên, số điện thoại hoặc số CCCD..." 
                        value="{{ request('search') }}"
                    >
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <button type="submit" class="btn btn-primary px-4 me-1">
                    Tìm kiếm
                </button>
                @if(request('search'))
                    <a href="{{ route('tenants.index', $property) }}" class="btn btn-outline-secondary">
                        Xóa lọc
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Bảng dữ liệu -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fs-7 text-secondary">
                    <tr>
                        <th class="py-3 ps-3">#ID</th>
                        <th class="py-3">Họ tên</th>
                        <th class="py-3">Số điện thoại</th>
                        <th class="py-3">CCCD</th>
                        <th class="py-3">Địa chỉ</th>
                        <th class="py-3 text-end pe-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tenants as $tenant)
                        <tr>
                            <td class="ps-3 fw-semibold text-muted">#{{ $tenant->id }}</td>
                            <td>
                                <span class="fw-bold text-dark">{{ $tenant->full_name }}</span>
                            </td>
                            <td>
                                <a href="tel:{{ $tenant->phone }}" class="text-decoration-none text-primary fw-medium">
                                    <i class="bi bi-telephone-inbound me-1 small"></i>{{ $tenant->phone }}
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <i class="bi bi-card-text me-1 text-muted"></i>{{ $tenant->cccd }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted text-truncate d-inline-block" style="max-width: 200px;" title="{{ $tenant->address }}">
                                    {{ $tenant->address ?: 'Chưa cập nhật' }}
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('tenants.edit', [
    'property' => $property,
    'tenant' => $tenant,
]) }}" class="btn btn-outline-warning btn-sm" title="Sửa">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('tenants.destroy', [
    'property' => $property,
    'tenant' => $tenant,
]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-end" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa người thuê này?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-people display-6 d-block mb-2 text-secondary"></i>
                                Chưa có thông tin người thuê nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 px-4 py-3">
        {{ $tenants->links() }}
    </div>
</div>
@endsection
