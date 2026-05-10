<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password'  => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message'   => 'Login successful', 
                'role'    => $user->role == User::ROLE_SUPERADMIN ? 'superadmin' : 'admin',
                'token' => $token,
                'token_type' => 'Bearer'
            ]);
        }

        return response()->json([
            'message' => 'Email atau Password salah'
        ], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'message' => 'Logout berhasil beb'
        ]);
    }
}
