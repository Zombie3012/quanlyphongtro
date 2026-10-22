<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('property')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $properties = Property::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('users.create', compact('properties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                'unique:users,username',
            ],
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'nullable',
                'email',
                'max:150',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'role' => ['required', 'in:admin,manager'],
            'property_id' => [
                'nullable',
                'required_if:role,manager',
                'integer',
                'exists:properties,id',
            ],
        ]);

        if ($validated['role'] === 'admin') {
            $propertyId = null;
        } else {
            $property = Property::where('id', $validated['property_id'])
                ->where('status', 'active')
                ->first();

            if (! $property) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'property_id' => 'Vui lòng chọn khu trọ đang hoạt động.',
                    ]);
            }

            $propertyId = $property->id;
        }

        User::create([
            'username' => $validated['username'],
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'property_id' => $propertyId,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Tạo tài khoản thành công!');
    }

    public function edit(User $user)
    {
        $properties = Property::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('users.edit', compact('user', 'properties'));
    }

    public function update(Request $request, User $user)
    {
        $isManager = $user->role === 'manager';

        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'nullable',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'property_id' => [
                'nullable',
                Rule::requiredIf($isManager),
                'integer',
                Rule::when(
                    $isManager,
                    [Rule::exists('properties', 'id')->where('status', 'active')],
                ),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->username = $validated['username'];
        $user->name = $validated['name'];
        $user->email = $validated['email'] ?? null;
        $user->property_id = $isManager ? $validated['property_id'] : null;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('users.index')
            ->with('success', 'Cập nhật tài khoản thành công!');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with(
                'error',
                'Bạn không thể tự xóa tài khoản đang đăng nhập.'
            );
        }

        if ($user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->count();

            if ($adminCount <= 1) {
                return back()->with(
                    'error',
                    'Không thể xóa Admin cuối cùng của hệ thống.'
                );
            }
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Đã xóa tài khoản thành công.');
    }
}
