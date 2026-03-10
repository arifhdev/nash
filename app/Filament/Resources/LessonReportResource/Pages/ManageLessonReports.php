<?php

namespace App\Filament\Resources\LessonReportResource\Pages;

use App\Filament\Resources\LessonReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageLessonReports extends ManageRecords
{
    protected static string $resource = LessonReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
