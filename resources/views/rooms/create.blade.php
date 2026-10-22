@extends('layouts.app')

@section('title', 'Thêm phòng')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Thêm phòng</h2>

    <a href="{{ route('rooms.index', $property) }}" class="btn btn-secondary">
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

<form action="{{ route('rooms.store', $property) }}" method="POST">

    @csrf

    <div class="mb-3">

        <label class="form-label">
            Số phòng
        </label>

        <input
            type="text"
            name="room_number"
            class="form-control"
            value="{{ old('room_number') }}"
            required
        >

    </div>

    <div class="mb-3">

        <label class="form-label">
            Giá phòng
        </label>

        <input
            type="number"
            name="price"
            class="form-control"
            value="{{ old('price') }}"
            min="0"
            required
        >

    </div>

    <div class="mb-3">

        <label class="form-label">
            Diện tích (m²)
        </label>

        <input
            type="number"
            name="area"
            class="form-control"
            value="{{ old('area') }}"
            min="0"
            step="0.01"
            required
        >

    </div>

    <div class="mb-3">

        <label class="form-label">
            Trạng thái
        </label>

        <select name="status" class="form-select" required>

            <option value="Trống">
                Trống
            </option>

            <option value="Đang thuê">
                Đang thuê
            </option>

            <option value="Đang sửa">
                Đang sửa
            </option>

        </select>

    </div>

    <button type="submit" class="btn btn-primary">
        Thêm phòng
    </button>

    <a
        href="{{ route('rooms.index', $property) }}"

        class="btn btn-secondary"
    >
        Hủy
    </a>

</form>

@endsection
