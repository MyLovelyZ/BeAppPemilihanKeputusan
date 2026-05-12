<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::with('quiz:id,title')->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $questions,
            'message' => 'Daftar pertanyaan berhasil diambil'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'question_text' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*.option_text' => 'required|string',
            'options.*.is_correct' => 'required|boolean',
        ]);

        $question = Question::create([
            'quiz_id' => $request->quiz_id,
            'question_text' => $request->question_text,
        ]);

        foreach ($request->options as $option) {
            $question->options()->create([
                'option_text' => $option['option_text'],
                'is_correct' => $option['is_correct'],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $question->load('options'),
            'message' => 'Pertanyaan berhasil dibuat'
        ], 201);
    }

    public function show($id)
    {
        $question = Question::with('options')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $question,
            'message' => 'Detail pertanyaan berhasil diambil'
        ]);
    }

    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        $request->validate([
            'question_text' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*.id' => 'sometimes|exists:options,id',
            'options.*.option_text' => 'required|string',
            'options.*.is_correct' => 'required|boolean',
        ]);

        $question->update([
            'question_text' => $request->question_text,
        ]);

        // Update atau buat opsi baru
        foreach ($request->options as $option) {
            if (isset($option['id'])) {
                // Update opsi yang sudah ada
                $question->options()->where('id', $option['id'])->update([
                    'option_text' => $option['option_text'],
                    'is_correct' => $option['is_correct'],
                ]);
            } else {
                // Buat opsi baru
                $question->options()->create([
                    'option_text' => $option['option_text'],
                    'is_correct' => $option['is_correct'],
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $question->load('options'),
            'message' => 'Pertanyaan berhasil diperbarui'
        ]);
    }

    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $question->options()->delete(); // Hapus opsi terkait
        $question->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pertanyaan berhasil dihapus'
        ]);
    }
}
