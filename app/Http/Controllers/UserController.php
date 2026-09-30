<?php

namespace App\Http\Controllers;
use App\Models\User ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            return response()->json([
                'success' => true,
                'message' => 'Welcome to '.config('app.brand_name')
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Invalid email or password'
        ]);
    }
    public function logout(){
        Auth::logout();
        return redirect()->route('auth.loginForm');
    }
}
