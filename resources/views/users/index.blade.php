@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Quản lý tài khoản</h3>
            <p class="text-muted mb-0">
                Quản lý tài khoản và khu trọ được phân công
            </p>
        </div>

        <a href="{{ route('users.create') }}"
           class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i>
            Tạo tài khoản
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Khu trọ</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td class="fw-semibold">
                                {{ $user->username }}
                            </td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email ?? '—' }}</td>

                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge bg-danger">
                                        Admin
                                    </span>
                                @else
                                    <span class="badge bg-primary">
                                        Manager
                                    </span>
                                @endif
                            </td>

                            <td>
                                {{ $user->property?->name ?? 'Toàn hệ thống' }}
                            </td>

                            <td class="text-end">
                                <a href="{{ route('users.edit', $user) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                {{-- Kiểm tra điều kiện hiển thị nút Xóa: Không tự xóa chính mình VÀ (Không phải Admin HOẶC số lượng Admin >= 2) --}}
                                @if(auth()->id() !== $user->id && ($user->role !== 'admin' || \App\Models\User::where('role', 'admin')->count() >= 2))
                                    <form action="{{ route('users.destroy', $user) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Bạn có chắc muốn xóa tài khoản này?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7"
                                class="text-center text-muted py-4">
                                Chưa có tài khoản nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection