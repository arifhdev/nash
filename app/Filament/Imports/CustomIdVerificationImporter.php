<?php

namespace App\Filament\Imports;

use App\Models\CustomIdVerification;
use App\Models\MainDealer;
use App\Models\Dealer;
use App\Models\Position;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class CustomIdVerificationImporter extends Importer
{
    protected static ?string $model = CustomIdVerification::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('custom_id')
                ->label('Custom ID')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
                
            ImportColumn::make('md_code')->label('Kode Main Dealer')->fillRecordUsing(fn () => null),
            ImportColumn::make('dealer_code')->label('Kode Dealer')->fillRecordUsing(fn () => null),
            ImportColumn::make('jabatan')->label('Jabatan')->fillRecordUsing(fn () => null),
            ImportColumn::make('group')->label('Group')->fillRecordUsing(fn () => null),
            ImportColumn::make('is_active')->label('Status Aktif')->boolean(),
        ];
    }

    public function resolveRecord(): ?CustomIdVerification
    {
        $customId = $this->data['custom_id'] ?? '';
        return CustomIdVerification::firstOrNew(['custom_id' => trim($customId)]);
    }

    protected function beforeSave(): void
    {
        if (isset($this->data['is_active']) && $this->data['is_active'] !== '') {
            $this->record->is_active = (bool) $this->data['is_active'];
        } elseif (! $this->record->exists) {
            $this->record->is_active = true; 
        }

        $mdCode = $this->data['md_code'] ?? '';
        if (!empty($mdCode)) {
            $md = MainDealer::where('code', trim($mdCode))->first();
            $this->record->main_dealer_id = $md?->id;
        }

        $dealerCode = $this->data['dealer_code'] ?? '';
        if (!empty($dealerCode)) {
            $dealer = Dealer::where('code', trim($dealerCode))->first();
            $this->record->dealer_id = $dealer?->id;
        }

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
        $body = 'Import Custom ID selesai. ' . number_format($import->successful_rows) . ' baris berhasil diproses.';
        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' Ditemukan ' . number_format($failedRowsCount) . ' baris yang gagal.';
        }
        return $body;
    }

    public function getJobBatchName(): ?string { return 'Import Custom ID Whitelist'; }
    public function getChunkSize(): int { return 1000; }
}