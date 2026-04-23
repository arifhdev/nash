<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\Division;
use App\Models\Position;
use App\Models\MainDealer;
use App\Models\Dealer;
use App\Jobs\SendBroadcastJob; // <-- Import Custom Job di sini
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

class BroadcastEmail extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Content Management';
    protected static ?string $title = 'Broadcast Email';
    protected static string $view = 'filament.pages.broadcast-email';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Filter Penerima')
                    ->description('Kosongkan filter jika ingin mengirim ke semua entitas di kategori tersebut.')
                    ->schema([
                        Forms\Components\Select::make('user_type')
                            ->label('Filter Tipe Karyawan')
                            ->multiple()
                            ->options([
                                'Karyawan AHM' => 'Karyawan AHM',
                                'Karyawan Main Dealer' => 'Karyawan Main Dealer',
                                'Karyawan Dealer' => 'Karyawan Dealer',
                            ]),
                            
                        Forms\Components\Select::make('division_id')
                            ->label('Divisi')
                            ->multiple()
                            ->searchable()
                            ->options(Division::pluck('name', 'id')), 
                            
                        Forms\Components\Select::make('position_id')
                            ->label('Jabatan')
                            ->multiple()
                            ->searchable()
                            ->options(Position::pluck('name', 'id')),
                            
                        Forms\Components\Select::make('main_dealer_id')
                            ->label('Main Dealer')
                            ->multiple()
                            ->searchable()
                            ->options(MainDealer::pluck('name', 'id')),
                            
                        Forms\Components\Select::make('dealer_id')
                            ->label('Dealer')
                            ->multiple()
                            ->searchable()
                            ->options(Dealer::pluck('name', 'id')),
                    ])->columns(3),

                Forms\Components\Section::make('Konten Email')
                    ->schema([
                        Forms\Components\TextInput::make('subject')
                            ->label('Subjek Email')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('content')
                            ->label('Pesan')
                            ->required()
                            ->toolbarButtons([
                                'bold', 'italic', 'link', 'bulletList', 'orderedList', 'h2', 'h3'
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function sendBroadcast(): void
    {
        $data = $this->form->getState();

        $query = User::query();

        if (!empty($data['user_type'])) {
            $query->whereIn('user_type', $data['user_type']);
        }
        if (!empty($data['division_id'])) {
            $query->whereIn('division_id', $data['division_id']);
        }
        if (!empty($data['position_id'])) {
            $query->whereIn('position_id', $data['position_id']);
        }
        if (!empty($data['main_dealer_id'])) {
            $query->whereIn('main_dealer_id', $data['main_dealer_id']);
        }
        if (!empty($data['dealer_id'])) {
            $query->whereIn('dealer_id', $data['dealer_id']);
        }

        // Ambil user yang punya email saja agar tidak error saat dikirim
        $users = $query->whereNotNull('email')->get();

        if ($users->isEmpty()) {
            Notification::make()
                ->title('Tidak ada penerima')
                ->body('Tidak ditemukan user dengan kriteria filter tersebut atau user tidak memiliki email.')
                ->warning()
                ->send();
            return;
        }

        // Eksekusi Custom Job agar terbaca di halaman Jobs Monitor
        foreach ($users as $user) {
            SendBroadcastJob::dispatch($user->email, $data['subject'], $data['content']);
        }

        Notification::make()
            ->title('Broadcast Sedang Diproses')
            ->body("Email berhasil dimasukkan ke dalam antrean (Queue) untuk {$users->count()} pengguna. Silakan pantau di menu Jobs.")
            ->success()
            ->send();

        // Reset form setelah sukses
        $this->form->fill();
    }
}