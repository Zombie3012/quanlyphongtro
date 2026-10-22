
@extends('layouts.app')

@section('title', 'Tạo hợp đồng')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Tạo hợp đồng</h2>
            <p class="text-secondary mb-0">
                Khu trọ: {{ $property->name }}
            </p>
        </div>

        <a href="{{ route('contracts.index', $property) }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
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

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <form action="{{ route('contracts.store', $property) }}"
                  method="POST">
                @csrf

                <div class="row g-3">

                    <div class="col-md-6">
                        <label for="room_id" class="form-label">
                            Phòng <span class="text-danger">*</span>
                        </label>

                        <select name="room_id" id="room_id"
                                class="form-select" required>
                            <option value="">-- Chọn phòng --</option>

                            @foreach ($rooms as $room)
                                <option
                                    value="{{ $room->id }}"
                                    data-price="{{ $room->price }}"
                                    {{ old('room_id') == $room->id ? 'selected' : '' }}
                                >
                                    Phòng {{ $room->room_number }}
                                    - {{ number_format($room->price) }} VNĐ/tháng
                                </option>
                            @endforeach
                        </select>

                        @if ($rooms->isEmpty())
                            <div class="form-text text-danger">
                                Hiện không có phòng trống để tạo hợp đồng.
                            </div>
                        @endif

                        @error('room_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="tenant_id" class="form-label">
                            Người thuê <span class="text-danger">*</span>
                        </label>

                        <select name="tenant_id" id="tenant_id"
                                class="form-select" required>
                            <option value="">-- Chọn người thuê --</option>

                            @foreach ($tenants as $tenant)
                                <option
                                    value="{{ $tenant->id }}"
                                    {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}
                                >
                                    {{ $tenant->full_name }} - {{ $tenant->phone }}
                                </option>
                            @endforeach
                        </select>

                        @error('tenant_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="monthly_rent" class="form-label">
                            Tiền thuê hàng tháng (VNĐ)
                            <span class="text-danger">*</span>
                        </label>

                        <input type="number"
                               name="monthly_rent"
                               id="monthly_rent"
                               class="form-control"
                               value="{{ old('monthly_rent') }}"
                               min="0"
                               step="0.01"
                               required>

                        @error('monthly_rent')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            Tự điền theo giá phòng khi chọn phòng; bạn vẫn có thể điều chỉnh.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="deposit" class="form-label">
                            Tiền cọc (VNĐ)
                            <span class="text-danger">*</span>
                        </label>

                        <input type="number"
                               name="deposit"
                               id="deposit"
                               class="form-control"
                               value="{{ old('deposit', 0) }}"
                               min="0"
                               step="0.01"
                               required>

                        @error('deposit')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="start_date" class="form-label">
                            Ngày bắt đầu <span class="text-danger">*</span>
                        </label>

                        <input type="date"
                               name="start_date"
                               id="start_date"
                               class="form-control"
                               value="{{ old('start_date') }}"
                               required>

                        @error('start_date')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="end_date" class="form-label">
                            Ngày kết thúc
                        </label>

                        <input type="date"
                               name="end_date"
                               id="end_date"
                               class="form-control"
                               value="{{ old('end_date') }}">

                        <div class="form-text">
                            Để trống nếu chưa xác định ngày kết thúc.
                        </div>

                        @error('end_date')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label">
                            Trạng thái hợp đồng
                        </label>

                        <select name="status" id="status" class="form-select">
                            @foreach ([
                                'Nháp',
                                'Đang hiệu lực',
                                'Đã kết thúc',
                                'Đã hủy'
                            ] as $status)
                                <option value="{{ $status }}"
                                    {{ old('status', 'Đang hiệu lực') === $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>

                        @error('status')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label">Ghi chú</label>

                        <textarea name="notes"
                                  id="notes"
                                  class="form-control"
                                  rows="3">{{ old('notes') }}</textarea>

                        @error('notes')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <hr class="my-4">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>
                        Tạo hợp đồng
                    </button>

                    <a href="{{ route('contracts.index', $property) }}"
                       class="btn btn-light border">
                        Hủy
                    </a>
                </div>
            </form>

        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const roomSelect = document.querySelector('[name="room_id"]');
    const rentInput = document.querySelector('[name="monthly_rent"]');

    if (!roomSelect || !rentInput) {
        return;
    }

    function updateRent() {
        const selectedOption =
            roomSelect.options[roomSelect.selectedIndex];

        if (
            selectedOption &&
            selectedOption.dataset.price !== undefined &&
            selectedOption.value !== ''
        ) {
            rentInput.value = selectedOption.dataset.price;
        }
    }

    roomSelect.addEventListener('change', updateRent);

    // Tự điền khi mở form hoặc khi dữ liệu phòng được giữ lại.
    if (!rentInput.value) {
        updateRent();
    }
});
</script>
@endsection