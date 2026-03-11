<?php

namespace App\Filament\Resources\BookletResource\Pages;

use App\Filament\Resources\BookletResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBooklet extends CreateRecord
{
    protected static string $resource = BookletResource::class;
}
