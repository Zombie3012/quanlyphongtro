@extends('layouts.app')

@section('title', 'Sửa người thuê')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Sửa người thuê</h2>

    <a href="{{ route('tenants.index', $property) }}" class="btn btn-secondary">
        Quay lại
    </a>

</div>

@if ($errors->any())

<div class="alert alert-danger">

    <ul class="mb-0">

        @foreach ($errors->all() as $error)

        <li>{{ $error }}</li>

        @endforeach

    </ul>

</div>

@endif

<form action="{{ route('tenants.update', [
    'property' => $property,
    'tenant' => $tenant,
]) }}" method="POST">

    @csrf
    @method('PUT')

    <div class="mb-3">

        <label class="form-label">
            Họ tên
        </label>

        <input
            type="text"
            name="full_name"
            class="form-control"
            value="{{ old('full_name', $tenant->full_name) }}"
            required>

    </div>

    <div class="mb-3">

        <label class="form-label">
            Số điện thoại
        </label>

        <input
            type="text"
            name="phone"
            class="form-control"
            value="{{ old('phone', $tenant->phone) }}"
            required>

    </div>

    <div class="mb-3">

        <label class="form-label">
            CCCD
        </label>

        <input
            type="text"
            name="cccd"
            class="form-control"
            value="{{ old('cccd', $tenant->cccd) }}"
            required>

    </div>

    <div class="mb-3">

        <label class="form-label">
            Địa chỉ
        </label>

        <textarea
            name="address"
            class="form-control"
            rows="3"
            required>{{ old('address', $tenant->address) }}</textarea>

    </div>

    <button type="submit" class="btn btn-primary">
        Cập nhật
    </button>

    <a
        href="{{ route('tenants.index', $property) }}"
        class="btn btn-secondary">
        Hủy
    </a>

</form>

@endsection