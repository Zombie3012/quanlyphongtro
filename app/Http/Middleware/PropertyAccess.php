<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PropertyAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $property = $request->route('property');

        // Chưa đăng nhập
        if (!$user) {
            return redirect()->route('login');
        }

        // Admin được quyền truy cập tất cả khu
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Manager chỉ được truy cập khu được phân công
        if (
            $user->role === 'manager' &&
            $property &&
            (int) $user->property_id === (int) $property->id
        ) {
            return $next($request);
        }

        abort(403, 'Bạn không có quyền truy cập khu nhà trọ này.');
    }
}
