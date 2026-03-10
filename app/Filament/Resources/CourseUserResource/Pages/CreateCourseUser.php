<?php

namespace App\Filament\Resources\CourseUserResource\Pages;

use App\Filament\Resources\CourseUserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCourseUser extends CreateRecord
{
    protected static string $resource = CourseUserResource::class;
}
