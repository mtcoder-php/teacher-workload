<?php

namespace App\Imports;

use App\Models\Department;
use App\Models\Direction;
use App\Models\Subject;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class SubjectImport implements
    ToModel,
    WithHeadingRow,
    WithStartRow,
    SkipsEmptyRows,
    WithBatchInserts,
    WithChunkReading
{
    public function headingRow(): int { return 1; }
    public function startRow(): int   { return 4; }
    public function batchSize(): int  { return 100; }
    public function chunkSize(): int  { return 100; }

    public function model(array $row): ?Subject
    {
        $nomi    = trim((string)($row['nomi']    ?? ''));
        $kodi    = trim((string)($row['kodi']    ?? ''));
        $kafedra = trim((string)($row['kafedra'] ?? ''));

        if ($nomi === '' && $kodi === '') return null;

        if ($nomi === '')    throw new \Exception("'nomi' ustuni bo'sh");
        if ($kodi === '')    throw new \Exception("'kodi' ustuni bo'sh");
        if ($kafedra === '') throw new \Exception("'kafedra' ustuni bo'sh");

        $department = Department::where('name', $kafedra)->first();
        if (!$department) {
            throw new \Exception("Kafedra topilmadi: '{$kafedra}'");
        }

        $direction = null;
        $yonalishKod = trim((string)($row['yonalish_kodi'] ?? ''));
        if ($yonalishKod !== '') {
            $direction = Direction::where('code', $yonalishKod)->first();
        }

        return new Subject([
            'name'          => $nomi,
            'code'          => $kodi,
            'department_id' => $department->id,
            'direction_id'  => $direction?->id,
            'course_level'  => (int)($row['kurs']   ?? 1),
            'credit_hours'  => (int)($row['kredit'] ?? 0),
            'subject_type'  => $this->parseSubjectType($row['turi'] ?? ''),

            'semester_1_lecture'    => (float)($row['s1_maruza']       ?? 0),
            'semester_1_practical'  => (float)($row['s1_amaliy']       ?? 0),
            'semester_1_laboratory' => (float)($row['s1_laboratoriya'] ?? 0),
            'semester_1_seminar'    => (float)($row['s1_seminar']      ?? 0),
            'semester_1_practice'   => (float)($row['s1_amaliyot']     ?? 0),
            'semester_1_exam'       => (float)($row['s1_imtihon']      ?? 0),
            'semester_1_test'       => (float)($row['s1_sinov']        ?? 0),

            'semester_2_lecture'    => (float)($row['s2_maruza']       ?? 0),
            'semester_2_practical'  => (float)($row['s2_amaliy']       ?? 0),
            'semester_2_laboratory' => (float)($row['s2_laboratoriya'] ?? 0),
            'semester_2_seminar'    => (float)($row['s2_seminar']      ?? 0),
            'semester_2_practice'   => (float)($row['s2_amaliyot']     ?? 0),
            'semester_2_exam'       => (float)($row['s2_imtihon']      ?? 0),
            'semester_2_test'       => (float)($row['s2_sinov']        ?? 0),

            'coursework_hours'     => (float)($row['kurs_ishi']     ?? 0),
            'diploma_hours'        => (float)($row['diplom_ishi']   ?? 0),
            'consultation_hours'   => (float)($row['konsultatsiya'] ?? 0),

            'can_be_potok'         => !empty($row['potok_mumkin']) && $row['potok_mumkin'] == '1',
            'min_groups_for_potok' => (int)($row['min_guruhlar'] ?? 2),
            'is_active'            => true,
        ]);
    }

    private function parseSubjectType(string $val): string
    {
        return match(strtolower(trim($val))) {
            'asosiy',  'majburiy' => 'asosiy',
            'yordamchi'           => 'yordamchi',
            'ixtiyoriy', 'tanlov' => 'ixtiyoriy',
            default               => 'asosiy',
        };
    }
}
