<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    /**
     * Kolom yang boleh diisi secara massal.
     * user_id wajib ada agar relasi ke User berfungsi saat create().
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
    ];

    /**
     * Relasi ke User (pembuat quiz).
     * Menggunakan foreign key 'user_id' sesuai kolom di tabel quizzes.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Question (satu quiz punya banyak pertanyaan).
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Relasi ke QuizGrade (satu quiz punya banyak rentang nilai/grade).
     */
    public function grades()
    {
        return $this->hasMany(QuizGrade::class);
    }
}