<?php

namespace App\Filament\Resources;

use App\Enums\UserType;
use App\Filament\Resources\UserResource\Pages;
use App\Models\Dealer;
use App\Models\User;
use App\Models\Position;
use App\Models\HondaIdVerification;
use App\Models\CustomIdVerification; 
use App\Models\AhmIdVerification; 
use App\Models\TrainerIdVerification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // --- BAGIAN 1: INFORMASI AKUN ---
                Forms\Components\Section::make('Informasi Akun')
                    ->description('Detail login dan hak akses sistem.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255),

                        Forms\Components\Select::make('roles')
                            ->label('Role Akses')
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),
                    ])->columns(2),

                // --- BAGIAN 2: DATA KARYAWAN & DEALER ---
                Forms\Components\Section::make('Detail Karyawan')
                    ->description('Klasifikasi karyawan dan penempatan dealer.')
                    ->schema([
                        // --- STATUS KARYAWAN ---
                        Forms\Components\Select::make('user_type')
                            ->label('Status Karyawan')
                            ->options(UserType::class)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                // Reset semua field terkait jika tipe diganti
                                $set('main_dealer_id', null);
                                $set('dealer_id', null);
                                $set('honda_id', null);
                                $set('ahm_id', null);
                                $set('custom_id', null);
                                $set('trainer_id', null);
                                $set('role_in_md', null); // Reset pilihan radio sub-level
                            }),

                        // --- SUB-PILIHAN KHUSUS MAIN DEALER (Virtual Field) ---
                        Forms\Components\Radio::make('role_in_md')
                            ->label('Posisi di Main Dealer')
                            ->options([
                                'trainer' => 'Sebagai Trainer',
                                'non_trainer' => 'Bukan Trainer (Karyawan Biasa)',
                            ])
                            ->inline()
                            ->live()
                            ->dehydrated(false) // PENTING: Jangan disimpan ke database
                            ->visible(function (Get $get) {
                                $userType = $get('user_type');
                                $val = $userType instanceof UserType ? $userType->value : $userType;
                                return $val === UserType::MAIN_DEALER->value;
                            })
                            ->formatStateUsing(function (?Model $record) {
                                // Auto-select saat halaman Edit dibuka
                                if ($record?->trainer_id) return 'trainer';
                                if ($record?->custom_id) return 'non_trainer';
                                return null;
                            })
                            ->afterStateUpdated(function (Set $set) {
                                // Reset ID kalau pindah dari Trainer ke Bukan Trainer
                                $set('trainer_id', null);
                                $set('custom_id', null);
                                $set('main_dealer_id', null);
                            }),

                        // --- 1. JALUR HONDA ID (Hanya untuk Karyawan Dealer) ---
                        Forms\Components\TextInput::make('honda_id')
                            ->label('Honda ID')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->visible(function (Get $get) {
                                $userType = $get('user_type');
                                $val = $userType instanceof UserType ? $userType->value : $userType;
                                return $val === UserType::DEALER->value; 
                            })
                            ->live(debounce: 500)
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if (filled($state)) {
                                    $whitelist = HondaIdVerification::where('honda_id', $state)->first();
                                    if ($whitelist) {
                                        $set('main_dealer_id', $whitelist->main_dealer_id);
                                        $set('dealer_id', $whitelist->dealer_id);
                                        if ($whitelist->position_id) {
                                            $set('positions', [$whitelist->position_id]);
                                        }
                                    }
                                }
                            })
                            ->rules([
                                fn (Get $get, string $operation) => function (string $attribute, $value, \Closure $fail) use ($operation) {
                                    if ($operation === 'create' && filled($value)) {
                                        $whitelist = HondaIdVerification::where('honda_id', $value)->first();
                                        if (!$whitelist) $fail('Honda ID tidak ditemukan di Master Whitelist.');
                                        elseif (!$whitelist->is_active) $fail('Honda ID ini statusnya tidak aktif.');
                                        elseif ($whitelist->has_account) $fail('Honda ID ini sudah pernah digunakan.');
                                    }
                                },
                            ]),

                        // --- 2. JALUR AHM ID (Hanya untuk AHM) ---
                        Forms\Components\TextInput::make('ahm_id')
                            ->label('AHM ID')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->visible(function (Get $get) {
                                $userType = $get('user_type');
                                $val = $userType instanceof UserType ? $userType->value : $userType;
                                return $val === UserType::AHM->value;
                            })
                            ->live(debounce: 500)
                            ->rules([
                                fn (Get $get, string $operation) => function (string $attribute, $value, \Closure $fail) use ($operation) {
                                    if ($operation === 'create' && filled($value)) {
                                        $whitelist = AhmIdVerification::where('ahm_id', $value)->first();
                                        if (!$whitelist) $fail('AHM ID tidak ditemukan di Master Whitelist.');
                                        elseif (!$whitelist->is_active) $fail('AHM ID tidak aktif.');
                                        elseif ($whitelist->has_account) $fail('AHM ID sudah pernah digunakan.');
                                    }
                                },
                            ]),

                        // --- 3A. JALUR TRAINER ID (Hanya Muncul jika Pilih "Sebagai Trainer") ---
                        Forms\Components\TextInput::make('trainer_id')
                            ->label('Trainer ID')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->visible(function (Get $get) {
                                $userType = $get('user_type');
                                $val = $userType instanceof UserType ? $userType->value : $userType;
                                return $val === UserType::MAIN_DEALER->value && $get('role_in_md') === 'trainer';
                            })
                            ->required(function (Get $get) {
                                $userType = $get('user_type');
                                $val = $userType instanceof UserType ? $userType->value : $userType;
                                return $val === UserType::MAIN_DEALER->value && $get('role_in_md') === 'trainer';
                            })
                            ->live(debounce: 500)
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if (filled($state)) {
                                    $whitelist = TrainerIdVerification::where('trainer_id', $state)->first();
                                    if ($whitelist) {
                                        $set('main_dealer_id', $whitelist->main_dealer_id);
                                    }
                                }
                            })
                            ->rules([
                                fn (Get $get, string $operation) => function (string $attribute, $value, \Closure $fail) use ($operation) {
                                    if ($operation === 'create' && filled($value)) {
                                        $whitelist = TrainerIdVerification::where('trainer_id', $value)->first();
                                        if (!$whitelist) $fail('Trainer ID tidak ditemukan di Master Whitelist.');
                                        elseif (!$whitelist->is_active) $fail('Trainer ID statusnya tidak aktif.');
                                        elseif ($whitelist->has_account) $fail('Trainer ID sudah pernah digunakan.');
                                    }
                                },
                            ]),

                        // --- 3B. JALUR MD ID (Hanya Muncul jika Pilih "Bukan Trainer") ---
                        Forms\Components\TextInput::make('custom_id')
                            ->label('MD ID')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->visible(function (Get $get) {
                                $userType = $get('user_type');
                                $val = $userType instanceof UserType ? $userType->value : $userType;
                                return $val === UserType::MAIN_DEALER->value && $get('role_in_md') === 'non_trainer';
                            })
                            ->required(function (Get $get) {
                                $userType = $get('user_type');
                                $val = $userType instanceof UserType ? $userType->value : $userType;
                                return $val === UserType::MAIN_DEALER->value && $get('role_in_md') === 'non_trainer';
                            })
                            ->live(debounce: 500)
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if (filled($state)) {
                                    $whitelist = CustomIdVerification::where('custom_id', $state)->first();
                                    if ($whitelist) {
                                        $set('main_dealer_id', $whitelist->main_dealer_id);
                                    }
                                }
                            })
                            ->rules([
                                fn (Get $get, string $operation) => function (string $attribute, $value, \Closure $fail) use ($operation) {
                                    if ($operation === 'create' && filled($value)) {
                                        $whitelist = CustomIdVerification::where('custom_id', $value)->first();
                                        if (!$whitelist) $fail('MD ID tidak ditemukan di Master Whitelist.');
                                        elseif (!$whitelist->is_active) $fail('MD ID tidak aktif.');
                                        elseif ($whitelist->has_account) $fail('MD ID sudah pernah digunakan.');
                                    }
                                },
                            ]),

                        Forms\Components\TextInput::make('phone_number')
                            ->label('No WhatsApp')
                            ->tel()
                            ->maxLength(20),

                        // --- JABATAN (POSITIONS) ---
                        Forms\Components\Select::make('positions')
                            ->label('Jabatan')
                            ->relationship('positions', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih Jabatan (Boleh Kosong)')
                            ->hidden(function (Get $get) {
                                $val = $get('user_type');
                                if ($val instanceof UserType) $val = $val->value;
                                return $val === UserType::MAIN_DEALER->value; 
                            }),

                        // --- MAIN DEALER ---
                        Forms\Components\Select::make('main_dealer_id')
                            ->label('Main Dealer')
                            ->relationship('mainDealer', 'name', modifyQueryUsing: function (Builder $query) {
                                $user = auth()->user();
                                if ($user->hasRole('super_admin')) return $query;
                                
                                $md = $user->mainDealer;
                                if ($md && $md->isHeadOffice()) {
                                    return $query->where('group', $md->group);
                                }
                                return $query->where('id', $user->main_dealer_id);
                            })
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->code} - {$record->name}")
                            ->searchable()
                            ->preload()
                            ->visible(function (Get $get) {
                                $userType = $get('user_type');
                                if (!$userType) return false;
                                $value = $userType instanceof UserType ? $userType->value : $userType;
                                return in_array($value, [UserType::MAIN_DEALER->value, UserType::DEALER->value]);
                            })
                            ->required(function (Get $get) {
                                $userType = $get('user_type');
                                if (!$userType) return false;
                                $value = $userType instanceof UserType ? $userType->value : $userType;
                                return in_array($value, [UserType::MAIN_DEALER->value, UserType::DEALER->value]);
                            })
                            ->default(fn () => auth()->user()->main_dealer_id)
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('dealer_id', null)),

                        // --- DEALER / AHASS ---
                        Forms\Components\Select::make('dealer_id')
                            ->label('Dealer / AHASS')
                            ->options(function (Get $get) {
                                $mainDealerId = $get('main_dealer_id');
                                if (!$mainDealerId) return [];
                                
                                return Dealer::where('main_dealer_id', $mainDealerId)
                                    ->get()
                                    ->mapWithKeys(fn ($dealer) => [
                                        $dealer->id => "{$dealer->code} - {$dealer->name}"
                                    ]);
                            })
                            ->searchable()
                            ->preload()
                            ->visible(function (Get $get) {
                                $userType = $get('user_type');
                                if (!$userType) return false;
                                $value = $userType instanceof UserType ? $userType->value : $userType;
                                return $value === UserType::DEALER->value;
                            })
                            ->required(function (Get $get) {
                                $userType = $get('user_type');
                                if (!$userType) return false;
                                $value = $userType instanceof UserType ? $userType->value : $userType;
                                return $value === UserType::DEALER->value;
                            }),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('positions.name')
                    ->label('Jabatan')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'middle_admin' => 'warning',
                        'user' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('user_type')
                    ->label('Tipe Karyawan')
                    ->badge(),

                Tables\Columns\TextColumn::make('mainDealer.name')
                    ->label('Main Dealer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('dealer.name')
                    ->label('Dealer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_type')
                    ->options(UserType::class)
                    ->label('Filter Tipe Karyawan'),
                
                Tables\Filters\SelectFilter::make('positions')
                    ->relationship('positions', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Filter Jabatan'),

                Tables\Filters\SelectFilter::make('main_dealer_id')
                    ->relationship('mainDealer', 'name', modifyQueryUsing: function (Builder $query) {
                        $user = auth()->user();
                        if ($user->hasRole('super_admin')) return $query;
                        
                        $md = $user->mainDealer;
                        if ($md && method_exists($md, 'isHeadOffice') && $md->isHeadOffice()) {
                            return $query->where('group', $md->group);
                        }
                        return $query->where('id', $user->main_dealer_id);
                    })
                    ->label('Filter Main Dealer')
                    ->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->mainDealer?->isHeadOffice()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            UserResource\RelationManagers\CoursesRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            return $query;
        }

        if ($user->hasRole('middle_admin')) {
            $md = $user->mainDealer;

            if ($md && $md->isHeadOffice()) {
                return $query->whereHas('mainDealer', function ($q) use ($md) {
                    $q->where('group', $md->group);
                })->whereDoesntHave('roles', function ($q) {
                    $q->where('name', 'super_admin'); 
                });
            } 
            
            return $query
                ->where('main_dealer_id', $user->main_dealer_id)
                ->whereDoesntHave('roles', function ($q) {
                    $q->where('name', 'super_admin');
                });
        }
        
        return $query->whereRaw('1 = 0'); 
    }
}