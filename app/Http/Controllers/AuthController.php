<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials)) {
            return back()
                ->withErrors([
                    'username' => 'Tên đăng nhập hoặc mật khẩu không đúng.',
                ])
                ->withInput($request->only('username'));
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Admin → Dashboard tổng
        if ($user->role === 'admin') {
            return redirect()->route('dashboard');
        }

        // Manager → Dashboard của property được phân công
        if ($user->role === 'manager') {
            if (!$user->property_id) {
                Auth::logout();

                return redirect()->route('login')
                    ->withErrors([
                        'username' => 'Tài khoản manager chưa được phân công khu nhà trọ.',
                    ]);
            }

            return redirect()->route('properties.dashboard', [
                'property' => $user->property_id,
            ]);
        }

        // Role không hợp lệ
        Auth::logout();

        return redirect()->route('login')
            ->withErrors([
                'username' => 'Tài khoản không có quyền truy cập hệ thống.',
            ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
