# 📘 Dokumentasi Database & Model — BeSistemQuizPemilihan

> Dokumen ini mencatat seluruh struktur tabel, relasi antar tabel, definisi model Eloquent,
> serta semua perbaikan (*bug fixes*) yang dilakukan pada migrasi dan model.

---

## 📐 Diagram Relasi Antar Tabel (ERD)

```
users
 └──< quizzes          (users.id = quizzes.user_id)
        ├──< questions  (quizzes.id = questions.quiz_id)
        │      └──< options  (questions.id = options.question_id)
        └──< quiz_grades (quizzes.id = quiz_grades.quiz_id)
```

| Relasi | Tipe | Keterangan |
|---|---|---|
| `users` → `quizzes` | One-to-Many | Satu user/admin dapat membuat banyak quiz |
| `quizzes` → `questions` | One-to-Many | Satu quiz memiliki banyak pertanyaan |
| `questions` → `options` | One-to-Many | Satu pertanyaan memiliki banyak pilihan jawaban |
| `quizzes` → `quiz_grades` | One-to-Many | Satu quiz memiliki banyak rentang nilai/grade |

---

## 🗄️ Struktur Tabel & Migrasi

### 1. `users`
**File migrasi:** `0001_01_01_000000_create_users_table.php`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint PK | Primary key |
| `name` | string | Nama pengguna |
| `email` | string UNIQUE | Email pengguna |
| `email_verified_at` | timestamp nullable | Waktu verifikasi email |
| `password` | string | Password (di-hash) |
| `role` | tinyInteger default 0 | `0` = Admin, `1` = Superadmin |
| `remember_token` | string | Token "ingat saya" |
| `created_at` / `updated_at` | timestamps | Laravel timestamps |

---

### 2. `quizzes`
**File migrasi:** `2026_05_09_015911_create_quizzes_table.php`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint PK | Primary key |
| `user_id` | bigint FK | Referensi ke `users.id`, cascade delete |
| `title` | string | Judul quiz |
| `description` | text nullable | Deskripsi quiz |
| `created_at` / `updated_at` | timestamps | Laravel timestamps |

**Foreign Key:** `user_id` → `users.id` (ON DELETE CASCADE)

---

### 3. `questions`
**File migrasi:** `2026_05_09_024201_create_questions_table.php`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint PK | Primary key |
| `quiz_id` | bigint FK | Referensi ke `quizzes.id`, cascade delete |
| `question_text` | string | Teks pertanyaan |
| `created_at` / `updated_at` | timestamps | Laravel timestamps |

**Foreign Key:** `quiz_id` → `quizzes.id` (ON DELETE CASCADE)

---

### 4. `options`
**File migrasi:** `2026_05_09_025948_create_options_table.php`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint PK | Primary key |
| `question_id` | bigint FK | Referensi ke `questions.id`, cascade delete |
| `option_text` | string | Teks pilihan jawaban |
| `point` | integer default 0 | Poin/nilai untuk pilihan ini |
| `created_at` / `updated_at` | timestamps | Laravel timestamps |

**Foreign Key:** `question_id` → `questions.id` (ON DELETE CASCADE)

---

### 5. `quiz_grades`
**File migrasi:** `2026_05_09_023601_create_quiz_grades_table.php`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint PK | Primary key |
| `quiz_id` | bigint FK | Referensi ke `quizzes.id`, cascade delete |
| `label` | string | Nama/kategori grade, contoh: `"Sangat Baik"`, `"Cukup"` |
| `min_point` | integer | Batas nilai minimum (inklusif) |
| `max_point` | integer | Batas nilai maksimum (inklusif) |
| `created_at` / `updated_at` | timestamps | Laravel timestamps |

**Foreign Key:** `quiz_id` → `quizzes.id` (ON DELETE CASCADE)

---

## 🧩 Model Eloquent & Relasi

### `User` — `app/Models/User.php`

```php
// Relasi
public function quizzes() // hasMany(Quiz::class, 'user_id')
```

| Method | Tipe Relasi | Target | Keterangan |
|---|---|---|---|
| `quizzes()` | `hasMany` | `Quiz` | Semua quiz yang dibuat oleh user ini |

---

### `Quiz` — `app/Models/Quiz.php`

```php
protected $fillable = ['user_id', 'title', 'description'];

public function author()    // belongsTo(User::class, 'user_id')
public function questions() // hasMany(Question::class)
public function grades()    // hasMany(QuizGrade::class)
```

| Method | Tipe Relasi | Target | Keterangan |
|---|---|---|---|
| `author()` | `belongsTo` | `User` | User yang membuat quiz ini |
| `questions()` | `hasMany` | `Question` | Semua pertanyaan dalam quiz ini |
| `grades()` | `hasMany` | `QuizGrade` | Semua rentang nilai/grade dalam quiz ini |

---

### `Question` — `app/Models/Question.php`

```php
protected $fillable = ['quiz_id', 'question_text'];

public function quiz()    // belongsTo(Quiz::class)
public function options() // hasMany(Option::class)
```

| Method | Tipe Relasi | Target | Keterangan |
|---|---|---|---|
| `quiz()` | `belongsTo` | `Quiz` | Quiz yang memiliki pertanyaan ini |
| `options()` | `hasMany` | `Option` | Semua pilihan jawaban dari pertanyaan ini |

---

### `Option` — `app/Models/Option.php`

```php
protected $fillable = ['question_id', 'option_text', 'point'];

public function question() // belongsTo(Question::class)
```

| Method | Tipe Relasi | Target | Keterangan |
|---|---|---|---|
| `question()` | `belongsTo` | `Question` | Pertanyaan yang memiliki pilihan ini |

---

### `QuizGrade` — `app/Models/QuizGrade.php`

```php
protected $fillable = ['quiz_id', 'label', 'min_point', 'max_point'];

public function quiz() // belongsTo(Quiz::class)
```

| Method | Tipe Relasi | Target | Keterangan |
|---|---|---|---|
| `quiz()` | `belongsTo` | `Quiz` | Quiz yang memiliki grade ini |

---

## 🔧 Daftar Perbaikan (*Bug Fixes* & *Improvements*)

### 1. ✅ Migrasi `quiz_grades` — Kolom Tidak Lengkap
**File:** `database/migrations/2026_05_09_023601_create_quiz_grades_table.php`

**Masalah:**
- Hanya ada kolom `min_point`. Sebuah rentang nilai membutuhkan batas atas (`max_point`) dan nama grade (`label`).

**Perbaikan:**
```diff
- $table->integer('min_point');
+ $table->string('label');         // Nama/kategori grade, contoh: "Sangat Baik"
+ $table->integer('min_point');    // Batas nilai minimum (inklusif)
+ $table->integer('max_point');    // Batas nilai maksimum (inklusif)
```

---

### 2. ✅ Model `Quiz` — `user_id` Tidak Ada di `$fillable` & Relasi `grades()` Hilang
**File:** `app/Models/Quiz.php`

**Masalah:**
- `user_id` tidak ada di `$fillable`, sehingga `Quiz::create(['user_id' => ...])` akan diabaikan oleh mass assignment protection.
- Tidak ada relasi `grades()` ke `QuizGrade`.

**Perbaikan:**
```diff
  protected $fillable = [
+     'user_id',
      'title',
      'description',
  ];

+ public function grades()
+ {
+     return $this->hasMany(QuizGrade::class);
+ }
```

---

### 3. ✅ Model `Question` — Relasi `options()` Tidak Ada
**File:** `app/Models/Question.php`

**Masalah:**
- Model tidak memiliki relasi ke `Option`, sehingga tidak bisa melakukan `$question->options`.

**Perbaikan:**
```diff
+ public function options()
+ {
+     return $this->hasMany(Option::class);
+ }
```

---

### 4. ✅ Model `Option` — Kosong Sama Sekali
**File:** `app/Models/Option.php`

**Masalah:**
- Model tidak memiliki `$fillable` maupun relasi apapun.

**Perbaikan:** Tambahkan `$fillable` dan relasi `question()`:
```php
protected $fillable = ['question_id', 'option_text', 'point'];

public function question()
{
    return $this->belongsTo(Question::class);
}
```

---

### 5. ✅ Model `QuizGrade` — Kosong Sama Sekali
**File:** `app/Models/QuizGrade.php`

**Masalah:**
- Model tidak memiliki `$fillable` maupun relasi apapun.

**Perbaikan:** Tambahkan `$fillable` dan relasi `quiz()`:
```php
protected $fillable = ['quiz_id', 'label', 'min_point', 'max_point'];

public function quiz()
{
    return $this->belongsTo(Quiz::class);
}
```

---

### 6. ✅ `QuizController` — Penggunaan Kolom `author_id` yang Tidak Exist
**File:** `app/Http/Controllers/QuizController.php`

**Masalah:**
- Seluruh controller menggunakan `author_id` padahal kolom di tabel `quizzes` adalah `user_id` (sesuai migrasi). Ini menyebabkan kegagalan saat membuat quiz, filter quiz, dan pengecekan izin edit/hapus.
- `with('author:id, name, email')` memiliki spasi setelah koma — format Laravel untuk column selector di eager loading tidak boleh ada spasi.

**Perbaikan:**

| Lokasi | Sebelum | Sesudah |
|---|---|---|
| `index()` | `where('author_id', ...)` | `where('user_id', ...)` |
| `store()` | `'author_id' => Auth::id()` | `'user_id' => Auth::id()` |
| `show()` | `'author:id, name, email'` | `'author:id,name,email'` |
| `index()` | `'author:id, name, email'` | `'author:id,name,email'` |
| `update()` | `$quiz->author_id !== Auth::id()` | `$quiz->user_id !== Auth::id()` |
| `destroy()` | `$quiz->author_id !== Auth::id()` | `$quiz->user_id !== Auth::id()` |

---

## 🚀 Cara Menerapkan Perubahan Migrasi

Karena tabel `quiz_grades` sudah pernah di-migrate, jalankan perintah berikut untuk
menerapkan perubahan kolom baru (`label` dan `max_point`):

```bash
# PILIHAN 1 — Reset semua tabel (development only, data akan hilang)
php artisan migrate:fresh

# PILIHAN 2 — Rollback tabel terdampak saja lalu migrate ulang
php artisan migrate:rollback --step=3
php artisan migrate
```

> ⚠️ **Peringatan:** `migrate:fresh` akan menghapus seluruh data. Gunakan hanya di lingkungan *development*.

---

## 💡 Contoh Penggunaan Relasi (Eager Loading)

```php
// Ambil quiz beserta semua pertanyaan, pilihan jawaban, dan grade-nya
$quiz = Quiz::with([
    'author:id,name,email',
    'questions.options',
    'grades',
])->findOrFail($id);

// Akses data
$quiz->author->name;
$quiz->questions[0]->options;
$quiz->grades; // koleksi QuizGrade (label, min_point, max_point)
```

---

*Dokumentasi ini dibuat pada 2026-05-11 dan mencerminkan kondisi kode terkini setelah semua perbaikan diterapkan.*
