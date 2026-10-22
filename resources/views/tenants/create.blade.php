@extends('layouts.app')

@section('title', 'Thêm người thuê')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Thêm người thuê</h2>

    <a href="{{ route('tenants.index', $property) }}" class="btn btn-secondary">
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

<form action="{{ route('tenants.store', $property) }}" method="POST">

    @csrf

    <div class="mb-3">

        <label class="form-label">
            Họ tên
        </label>

        <input
            type="text"
            name="full_name"
            class="form-control"
            value="{{ old('full_name') }}"
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
            value="{{ old('phone') }}"
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
            value="{{ old('cccd') }}"
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
            required>{{ old('address') }}</textarea>

    </div>

    <button type="submit" class="btn btn-primary">
        Thêm người thuê
    </button>

    <a
        href="{{ route('tenants.index', $property) }}"
        class="btn btn-secondary">
        Hủy
    </a>

</form>

@endsection