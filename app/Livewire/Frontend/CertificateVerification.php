<?php

namespace App\Livewire\Frontend;

use App\Models\Certificate;
use Livewire\Component;

class CertificateVerification extends Component
{
    public $certificateNumber = '';
    public $certificate = null;
    public $hasSearched = false;
    
    // Tambahan variabel untuk nangkep error server
    public $serverError = null; 

    public function verify()
    {
        // Reset state awal
        $this->serverError = null;
        $this->hasSearched = false;

        $this->validate([
            'certificateNumber' => 'required|string',
        ], [
            'certificateNumber.required' => 'Nomor registrasi sertifikat wajib diisi.',
        ]);

        // KITA KURUNG PAKAI TRY CATCH BIAR KETAHUAN PENYAKITNYA
        try {
            $this->certificate = Certificate::with(['user', 'course'])
                ->where('certificate_number', trim($this->certificateNumber))
                ->first();

            $this->hasSearched = true;
        } catch (\Exception $e) {
            // Kalau server error, tulisan errornya langsung dilempar ke layar!
            $this->serverError = "ERROR SYSTEM: " . $e->getMessage() . " (Baris: " . $e->getLine() . ")";
        }
    }

    public function render()
    {
        return view('livewire.frontend.certificate-verification')->layout('layouts.app');
    }
}