<div class="row g-3">

    <div class="col-md-6">
        <label class="form-label">
            Tên khu trọ <span class="text-danger">*</span>
        </label>

        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $property?->name) }}" required maxlength="255">

        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">
            Số điện thoại
        </label>

        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
            value="{{ old('phone', $property?->phone) }}" maxlength="20">

        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">
            Địa chỉ <span class="text-danger">*</span>
        </label>

        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
            value="{{ old('address', $property?->address) }}" required maxlength="255">

        @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Trường Người quản lý (được chèn sau Địa chỉ) --}}
    <div class="col-12">
        <label for="manager_id" class="form-label">
            <i class="bi bi-person-badge me-1"></i>
            Người quản lý
        </label>

        <select name="manager_id" id="manager_id"
                class="form-select @error('manager_id') is-invalid @enderror">

            <option value="">-- Chưa phân công quản lý --</option>

            @foreach($managers as $manager)
                <option value="{{ $manager->id }}"
                    @selected(old('manager_id', $currentManager?->id) == $manager->id)>
                    {{ $manager->name }} ({{ $manager->username }})
                </option>
            @endforeach
        </select>

        @error('manager_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <div class="form-text">
            Có thể để trống và phân công Manager sau.
            Chỉ hiển thị tài khoản Manager chưa được phân công.
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label">
            Trạng thái <span class="text-danger">*</span>
        </label>

        <select name="status" class="form-select @error('status') is-invalid @enderror" required>

            <option value="active" @selected(old('status', $property?->status ?? 'active') === 'active')>
                Hoạt động
            </option>

            <option value="inactive" @selected(old('status', $property?->status ?? 'active') === 'inactive')>
                Ngừng hoạt động
            </option>

        </select>

        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Mô tả</label>

        <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror"
            placeholder="Thông tin bổ sung về khu trọ...">{{ old('description', $property?->description) }}</textarea>

        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

</div>