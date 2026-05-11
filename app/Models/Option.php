<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    /**
     * Kolom yang boleh diisi secara massal.
     * - question_id : FK ke tabel questions
     * - option_text : teks pilihan jawaban
     * - point       : nilai/poin untuk pilihan ini (digunakan untuk menghitung total skor)
     */
    protected $fillable = [
        'question_id',
        'option_text',
        'point',
    ];

    /**
     * Relasi ke Question (banyak pilihan dimiliki oleh satu pertanyaan).
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
