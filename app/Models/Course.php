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
        'curriculum_count', 
        'is_active',
        'has_certificate',
        'require_sequential', // FITUR BARU: Materi Wajib Berurutan
        'points_reward',      // Gamification: Poin
        'xp_reward',          // Tambahan: Pastikan masuk fillable agar form Filament tidak error
    ];

    protected $casts = [
        'start_date' => 'date',
        'is_active' => 'boolean',
        'has_certificate' => 'boolean',
        'require_sequential' => 'boolean', // Pastikan di-cast sebagai boolean
        'points_reward' => 'integer', 
        'xp_reward' => 'integer',     
    ];

    /**
     * Relasi ke Kategori Kursus
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke Module (Many-to-Many)
     */
    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class)
                    ->withPivot('sort_order')
                    ->withTimestamps()
                    ->orderByPivot('sort_order');
    }

    /**
     * Relasi ke User (Enrollment & Progress)
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_user')
                    ->withPivot('progress_percent', 'status', 'completed_at', 'last_accessed_at')
                    ->withTimestamps();
    }

    public function students(): BelongsToMany
    {
        return $this->users();
    }

    // =========================================================================
    // --- RELASI ASSIGN AKSES (Boleh Melihat) ---
    // =========================================================================

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'course_position');
    }

    public function ahmPositions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'course_position')->where('positions.user_type', 'ahm');
    }

    public function mdPositions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'course_position')->where('positions.user_type', 'main_dealer');
    }

    public function dealerPositions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'course_position')->where('positions.user_type', 'dealer');
    }

    // =========================================================================
    // --- RELASI ASSIGN WAJIB (Harus Ikut) ---
    // =========================================================================

    /**
     * Relasi Master Jabatan Wajib
     */
    public function mandatoryPositions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'course_mandatory_position');
    }

    /**
     * Relasi Wajib - Per Tipe User (Untuk UI Filament yang dipisah)
     */
    public function ahmMandatoryPositions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'course_mandatory_position')->where('positions.user_type', 'ahm');
    }

    public function mdMandatoryPositions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'course_mandatory_position')->where('positions.user_type', 'main_dealer');
    }

    public function dealerMandatoryPositions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'course_mandatory_position')->where('positions.user_type', 'dealer');
    }

    // =========================================================================
    // --- RELASI MAIN DEALER ---
    // =========================================================================

    public function mainDealers(): BelongsToMany
    {
        return $this->belongsToMany(MainDealer::class);
    }

    // =========================================================================
    // --- PRASYARAT COURSE (LEARNING PATH) ---
    // =========================================================================

    /**
     * Relasi ke Course lain sebagai prasyarat
     */
    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_prerequisites', 'course_id', 'prerequisite_id');
    }

    /**
     * LOGIC UTAMA: Cek apakah course ini terkunci untuk user tertentu
     * * @param \App\Models\User $user
     * @return bool
     */
    public function isLockedForUser($user): bool
    {
        // 1. Jika tidak ada prasyarat, otomatis terbuka
        if ($this->prerequisites->isEmpty()) {
            return false;
        }

        // 2. Proteksi jika object user null (misal blm login)
        if (!$user) {
            return true;
        }

        // 3. Loop semua course prasyarat
        foreach ($this->prerequisites as $prerequisite) {
            // Jika ada SATU saja course yang belum diselesaikan user, maka course ini Terkunci
            if (! $user->hasCompletedCourse($prerequisite->id)) {
                return true; 
            }
        }

        return false;
    }
}