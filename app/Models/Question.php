<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    /**
     * Kolom yang boleh diisi secara massal.
     */
    protected $fillable = [
        'quiz_id',
        'question_text',
    ];

    /**
     * Relasi ke Quiz (banyak pertanyaan dimiliki oleh satu quiz).
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Relasi ke Option (satu pertanyaan punya banyak pilihan jawaban).
     */
    public function options()
    {
        return $this->hasMany(Option::class);
    }
}
