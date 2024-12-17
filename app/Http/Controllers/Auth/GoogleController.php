<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class GoogleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
     public function redirectToGoogle()

     {
         return Socialite::driver('google')->redirect();
     }
     /**
      * Create a new controller instance.
      *
      * @return void
      */
      public function handleGoogleCallback()
{
    try {
        // Lấy thông tin người dùng từ Google
        $user = Socialite::driver('google')->user();

        // Kiểm tra nếu đã có người dùng với google_id
        $finduser = User::where('google_id', $user->id)->first();

        if ($finduser) {
            // Đăng nhập nếu đã tồn tại google_id
            Auth::login($finduser);
            return redirect()->intended('/');
        } else {
            // Kiểm tra nếu email đã tồn tại trong hệ thống
            $existingUser = User::where('email', $user->email)->first();

            if ($existingUser) {
                // Cập nhật google_id nếu email đã tồn tại
                $existingUser->update([
                    'google_id' => $user->id,
                ]);

                // Đăng nhập user đã tồn tại
                Auth::login($existingUser);
                return redirect()->intended('/');
            } else {
                // Tạo mật khẩu ngẫu nhiên
                $randomPassword = Str::random(12); // Mật khẩu ngẫu nhiên với độ dài 12 ký tự

                // Tạo user mới nếu email chưa tồn tại
                $newUser = User::create([
                    'username' => $user->name,
                    'email' => $user->email,
                    'google_id'=> $user->id,
                    'password' => bcrypt($randomPassword), // Mã hóa mật khẩu ngẫu nhiên
                    'role' => 'customer'
                ]);

                // Đăng nhập user mới
                Auth::login($newUser);
                return redirect()->intended('/');
            }
        }
    } catch (Exception $e) {
        // Xử lý lỗi và hiển thị thông báo lỗi
        dd($e->getMessage());
    }
}
      
}
