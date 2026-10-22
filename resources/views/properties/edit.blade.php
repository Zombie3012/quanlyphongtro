@extends('layouts.app')

@section('title', 'Sửa khu trọ')

@section('content')
<div class="container" style="max-width: 850px">

    <div class="mb-4">
        <a href="{{ route('properties.index') }}"
           class="text-decoration-none text-secondary">
            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
        </a>

        <h3 class="fw-bold mt-3">Chỉnh sửa khu trọ</h3>
        <p class="text-secondary">
            Cập nhật thông tin khu trọ.
        </p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <form method="POST" action="{{ route('properties.update', $property->id) }}">
                @csrf
                @method('PUT')

                {{-- Gọi lại form dùng chung, truyền vào $property và danh sách managers --}}
                @include('properties.form', [
                    'property' => $property,
                    'managers' => $managers ?? [],
                    'currentManager' => $currentManager ?? null
                ])

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('properties.index') }}"
                       class="btn btn-light border">
                        Hủy
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Lưu thay đổi
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection