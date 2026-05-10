<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            $quizzes = Quiz::with('author:id, name, email')->latest()->get();

            return response()->json([
                'status' => 'success',
                'data' => $quizzes,
                'message' => 'Daftar semua quiz berhasil diambil (superadmin)'
            ]);
        } else {
            $quizzes = Quiz::where('author_id', $user->id)->latest()->get();

            return response()->json([
                'status' => 'success',
                'data' => $quizzes,
                'message' => 'Daftar quiz Anda berhasil diambil'
            ]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $quiz = Quiz::create([
            'title' => $request->title,
            'description' => $request->description,
            'author_id' => Auth::id(),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $quiz,
            'message' => 'Quiz berhasil dibuat'
        ], 201);
    }

    public function show($id)
    {
        $quiz = Quiz::with('author:id, name, email')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $quiz
        ]);
    }

    public function update(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);

        if ($quiz->author_id !== Auth::id() && !Auth::user()->isSuperAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki izin untuk mengedit quiz ini'
            ], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $quiz->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $quiz,
            'message' => 'Quiz berhasil diperbarui'
        ]);
    }

    public function destroy($id)
    {
        // Logic untuk menghapus quiz berdasarkan ID
    }
}
