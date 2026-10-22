@extends('layouts.app')

@section('title', 'Quản lý khu trọ')

@section('content')
    <div class="container-fluid px-0">

        <div class="d-flex flex-wrap justify-content-between
                                    align-items-center gap-3 mb-4">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-buildings text-primary me-2"></i>
                    Quản lý khu trọ
                </h3>
                <p class="text-secondary mb-0">
                    Danh sách các khu nhà trọ trong hệ thống
                </p>
            </div>

            <a href="{{ route('properties.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Thêm khu trọ
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-warning alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('properties.index') }}" class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Tìm kiếm khu trọ</label>
                        <input type="text" name="keyword" class="form-control" value="{{ request('keyword') }}"
                            placeholder="Tên khu, thành phố, quận, địa chỉ...">
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary me-2" type="submit">
                            <i class="bi bi-search me-1"></i> Tìm kiếm
                        </button>

                        <a href="{{ route('properties.index') }}" class="btn btn-light border">
                            Làm mới
                        </a>
                    </div>

                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3">Tên khu trọ</th>
                            <th>Địa chỉ</th>
                            <th>Người quản lý</th>
                            <th>Điện thoại</th>
                            <th>Trạng thái</th>
                            <th class="text-center pe-4">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($properties as $property)
                            <tr>
                                {{-- Tên khu trọ --}}
                                <td class="ps-4 fw-semibold">
                                    {{ $property->name }}

                                    <div class="small text-secondary">
                                        Mã khu: #{{ $property->id }}
                                    </div>
                                </td>

                                {{-- Địa chỉ --}}
                                <td>
                                    {{ $property->address ?: '—' }}
                                </td>

                                {{-- Người quản lý --}}
                                <td>
                                    @php
                                        $manager = $property->managers
                                            ->where('role', 'manager')
                                            ->first();
                                    @endphp

                                    @if($manager)
                                        <div class="fw-semibold">
                                            <i class="bi bi-person-badge text-primary me-1"></i>
                                            {{ $manager->name }}
                                        </div>
                                        <div class="small text-secondary">
                                            {{ '@' . $manager->username }}
                                        </div>
                                    @else
                                        <span class="text-secondary small">
                                            <i class="bi bi-person-dash me-1"></i>
                                            Chưa phân công
                                        </span>
                                    @endif
                                </td>

                                {{-- Điện thoại --}}
                                <td>
                                    {{ $property->phone ?: '—' }}
                                </td>

                                {{-- Trạng thái --}}

                                <td>
                                    @if($property->status === 'active')
                                        <span class="badge bg-success-subtle text-success">
                                            Hoạt động
                                        </span>
                                    @elseif($property->status === 'inactive')
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            Ngừng hoạt động
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark">
                                            {{ $property->status ?: 'Chưa xác định' }}
                                        </span>
                                    @endif
                                </td>


                                {{-- Thao tác --}}
                                <td class="text-center pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('properties.edit', $property) }}" class="btn btn-sm btn-outline-primary" title="Sửa">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <a href="{{ route('properties.dashboard', $property->id) }}"
                                            class="btn btn-sm btn-outline-secondary" title="Dashboard">
                                            <i class="bi bi-speedometer2"></i>
                                        </a>

                                        {{-- Nút xóa sử dụng form và phương thức DELETE --}}
                                        <form action="{{ route('properties.destroy', $property->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa khu trọ này không?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-buildings fs-1 text-secondary"></i>

                                    <p class="fw-semibold mt-3 mb-1">
                                        Chưa có khu trọ
                                    </p>

                                    <p class="text-secondary small mb-0">
                                        Thử thay đổi từ khóa tìm kiếm.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-0 px-4 py-3">
                {{ $properties->links() }}
            </div>
        </div>
    </div>
@endsection