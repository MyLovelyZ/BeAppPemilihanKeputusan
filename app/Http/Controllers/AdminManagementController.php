<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminManagementController extends Controller
{
    public function index()
    {
        $admins = User::where('role', User::ROLE_ADMIN)->get();
        return response()->json($admins);
    }



    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($request->role == User::ROLE_SUPERADMIN) {
            if (User::where('role', User::ROLE_SUPERADMIN)->exists()) {
                return response()->json([
                    'message' => 'Sudah ada superadmin yang terdaftar'
                ], 400);
            }
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_ADMIN,
        ]);

        return response()->json([
            'message' => 'Admin berhasil ditambahkan'
        ], 201);
    }

    public function destroy($id)
    {
        $admin = User::findorFail($id);
        if ($admin->role == User::ROLE_SUPERADMIN) {
            return response()->json([
                'message' => 'Tidak dapat menghapus superadmin'
            ], 400);
        }
        $admin->delete();
        return response()->json([
            'message' => 'Admin berhasil dihapus'
        ]);
    }
}
