<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\MainDealer;
use App\Models\Dealer;
use App\Models\Position;
use App\Models\HondaIdVerification;
use App\Models\AhmIdVerification;
use App\Models\MdIdVerification;
use App\Models\TrainerIdVerification;
use App\Models\Pdp; 
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache; // <-- TAMBAHAN UNTUK OPTIMASI

class Register extends Component
{
    // --- Properties Form ---
    public $name;
    public $phone_number;
    public $agreed_pdp = false;
    
    // --- Properties Verifikasi ---
    public $user_type = ''; 
    public $role_in_md = ''; 
    public $honda_id;
    public $ahm_id;
    public $trainer_id; 
    public $custom_id;

    // --- Properties Khusus Penempatan, Divisi, dan Jabatan ---
    public $main_dealer_id = '';
    public $dealer_id = '';
    public $division_id = null; 
    public $position_id = null; 
    
    // --- Properties OTP ---
    public $step = 1; // 1: Form Registrasi, 2: Form OTP
    public $otp_input; // Input dari user
    public $generated_otp; // OTP yang dikirim

    public $userTypes = [
        'ahm' => 'Karyawan AHM',
        'main_dealer' => 'Karyawan Main Dealer',
        'dealer' => 'Karyawan Dealer',
    ];

    public function updatedUserType($value)
    {
        $this->reset(['honda_id', 'ahm_id', 'trainer_id', 'custom_id', 'main_dealer_id', 'dealer_id', 'division_id', 'position_id', 'role_in_md', 'name']);
        $this->resetValidation();
    }

    public function updatedRoleInMd($value)
    {
        $this->reset(['trainer_id', 'custom_id', 'main_dealer_id', 'name']);
        $this->resetValidation(['trainer_id', 'custom_id']);
    }

    private function assignPositionAndDivision($positionId)
    {
        $this->position_id = $positionId;
        
        if ($positionId) {
            $position = Position::find($positionId);
            $this->division_id = $position ? $position->division_id : null;
        } else {
            $this->division_id = null;
        }
    }

    // --- VALIDASI REAL-TIME HONDA ID (JALUR DEALER) ---
    public function updatedHondaId($value)
    {
        $this->resetValidation('honda_id');
        $this->reset(['main_dealer_id', 'dealer_id', 'division_id', 'position_id', 'name']);

        if (empty($value)) return;

        $whitelist = HondaIdVerification::where('honda_id', trim($value))->first();

        if (!$whitelist) { $this->addError('honda_id', 'Honda ID tidak ditemukan.'); return; }
        if (!$whitelist->is_active) { $this->addError('honda_id', 'Honda ID tidak aktif.'); return; }
        if ($whitelist->has_account) { $this->addError('honda_id', 'Honda ID sudah terdaftar.'); return; }

        $this->main_dealer_id = $whitelist->main_dealer_id;
        $this->dealer_id = $whitelist->dealer_id;
        $this->name = $whitelist->name; 
        
        $this->assignPositionAndDivision($whitelist->position_id);
    }

    // --- VALIDASI REAL-TIME TRAINER ID (JALUR MAIN DEALER) ---
    public function updatedTrainerId($value)
    {
        $this->resetValidation('trainer_id');
        $this->reset(['main_dealer_id', 'name', 'division_id', 'position_id']);

        if (empty($value)) return;

        $whitelist = TrainerIdVerification::where('trainer_id', trim($value))->first();

        if (!$whitelist) { $this->addError('trainer_id', 'Trainer ID tidak terdaftar.'); return; }
        if (!$whitelist->is_active) { $this->addError('trainer_id', 'Trainer ID tidak aktif.'); return; }
        if ($whitelist->has_account) { $this->addError('trainer_id', 'Trainer ID sudah memiliki akun.'); return; }

        $this->main_dealer_id = $whitelist->main_dealer_id;
        $this->name = $whitelist->name;
        
        $this->assignPositionAndDivision($whitelist->position_id);
    }

    // --- VALIDASI REAL-TIME AHM ID ---
    public function updatedAhmId($value)
    {
        $this->resetValidation('ahm_id');
        $this->reset(['name', 'division_id', 'position_id']);

        if (empty($value)) return;

        $whitelist = AhmIdVerification::where('ahm_id', trim($value))->first();

        if (!$whitelist) {
            $this->addError('ahm_id', 'AHM ID tidak terdaftar.');
        } elseif (!$whitelist->is_active) {
            $this->addError('ahm_id', 'AHM ID tidak aktif.');
        } elseif ($whitelist->has_account) {
            $this->addError('ahm_id', 'AHM ID sudah terdaftar.');
        } else {
            $this->name = $whitelist->name; 
            
            $this->assignPositionAndDivision($whitelist->position_id);
        }
    }

    // --- VALIDASI REAL-TIME MD ID ---
    public function updatedCustomId($value)
    {
        $this->resetValidation('custom_id');
        $this->reset(['main_dealer_id', 'name', 'division_id', 'position_id']);

        if (empty($value)) return;

        // FIXED: Mengubah 'md_id' menjadi 'custom_id' agar tidak error MySQL
        $whitelist = MdIdVerification::where('custom_id', trim($value))->first();

        if (!$whitelist) { $this->addError('custom_id', 'MD ID tidak ditemukan.'); return; } 
        if (!$whitelist->is_active) { $this->addError('custom_id', 'MD ID tidak aktif.'); return; } 
        if ($whitelist->has_account) { $this->addError('custom_id', 'MD ID sudah terdaftar.'); return; }

        if ($this->user_type === 'main_dealer') {
            $this->main_dealer_id = $whitelist->main_dealer_id;
        }
        $this->name = $whitelist->name;
        
        $this->assignPositionAndDivision($whitelist->position_id);
    }

    public function register()
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20', 'unique:users,phone_number'], 
            'user_type' => ['required', Rule::in(array_keys($this->userTypes))],
            'agreed_pdp' => ['accepted'], 
        ];

        if ($this->user_type === 'dealer') {
            $rules['honda_id'] = ['required', 'string', 'unique:users,honda_id'];
        }

        if ($this->user_type === 'ahm') {
            $rules['ahm_id'] = ['required', 'string', 'unique:users,ahm_id'];
        }

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
            'phone_number.unique' => 'Nomor WhatsApp ini sudah terdaftar.',
        ]);

        // Simpan OTP ke Session
        $this->generated_otp = rand(100000, 999999);
        Session::put('register_otp', $this->generated_otp);

        // Kirim Notifikasi WhatsApp
        $this->sendWhatsAppNotification($this->name, $this->phone_number, $this->generated_otp);
        
        // Pindah ke langkah verifikasi OTP
        $this->step = 2; 
    }

    public function verifyOtp()
    {
        $this->validate([
            'otp_input' => 'required|numeric'
        ]);

        $savedOtp = Session::get('register_otp');

        if ($this->otp_input == $savedOtp) {
             // OTP Benar, Simpan User
            $user = User::create([
                'name' => $this->name,
                'phone_number' => $this->phone_number,
                'user_type' => $this->user_type,
                
                'honda_id' => $this->honda_id,
                'ahm_id' => $this->ahm_id,
                'trainer_id' => $this->trainer_id,
                'custom_id' => $this->custom_id,

                'main_dealer_id' => $this->main_dealer_id ?: null,
                'dealer_id' => $this->dealer_id ?: null,
                
                'division_id' => $this->division_id, 
                'position_id' => $this->position_id, 
            ]);

            $user->assignRole('user');

            Auth::login($user);
            Session::forget('register_otp'); // Hapus OTP dari session setelah berhasil

            return redirect()->route('home'); 
        } else {
            $this->addError('otp_input', 'Kode OTP tidak valid atau sudah kadaluarsa.');
        }
    }

    private function sendWhatsAppNotification($name, $phoneNumber, $otpCode)
    {
        $token = config('services.ruangwa.token');
        $cleanPhone = preg_replace('/[^0-9]/', '', $phoneNumber);

        if (strpos($cleanPhone, '0') === 0) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        $message = "Halo {$name},\n\nKode OTP Anda untuk registrasi di Akademi Satu Hati adalah: *{$otpCode}*\n\nKode ini bersifat rahasia. Jangan berikan kode ini kepada siapa pun.\n\nSalam,\n*Akademi Satu Hati*";

        try {
            // Gunakan User-Agent yang super simpel sesuai dengan test CURL yang sukses tadi
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0',
            ])->asForm()->post('https://app.ruangwa.id/api/send_message', [
                'token' => $token,
                'number' => $cleanPhone,
                'message' => $message,
            ]);

            if (!$response->successful()) {
                Log::error('Ruangwa API Failed: ' . $response->body());
            } else {
                Log::info("WA OTP Berhasil Dikirim ke {$cleanPhone}");
            }

        } catch (\Exception $e) {
            Log::error('Ruangwa Connection Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // OPTIMASI: Menggunakan cache agar database tidak di-query berulang kali
        // saat Livewire melakukan AJAX (mengetik atau pilih dropdown).
        // Cache akan disimpan selama 1 jam (3600 detik).
        $pdpContent = Cache::remember('active_pdp_content', 3600, function () {
            return Pdp::where('is_active', true)->latest()->first();
        });

        return view('livewire.auth.register', [
            'pdpContent' => $pdpContent
        ])->layout('layouts.guest'); 
    }
}