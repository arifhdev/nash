<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'image',
        'description',
        'category_id',
        'start_date',
        'curriculum_count', // Biarkan di fillable tidak masalah, walau form-nya sudah kita hapus
        'is_active',
        'has_certificate',
    ];

    protected $casts = [
        'start_date' => 'date',
        'is_active' => 'boolean',
        'has_certificate' => 'boolean',
    ];

    /**
     * Relasi ke Kategori Kursus
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke Module (Sekarang Many-to-Many)
     * Menggunakan tabel pivot 'course_module'
     * Diurutkan berdasarkan kolom 'sort_order' yang ada di tabel pivot
     */
    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class)
                    ->withPivot('sort_order') // Agar kita bisa akses/edit urutan
                    ->withTimestamps()        // Agar created_at/updated_at di pivot terisi
                    ->orderByPivot('sort_order'); // Urutkan modul berdasarkan settingan di pivot
    }

    /**
     * Relasi ke User (untuk mengambil data enroll dan progress)
     * Ini yang dibutuhkan oleh Livewire MandatoryCourses
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_user')
                    ->withPivot('progress_percent', 'status', 'completed_at', 'last_accessed_at')
                    ->withTimestamps();
    }

    /**
     * Alias untuk users() jika ada kode lain yang masih memanggil students()
     */
    public function students(): BelongsToMany
    {
        return $this->users();
    }

    /**
     * Relasi ke Jabatan (Untuk fitur Assign Jabatan Wajib)
     */
    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'course_position');
    }
}