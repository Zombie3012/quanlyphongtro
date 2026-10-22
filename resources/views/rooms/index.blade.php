@extends('layouts.app')

@section('title', 'Quản lý phòng trọ')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="fw-bold text-dark mb-0">
            <i class="bi bi-door-open-fill text-primary me-2"></i>Danh sách phòng
        </h2>
        <p class="text-muted small mb-0">Quản lý thông tin, giá cả và trạng thái các phòng trọ</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <a href="{{ route('rooms.create', $property) }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Thêm phòng mới
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<form action="{{ route('rooms.index', $property) }}" method="GET" class="card shadow-sm border-0 mb-4">
    <div class="card-body row g-2 align-items-center">
        <div class="col-md-8">
            <label class="visually-hidden" for="room-search">Tìm số phòng</label>
            <input id="room-search" type="search" name="search" class="form-control"
                placeholder="Tìm theo số phòng..." value="{{ request('search') }}">
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-outline-primary">Tìm kiếm</button>
            @if(request('search'))
                <a href="{{ route('rooms.index', $property) }}" class="btn btn-outline-secondary">Xóa lọc</a>
            @endif
        </div>
    </div>
</form>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fs-7 text-secondary">
                    <tr>
                        <th class="py-3 ps-3">#ID</th>
                        <th class="py-3">Số phòng</th>
                        <th class="py-3">Giá phòng</th>
                        <th class="py-3">Diện tích</th>
                        <th class="py-3">Trạng thái</th>
                        <th class="py-3 text-end pe-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $room)
                    <tr>
                        <td class="ps-3 fw-semibold text-muted">#{{ $room->id }}</td>
                        <td>
                            <span class="fw-bold text-dark fs-6">{{ $room->room_number }}</span>
                        </td>
                        <td>
                            <span class="text-success fw-semibold">{{ number_format($room->price) }} VNĐ</span>
                        </td>
                        <td>
                            <span class="text-muted">{{ $room->area }} m²</span>
                        </td>
                        <td>
                            @if($room->status == 'Trống')
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                <i class="bi bi-circle-fill small me-1"></i> Trống
                            </span>
                            @elseif($room->status == 'Đang thuê')
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                <i class="bi bi-circle-fill small me-1"></i> Đang thuê
                            </span>
                            @elseif($room->status == 'Đang sửa')
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                <i class="bi bi-circle-fill small me-1"></i> Đang sửa
                            </span>
                            @else
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                {{ $room->status }}
                            </span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group" role="group">
                                <a href="{{ route('rooms.edit', [
                                    'property' => $property,
                                    'room' => $room,
                                ]) }}" class="btn btn-outline-warning btn-sm" title="Sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('rooms.destroy', [
    'property' => $property,
    'room' => $room,
]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-end" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa phòng này?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox display-6 d-block mb-2 text-secondary"></i>
                            Chưa có phòng nào được tạo.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 px-4 py-3">
        {{ $rooms->links() }}
    </div>
</div>
@endsection
