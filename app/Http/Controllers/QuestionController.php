<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => [],
            'message' => 'Daftar pertanyaan berhasil diambil'
        ]);
    }
}
