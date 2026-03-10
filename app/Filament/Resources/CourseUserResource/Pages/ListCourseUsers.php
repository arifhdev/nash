<?php

namespace App\Filament\Resources\CourseUserResource\Pages;

use App\Filament\Resources\CourseUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCourseUsers extends ListRecords
{
    protected static string $resource = CourseUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
