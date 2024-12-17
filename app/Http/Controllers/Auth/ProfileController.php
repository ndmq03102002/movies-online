<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\UserProfile;

class ProfileController extends Controller
{
    public function edit()
    {
        $profile = UserProfile::where('user_id', Auth::user()->id)->first();
        return view('auth.profile', compact('profile'));
    }

    public function update(Request $request)
{
    // Lấy thông tin hồ sơ của người dùng hiện tại
    $userProfile = UserProfile::where('user_id', Auth::user()->id)->first();

   
    // Cập nhật hoặc tạo mới hồ sơ người dùng
    UserProfile::updateOrCreate(
        ['user_id' => Auth::user()->id],
        [
            'name' => $request->name,
            'dateofbirth' => $request->dateofbirth,
            'sex' => $request->sex,
            'address' => $request->address,
            'avatar' => $request->avatar
        ]
    );

    return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
}


}
