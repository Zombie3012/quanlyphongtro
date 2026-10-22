
@extends('layouts.app')

@section('title', 'Chỉnh sửa tài khoản')

@section('content')
<div class="container py-3">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Chỉnh sửa tài khoản</h3>
            <p class="text-secondary mb-0">
                Cập nhật thông tin và khu trọ được phân công
            </p>
        </div>

        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Quay lại
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-2">
                Vui lòng kiểm tra lại thông tin:
            </div>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <form action="{{ route('users.update', $user->id) }}"
                  method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    <div class="col-md-6">
                        <label for="username" class="form-label">
                            Username <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               id="username"
                               name="username"
                               value="{{ old('username', $user->username) }}"
                               class="form-control @error('username') is-invalid @enderror"
                               maxlength="50"
                               required>

                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="name" class="form-label">
                            Họ và tên <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name', $user->name) }}"
                               class="form-control @error('name') is-invalid @enderror"
                               maxlength="100"
                               required>

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>

                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email', $user->email) }}"
                               class="form-control @error('email') is-invalid @enderror"
                               maxlength="150">

                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($user->role === 'manager')
                        <div class="col-md-6">
                            <label for="property_id" class="form-label">Khu trọ phụ trách <span class="text-danger">*</span></label>
                            <select id="property_id" name="property_id" class="form-select @error('property_id') is-invalid @enderror" required>
                                <option value="">-- Chọn khu trọ --</option>
                                @foreach($properties as $property)
                                    <option value="{{ $property->id }}" @selected((string) old('property_id', $user->property_id) === (string) $property->id)>
                                        {{ $property->name }} - {{ $property->address }}
                                    </option>
                                @endforeach
                            </select>
                            @error('property_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @else
                        <div class="col-md-6">
                            <div class="form-label">Khu trọ phụ trách</div>
                            <div class="form-control-plaintext">Tài khoản quản trị áp dụng toàn hệ thống.</div>
                        </div>
                    @endif

                    <div class="col-12">
                        <hr>
                        <h6 class="fw-bold">
                            <i class="bi bi-shield-lock me-1"></i>
                            Đổi mật khẩu
                        </h6>

                        <p class="text-secondary small">
                            Để trống hai ô mật khẩu nếu bạn không muốn đổi.
                        </p>
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label">
                            Mật khẩu mới
                        </label>

                        <input type="password"
                               id="password"
                               name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               minlength="8"
                               autocomplete="new-password">

                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">
                            Xác nhận mật khẩu mới
                        </label>

                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               class="form-control"
                               minlength="8"
                               autocomplete="new-password">
                    </div>

                </div>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>
                        Lưu thay đổi
                    </button>

                    <a href="{{ route('users.index') }}"
                       class="btn btn-light border">
                        Hủy
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
