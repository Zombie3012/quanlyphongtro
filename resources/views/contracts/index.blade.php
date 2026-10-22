@extends('layouts.app')

@section('title', 'Quản lý hợp đồng')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="fw-bold text-dark mb-0">
            <i class="bi bi-file-earmark-text-fill text-primary me-2"></i>Danh sách hợp đồng
        </h2>
        <p class="text-muted small mb-0">Quản lý các hợp đồng thuê phòng, thời hạn và tiền đặt cọc</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <a href="{{ route('contracts.create', $property) }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-file-earmark-plus-fill me-1"></i> Tạo hợp đồng mới
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
        <form action="{{ route('contracts.index', $property) }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input 
                        type="text" 
                        name="search" 
                        class="form-control border-start-0 ps-0" 
                        placeholder="Tìm theo số phòng hoặc tên người thuê..." 
                        value="{{ request('search') }}"
                    >
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <button type="submit" class="btn btn-primary px-4 me-1">
                    Tìm kiếm
                </button>
                @if(request('search'))
                    <a href="{{ route('contracts.index', $property) }}" class="btn btn-outline-secondary">
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
                        <th class="py-3">Phòng</th>
                        <th class="py-3">Người thuê</th>
                        <th class="py-3">Ngày bắt đầu</th>
                        <th class="py-3">Ngày kết thúc</th>
                        <th class="py-3">Tiền cọc</th>
                        <th class="py-3 text-end pe-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contracts as $contract)
                        <tr>
                            <td class="ps-3 fw-semibold text-muted">#{{ $contract->id }}</td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-bold">
                                    <i class="bi bi-door-open me-1"></i>{{ $contract->room->room_number ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-dark">
                                    <i class="bi bi-person me-1 text-muted"></i>{{ $contract->tenant->full_name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    <i class="bi bi-calendar-event me-1"></i>{{ $contract->start_date }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    <i class="bi bi-calendar-check me-1"></i>{{ $contract->end_date }}
                                </span>
                            </td>
                            <td>
                                <span class="text-success fw-semibold">{{ number_format($contract->deposit) }} VNĐ</span>
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('contracts.show', [
                                        'property' => $property,
                                        'contract' => $contract
                                    ]) }}" class="btn btn-outline-info btn-sm" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('contracts.edit', [
                                        'property' => $property,
                                        'contract' => $contract
                                    ]) }}" class="btn btn-outline-warning btn-sm" title="Sửa">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('contracts.destroy', [
                                        'property' => $property,
                                        'contract' => $contract
                                    ]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-end" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa hợp đồng này?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-file-earmark-text display-6 d-block mb-2 text-secondary"></i>
                                Chưa có hợp đồng nào được tạo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 px-4 py-3">
        {{ $contracts->links() }}
    </div>
</div>
@endsection
