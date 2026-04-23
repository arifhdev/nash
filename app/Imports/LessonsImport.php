<?php

namespace App\Imports;

use App\Models\Lesson;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LessonsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Pastikan kolom di Excel Anda bernama: title, type, video_url, duration_minutes
        $lesson = Lesson::create([
            'title'            => $row['title'],
            'slug'             => Str::slug($row['title']), // Auto generate slug
            'type'             => $row['type'] ?? 'video',
            'video_url'        => $row['video_url'] ?? null,
            'duration_minutes' => $row['duration_minutes'] ?? 0,
            'is_active'        => true,
        ]);

        // (Opsional) Jika di Excel Anda ada kolom 'module_ids' yang isinya misal: "1,2,3"
        // Anda bisa langsung menautkannya ke module di sini:
        if (!empty($row['module_ids'])) {
            $moduleIds = explode(',', $row['module_ids']);
            $lesson->modules()->sync($moduleIds);
        }

        return $lesson;
    }
}