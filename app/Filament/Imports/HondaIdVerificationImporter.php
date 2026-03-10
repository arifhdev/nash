<?php

namespace App\Filament\Imports;

use App\Models\HondaIdVerification;
use App\Models\MainDealer;
use App\Models\Dealer;
use App\Models\Position;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class HondaIdVerificationImporter extends Importer
{
    protected static ?string $model = HondaIdVerification::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('honda_id')
                ->label('Honda ID')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
                
            // Gunakan ->fillRecordUsing(fn() => null) untuk mencegah Laravel Error Mass Assignment
            ImportColumn::make('md_code')
                ->label('Kode Main Dealer')
                ->fillRecordUsing(fn () => null),
                
            ImportColumn::make('dealer_code')
                ->label('Kode Dealer')
                ->fillRecordUsing(fn () => null),
                
            ImportColumn::make('jabatan')
                ->label('Jabatan')
                ->fillRecordUsing(fn () => null),
                
            ImportColumn::make('group')
                ->label('Group')
                ->fillRecordUsing(fn () => null),
                
            ImportColumn::make('is_active')
                ->label('Status Aktif')
                ->boolean(),
        ];
    }

    public function resolveRecord(): ?HondaIdVerification
    {
        // Gunakan ?? '' agar aman dari Error Null
        $hondaId = $this->data['honda_id'] ?? '';
        
        return HondaIdVerification::firstOrNew([
            'honda_id' => trim($hondaId),
        ]);
    }

    protected function beforeSave(): void
    {
        // 1. Status Aktif
        if (isset($this->data['is_active']) && $this->data['is_active'] !== '') {
            $this->record->is_active = (bool) $this->data['is_active'];
        } elseif (! $this->record->exists) {
            $this->record->is_active = true; // Default aktif untuk data baru
        }

        // 2. Main Dealer
        $mdCode = $this->data['md_code'] ?? '';
        if (!empty($mdCode)) {
            $md = MainDealer::where('code', trim($mdCode))->first();
            $this->record->main_dealer_id = $md?->id;
        }

        // 3. Dealer
        $dealerCode = $this->data['dealer_code'] ?? '';
        if (!empty($dealerCode)) {
            $dealer = Dealer::where('code', trim($dealerCode))->first();
            $this->record->dealer_id = $dealer?->id;
        }

        // 4. Jabatan (Auto create jika belum ada)
        $jabatanName = $this->data['jabatan'] ?? '';
        $groupName = $this->data['group'] ?? '';
        
        if (!empty($jabatanName)) {
            $position = Position::firstOrCreate([
                'name' => trim($jabatanName),
                'group' => !empty($groupName) ? trim($groupName) : null,
            ]);
            $this->record->position_id = $position->id;
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import Honda ID selesai. ' . number_format($import->successful_rows) . ' baris berhasil diproses.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' Ditemukan ' . number_format($failedRowsCount) . ' baris yang gagal. Klik tombol Download di bawah untuk melihat log error-nya.';
        }

        return $body;
    }

    public function getJobBatchName(): ?string
    {
        return 'Import Honda ID Whitelist (Massal)';
    }

    public function getChunkSize(): int
    {
        return 1000;
    }
}