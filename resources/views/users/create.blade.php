@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="mb-4">
            <h3 class="fw-bold">Tạo tài khoản Manager</h3>
            <p class="text-muted mb-0">
                Phân công tài khoản quản lý một khu trọ
            </p>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Username *</label>
                        <input type="text" name="username" value="{{ old('username') }}"
                            class="form-control @error('username') is-invalid @enderror" required>

                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Họ và tên *</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror" required>

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror">

                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="mb-3">
                        <label for="role" class="form-label">
                            Loại tài khoản <span class="text-danger">*</span>
                        </label>

                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="manager" @selected(old('role', 'manager') === 'manager')>
                                Manager - Quản lý khu trọ
                            </option>

                            <option value="admin" @selected(old('role') === 'admin')>
                                Admin - Quản trị toàn hệ thống
                            </option>
                        </select>

                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mật khẩu *</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                            required>

                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Xác nhận mật khẩu *</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>


                    <div class="mb-4" id="property-field">
                        <label for="property_id" class="form-label">
                            Khu trọ phụ trách
                        </label>

                        <select name="property_id" id="property_id"
                            class="form-select @error('property_id') is-invalid @enderror">
                            <option value="">-- Chọn khu trọ --</option>

                            @foreach($properties as $property)
                                <option value="{{ $property->id }}" @selected(old('property_id') == $property->id)>
                                    {{ $property->name }} - {{ $property->address }}
                                </option>
                            @endforeach
                        </select>

                        @error('property_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="form-text" id="property-help">
                            Manager chỉ được quản lý khu trọ được phân công.
                        </div>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const role = document.getElementById('role');
                            const property = document.getElementById('property_id');
                            const propertyField = document.getElementById('property-field');
                            const propertyHelp = document.getElementById('property-help');

                            function toggleProperty() {
                                const isManager = role.value === 'manager';

                                propertyField.style.display = isManager ? '' : 'none';
                                property.required = isManager;

                                if (!isManager) {
                                    property.value = '';
                                }

                                propertyHelp.textContent = isManager
                                    ? 'Manager chỉ được quản lý khu trọ được phân công.'
                                    : 'Admin có quyền quản trị toàn bộ hệ thống.';
                            }

                            role.addEventListener('change', toggleProperty);
                            toggleProperty();
                        });
                    </script>


                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>
                            Tạo tài khoản
                        </button>

                        <a href="{{ route('users.index') }}" class="btn btn-light border">
                            Quay lại
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection