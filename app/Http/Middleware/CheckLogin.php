<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!Auth::check()){
            return $next($request);
        }
        if(Auth::user()->role === "admin"){
            return redirect()->route('dashboard.index')->with('error','Bạn đã đăng nhập rồi');
        }else{
            return redirect()->route('homepage')->with('error','Bạn đã đăng nhập rồi');
        }
    }
}
