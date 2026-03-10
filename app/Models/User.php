<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; 

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'honda_id',
        'ahm_id',       // Tambahan Baru
        'trainer_id',   // Tambahan Baru
        'custom_id',    // Tambahan Baru (MD ID)
        'name',
        'email',
        'phone_number', 
        'position_id',  // Legacy: Masih disimpan jika diperlukan
        'user_type',    // Enum UserType
        'main_dealer_id',
        'dealer_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'user_type' => UserType::class, // Auto convert string ke Enum
        ];
    }

    protected static function booted()
    {
        // 1. Trigger saat user BARU dibuat
        static::created(function ($user) {
            if ($user->honda_id) \App\Models\HondaIdVerification::where('honda_id', $user->honda_id)->update(['has_account' => true]);
            if ($user->ahm_id) \App\Models\AhmIdVerification::where('ahm_id', $user->ahm_id)->update(['has_account' => true]);
            if ($user->trainer_id) \App\Models\TrainerIdVerification::where('trainer_id', $user->trainer_id)->update(['has_account' => true]);
            if ($user->custom_id) \App\Models\CustomIdVerification::where('custom_id', $user->custom_id)->update(['has_account' => true]);
        });

        // 2. Trigger saat user DIHAPUS (Kembalikan status ID agar bisa dipakai lagi)
        static::deleted(function ($user) {
            if ($user->honda_id) \App\Models\HondaIdVerification::where('honda_id', $user->honda_id)->update(['has_account' => false]);
            if ($user->ahm_id) \App\Models\AhmIdVerification::where('ahm_id', $user->ahm_id)->update(['has_account' => false]);
            if ($user->trainer_id) \App\Models\TrainerIdVerification::where('trainer_id', $user->trainer_id)->update(['has_account' => false]);
            if ($user->custom_id) \App\Models\CustomIdVerification::where('custom_id', $user->custom_id)->update(['has_account' => false]);
        });

        // 3. Trigger saat user DIEDIT (Sinkronisasi jika ID diubah oleh Admin)
        static::updated(function ($user) {
            // Cek & Update Honda ID
            if ($user->isDirty('honda_id')) {
                $original = $user->getOriginal('honda_id');
                if ($original) \App\Models\HondaIdVerification::where('honda_id', $original)->update(['has_account' => false]);
                if ($user->honda_id) \App\Models\HondaIdVerification::where('honda_id', $user->honda_id)->update(['has_account' => true]);
            }
            // Cek & Update AHM ID
            if ($user->isDirty('ahm_id')) {
                $original = $user->getOriginal('ahm_id');
                if ($original) \App\Models\AhmIdVerification::where('ahm_id', $original)->update(['has_account' => false]);
                if ($user->ahm_id) \App\Models\AhmIdVerification::where('ahm_id', $user->ahm_id)->update(['has_account' => true]);
            }
            // Cek & Update Trainer ID
            if ($user->isDirty('trainer_id')) {
                $original = $user->getOriginal('trainer_id');
                if ($original) \App\Models\TrainerIdVerification::where('trainer_id', $original)->update(['has_account' => false]);
                if ($user->trainer_id) \App\Models\TrainerIdVerification::where('trainer_id', $user->trainer_id)->update(['has_account' => true]);
            }
            // Cek & Update MD ID (Custom ID)
            if ($user->isDirty('custom_id')) {
                $original = $user->getOriginal('custom_id');
                if ($original) \App\Models\CustomIdVerification::where('custom_id', $original)->update(['has_account' => false]);
                if ($user->custom_id) \App\Models\CustomIdVerification::where('custom_id', $user->custom_id)->update(['has_account' => true]);
            }
        });
    }

    // --- Relasi Organisasi (Main Dealer, Dealer, Jabatan) ---

    // 1. Relasi ke Main Dealer (WAJIB ADA untuk Filament)
    public function mainDealer()
    {
        return $this->belongsTo(MainDealer::class, 'main_dealer_id');
    }

    // 2. Relasi ke Dealer
    public function dealer()
    {
        return $this->belongsTo(Dealer::class, 'dealer_id');
    }

    // 3. Relasi Jabatan (Many-to-Many) - Digunakan untuk Multiple Select
    public function positions()
    {
        return $this->belongsToMany(Position::class, 'position_user');
    }

    // 4. Relasi Jabatan (Single) - Legacy/Backup jika masih ada data lama
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    // --- Relasi LMS (Learning Management System) ---

    // 1. Course yang diikuti User (RAPOR UMUM)
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_user')
                    ->withPivot('progress_percent', 'status', 'completed_at')
                    ->withTimestamps();
    }

    // 2. Lesson yang sudah selesai (DETAIL ABSENSI)
    public function completedLessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_user')
                    ->wherePivotNotNull('completed_at') 
                    ->withPivot(['course_id',  'started_at', 'last_accessed_at',  'completed_at']) 
                    ->withTimestamps();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Hanya Super Admin dan Middle Admin yang boleh masuk
        return $this->hasRole(['super_admin', 'middle_admin']);
    }
}