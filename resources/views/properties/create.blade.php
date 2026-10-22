@extends('layouts.app')

@section('title', 'Thêm khu trọ')

@section('content')
<div class="container" style="max-width: 850px">

    <div class="mb-4">
        <a href="{{ route('properties.index') }}"
           class="text-decoration-none text-secondary">
            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
        </a>

        <h3 class="fw-bold mt-3">Thêm khu trọ mới</h3>
        <p class="text-secondary">
            Nhập thông tin khu nhà trọ cần quản lý.
        </p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <form method="POST" action="{{ route('properties.store') }}">
                @csrf

                @include('properties.form', ['property' => null])

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('properties.index') }}"
                       class="btn btn-light border">
                        Hủy
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        Lưu khu trọ
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
