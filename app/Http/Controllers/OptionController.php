<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OptionController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => [],
            'message' => 'Daftar opsi berhasil diambil'
        ]);
    }
}
