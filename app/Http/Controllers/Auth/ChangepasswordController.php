<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;
class ChangepasswordController extends Controller
{
   
    public function edit()
    {
        return view('auth.change-password');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|confirmed',
            'new_password_confirmation' => 'required|same:new_password' 
        ],
            [
                
                'new_password.confirmed' => 'Mật khẩu không khớp',
                
            ]);
        
    
        if ($validator->fails()) {
            return redirect()->route(route: 'change.password')
                             ->withErrors($validator)
                             ->withInput();
        }
    
        $id = Auth::user()->id;
        $user = User::find($id);
        if (!Hash::check($request->input('current_password'), $user->password)) {
            return redirect()->route('change.password')
                             ->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.'])
                             ->withInput();
        }
    
        $user->password = Hash::make($request->input('new_password'));
        $user->save();
        $cookie = Cookie::forget('user_token');
        return redirect()->route('homepage')->with('status', 'Mật khẩu đã được cập nhật thành công.')->withCookie($cookie);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
