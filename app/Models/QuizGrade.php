<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizGrade extends Model
{
    /**
     * Kolom yang boleh diisi secara massal.
     * - quiz_id   : FK ke tabel quizzes
     * - label     : nama/kategori grade, contoh: "Sangat Baik", "Cukup", "Kurang"
     * - min_point : batas bawah rentang nilai (inklusif)
     * - max_point : batas atas rentang nilai (inklusif)
     */
    protected $fillable = [
        'quiz_id',
        'label',
        'min_point',
        'max_point',
    ];

    /**
     * Relasi ke Quiz (banyak grade dimiliki oleh satu quiz).
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
