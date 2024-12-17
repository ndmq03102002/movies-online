<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
class CheckUserCookie
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra cookie
        if ($request->hasCookie('user_token')) {
            $userId = $request->cookie('user_token');
            $user = User::find($userId); // Thay đổi với model người dùng của bạn

            if ($user) {
                Auth::login($user);
            }
        }
        return $next($request);
    }
}
