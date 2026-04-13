<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Process;
use Filament\Notifications\Notification;

class SupervisorStatus extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationLabel = 'Supervisor Status';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 100;

    protected static string $view = 'filament.pages.supervisor-status';

    // Definisikan lokasi absolut perintah
    protected string $sudo = '/usr/bin/sudo';
    protected string $supervisorctl = '/usr/bin/supervisorctl';
    protected string $systemctl = '/usr/bin/systemctl';

    public function getStatus(): array
    {
        // Panggil status pakai full path agar PHP tidak bingung
        $process = Process::run("{$this->sudo} {$this->supervisorctl} status");
        
        if (!$process->successful()) {
            return [
                'error' => true,
                'output' => $process->errorOutput() ?: $process->output() ?: 'Tidak dapat terhubung ke supervisor.sock. Cek izin chmod file .sock Anda.',
            ];
        }

        $output = trim($process->output());
        if (empty($output)) {
            return ['error' => false, 'processes' => []];
        }

        $lines = explode("\n", $output);
        $parsed = [];

        foreach ($lines as $line) {
            $parts = preg_split('/\s+/', $line, 3);
            if(count($parts) < 2) continue;
            
            $parsed[] = [
                'name' => $parts[0] ?? 'Unknown',
                'status' => $parts[1] ?? 'UNKNOWN',
                'description' => $parts[2] ?? '-',
            ];
        }

        return ['error' => false, 'processes' => $parsed];
    }

    public function startSupervisorService()
    {
        $this->runCommand("{$this->sudo} {$this->systemctl} start supervisor", "Servis Utama Supervisor BERHASIL dinyalakan!");
    }

    public function startProcess(string $name)
    {
        $this->runCommand("{$this->sudo} {$this->supervisorctl} start {$name}", "Worker {$name} sekarang RUNNING!");
    }

    public function stopProcess(string $name)
    {
        $this->runCommand("{$this->sudo} {$this->supervisorctl} stop {$name}", "Worker {$name} dimatikan.");
    }

    public function restartProcess(string $name)
    {
        $this->runCommand("{$this->sudo} {$this->supervisorctl} restart {$name}", "Worker {$name} berhasil direstart.");
    }

    private function runCommand(string $command, string $successMessage)
    {
        $process = Process::run($command);

        if ($process->successful()) {
            Notification::make()->title($successMessage)->success()->send();
        } else {
            Notification::make()
                ->title('Gagal!')
                ->description($process->errorOutput() ?: 'Cek izin sudo/visudo.')
                ->danger()
                ->send();
        }
    }
}