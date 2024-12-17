<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\PasswordReset;
use App\Mail\ForgotPassword;
use Illuminate\Support\Facades\Cookie;
class ForgotPasswordController extends Controller
{
    public function showForgotPasswordForm()
    {
        // $checkToken = PasswordReset::where('email', 'nguyendinhmanhquynh@gmail.com')->first();
        // $expirationTime = now()->subMinutes(1);
        // if ($checkToken && $checkToken->created_at < $expirationTime) {
        //         echo 'dung';
        //     }
        return view('auth.forgot-password');
    }

    public function checkForgotPassword(Request $request)
{
    $request->validate(['email' => 'required|email|exists:users,email']);


    // Tìm user và lưu OTP
    $user = User::where('email', $request->email)->firstOrFail();

    $rand = Str::random(40);

    $token = $rand."email=".$request->email;
    $checkToken = PasswordReset::where('email', $request->email)->first();

    // nếu có token mà chưa hết hạn thì thông báo vui lòng check lại email
        $expirationTime = now()->subMinutes(5); // Thời gian 1 phút trước
        if ($checkToken && $checkToken->created_at > $expirationTime) {
            // Chuyển hướng về trang yêu cầu reset password với thông báo
            return redirect()->back()->with('error', 'Vui lòng check email trước.');
        }
        else{
            PasswordReset::where('email', $request->email)->delete();
        }

    $tokenData = [
        'email' => $request->email,
        'token' => Hash::make($token)
    ];
    if(PasswordReset::create($tokenData)){
        Mail::to($request->email)->send(new ForgotPassword($user,$token));
        return redirect()->back()->with('success', 'Chúng tôi đã gửi email hãy xác nhận.');
    }
    

    return redirect()->back()->with('error', 'Lỗi! Vui lòng thử lại.');
           
    }
    public function showResetPasswordForm($token)
    {
        $parts = explode('email=', $token, 2);
        $email = $parts[1];
        $tokenData = PasswordReset::where('email', $email)->firstOrFail();
        $expirationTime = now()->subMinutes(5); // Thời gian 5 phút trước
        if ($tokenData && $tokenData->created_at < $expirationTime) {
            // Xóa token đã hết hạn
            PasswordReset::where('email', $email)->delete();
            
            // Chuyển hướng về trang yêu cầu reset password với thông báo
            return redirect()->route('password.request')->with('error', 'Token đã hết hạn, vui lòng yêu cầu lại.');
        }
        
        return view('auth.reset-password', compact('token','email'));
    }

    public function resetPassword(Request $request , $token)
    {
        $request->validate([
            'password' => 'required|confirmed',
            'password_confirmation' => 'required|same:password'
        ]);
        // Lấy token từ cơ sở dữ liệu
        $tokenData = PasswordReset::where('email', $request->email)->firstOrFail();

        // So sánh token với token trong DB
        if (Hash::check($token, $tokenData->token)) {

        $user = User::where('email', $tokenData->email)->firstOrFail();
        $data = [
            
            'password' => Hash::make($request->password)
        ];
         $user->update($data);

        // Xóa token sau khi sử dụng
            PasswordReset::where('email', $request->email)->delete();
            $cookie = Cookie::forget('user_token');

            return redirect()->route("auth.login")->with('success', 'Bạn có thể đăng nhập ngay.')->withCookie($cookie);
        
        }
        return redirect()->back()->with('error', 'Lỗi! Vui lòng thử lại.');

    }


}