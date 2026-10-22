@extends('layouts.app')

@section('title', 'Chi tiết người thuê')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Chi tiết người thuê</h2>

    <a href="{{ route('tenants.index') }}" class="btn btn-secondary">
        Quay lại
    </a>

</div>

<div class="card">

    <div class="card-header bg-primary text-white">
        Thông tin người thuê
    </div>

    <div class="card-body">

        <div class="row mb-3">

            <div class="col-md-6">

                <strong>ID:</strong>

                {{ $tenant->id }}

            </div>

            <div class="col-md-6">

                <strong>Họ tên:</strong>

                {{ $tenant->full_name }}

            </div>

        </div>

        <div class="row mb-3">

            <div class="col-md-6">

                <strong>Số điện thoại:</strong>

                {{ $tenant->phone }}

            </div>

            <div class="col-md-6">

                <strong>CCCD:</strong>

                {{ $tenant->cccd }}

            </div>

        </div>

        <div class="mb-3">

            <strong>Địa chỉ:</strong>

            <p class="mt-2">
                {{ $tenant->address }}
            </p>

        </div>

        <div class="mt-4">

            <a
                href="{{ route('tenants.edit', $tenant->id) }}"
                class="btn btn-warning"
            >
                Sửa người thuê
            </a>

            <a
                href="{{ route('tenants.index') }}"
                class="btn btn-secondary"
            >
                Danh sách người thuê
            </a>

        </div>

    </div>

</div>

@endsection
