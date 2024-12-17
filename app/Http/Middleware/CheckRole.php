<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next , $role): Response
    {
        // Kiểm tra xem người dùng đã đăng nhập và có vai trò chính xác chưa
        if (Auth::check() && Auth::user()->role === $role) {
            return $next($request);
        }

        // Nếu người dùng không có quyền truy cập, chuyển hướng về trang home hoặc trang lỗi
        return redirect()->route('homepage')->with('error', 'Bạn không có quyền truy cập vào trang này.');
    }
}
