<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\MainDealer;
use App\Models\Dealer;
use App\Models\HondaIdVerification;
use App\Models\AhmIdVerification;
use App\Models\CustomIdVerification;
use App\Models\TrainerIdVerification;
use App\Models\Pdp; 
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class Register extends Component
{
    // --- Properties Form ---
    public $name;
    public $email;
    public $phone_number;
    public $password;
    public $password_confirmation;
    public $agreed_pdp = false; // Property untuk checkbox PDP
    
    // --- Properties Verifikasi (Tiga Jalur Utama) ---
    public $user_type = ''; 
    public $role_in_md = ''; 
    public $honda_id;
    public $ahm_id;
    public $trainer_id; 
    public $custom_id;

    // --- Properties Khusus Penempatan ---
    public $main_dealer_id = '';
    public $dealer_id = '';
    public $position_id = null; 
    
    // Update: Hapus non_dealer dari sini
    public $userTypes = [
        'ahm' => 'Karyawan AHM',
        'main_dealer' => 'Karyawan Main Dealer',
        'dealer' => 'Karyawan Dealer',
    ];

    /**
     * Listener saat user_type diganti
     */
    public function updatedUserType($value)
    {
        $this->reset(['honda_id', 'ahm_id', 'trainer_id', 'custom_id', 'main_dealer_id', 'dealer_id', 'position_id', 'role_in_md']);
        $this->resetValidation();
    }

    /**
     * Listener saat pilihan radio di Main Dealer diganti
     */
    public function updatedRoleInMd($value)
    {
        $this->reset(['trainer_id', 'custom_id', 'main_dealer_id']);
        $this->resetValidation(['trainer_id', 'custom_id']);
    }

    // --- VALIDASI REAL-TIME HONDA ID (JALUR DEALER) ---
    public function updatedHondaId($value)
    {
        $this->resetValidation('honda_id');
        $this->reset(['main_dealer_id', 'dealer_id', 'position_id']);

        if (empty($value)) return;

        $whitelist = HondaIdVerification::where('honda_id', trim($value))->first();

        if (!$whitelist) {
            $this->addError('honda_id', 'Honda ID tidak ditemukan.');
            return;
        }

        if (!$whitelist->is_active) {
            $this->addError('honda_id', 'Honda ID tidak aktif.');
            return;
        }

        if ($whitelist->has_account) {
            $this->addError('honda_id', 'Honda ID sudah terdaftar.');
            return;
        }

        $this->main_dealer_id = $whitelist->main_dealer_id;
        $this->dealer_id = $whitelist->dealer_id;
        $this->position_id = $whitelist->position_id;
    }

    // --- VALIDASI REAL-TIME TRAINER ID (JALUR MAIN DEALER) ---
    public function updatedTrainerId($value)
    {
        $this->resetValidation('trainer_id');
        $this->main_dealer_id = '';

        if (empty($value)) return;

        $whitelist = TrainerIdVerification::where('trainer_id', trim($value))->first();

        if (!$whitelist) {
            $this->addError('trainer_id', 'Trainer ID tidak terdaftar.');
            return;
        }

        if (!$whitelist->is_active) {
            $this->addError('trainer_id', 'Trainer ID tidak aktif.');
            return;
        }

        if ($whitelist->has_account) {
            $this->addError('trainer_id', 'Trainer ID sudah memiliki akun.');
            return;
        }

        $this->main_dealer_id = $whitelist->main_dealer_id;
    }

    // --- VALIDASI REAL-TIME AHM ID ---
    public function updatedAhmId($value)
    {
        $this->resetValidation('ahm_id');
        if (empty($value)) return;

        $whitelist = AhmIdVerification::where('ahm_id', trim($value))->first();

        if (!$whitelist) {
            $this->addError('ahm_id', 'AHM ID tidak terdaftar.');
        } elseif (!$whitelist->is_active) {
            $this->addError('ahm_id', 'AHM ID tidak aktif.');
        } elseif ($whitelist->has_account) {
            $this->addError('ahm_id', 'AHM ID sudah terdaftar.');
        }
    }

    // --- VALIDASI REAL-TIME MD ID / CUSTOM ID ---
    public function updatedCustomId($value)
    {
        $this->resetValidation('custom_id');
        if (empty($value)) return;

        $whitelist = CustomIdVerification::where('custom_id', trim($value))->first();

        if (!$whitelist) {
            $this->addError('custom_id', 'ID tidak ditemukan.');
            return;
        } 
        
        if (!$whitelist->is_active) {
            $this->addError('custom_id', 'ID tidak aktif.');
            return;
        } 
        
        if ($whitelist->has_account) {
            $this->addError('custom_id', 'ID sudah terdaftar.');
            return;
        }

        // Jika Main Dealer memilih jalur MD ID, set juga Main Dealer ID-nya
        if ($this->user_type === 'main_dealer') {
            $this->main_dealer_id = $whitelist->main_dealer_id;
        }
    }

    public function register()
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', 'max:20'], 
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'user_type' => ['required', Rule::in(array_keys($this->userTypes))],
            'agreed_pdp' => ['accepted'], // Validasi wajib centang persetujuan
        ];

        // Jalur Dealer
        if ($this->user_type === 'dealer') {
            $rules['honda_id'] = ['required', 'string', 'unique:users,honda_id'];
        }

        // Jalur AHM
        if ($this->user_type === 'ahm') {
            $rules['ahm_id'] = ['required', 'string', 'unique:users,ahm_id'];
        }

        // Jalur Main Dealer (Trainer / MD ID)
        if ($this->user_type === 'main_dealer') {
            $rules['role_in_md'] = ['required'];
            if ($this->role_in_md === 'trainer') {
                $rules['trainer_id'] = ['required', 'string', 'unique:users,trainer_id'];
            } else {
                $rules['custom_id'] = ['required', 'string', 'unique:users,custom_id'];
            }
        }

        $this->validate($rules, [
            'agreed_pdp.accepted' => 'Anda harus menyetujui Pemrosesan Data Pribadi (PDP) untuk melanjutkan.',
        ]);

        // Simpan User
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'phone_number' => $this->phone_number,
            'user_type' => $this->user_type,
            
            'honda_id' => $this->honda_id,
            'ahm_id' => $this->ahm_id,
            'trainer_id' => $this->trainer_id,
            'custom_id' => $this->custom_id,

            'main_dealer_id' => $this->main_dealer_id ?: null,
            'dealer_id' => $this->dealer_id ?: null,
            'position_id' => $this->position_id,
        ]);

        // Role Mapping
        if ($this->user_type === 'ahm') {
            $user->assignRole('super_admin');
        } elseif ($this->user_type === 'main_dealer') {
            $user->assignRole('middle_admin');
        } else {
            $user->assignRole('user');
        }

        Auth::login($user);
        
        // Sesuaikan route tujuan setelah register
        return redirect()->route('dashboard'); 
    }

    public function render()
    {
        // Ambil data PDP yang statusnya aktif
        $pdpContent = Pdp::where('is_active', true)->latest()->first();

        return view('livewire.auth.register', [
            'pdpContent' => $pdpContent
        ])->layout('layouts.guest'); 
    }
}