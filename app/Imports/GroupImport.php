<?php

namespace App\Imports;

use App\Models\AcademicYear;
use App\Models\Direction;
use App\Models\Group;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class GroupImport implements
    ToModel,
    WithHeadingRow,
    WithStartRow,
    SkipsEmptyRows,
    WithChunkReading
{
    private int $academicYearId;

    public function __construct()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            throw new \Exception('Faol o\'quv yili topilmadi. Avval o\'quv yilini faollashtiring!');
        }
        $this->academicYearId = $activeYear->id;
    }

    public function headingRow(): int { return 1; }
    public function startRow(): int   { return 4; }
    public function chunkSize(): int  { return 100; }

    public function model(array $row): ?Group
    {
        $nomi        = trim((string)($row['nomi']          ?? ''));
        $yonalishKod = trim((string)($row['yonalish_kodi'] ?? ''));
        $kurs        = trim((string)($row['kurs']          ?? ''));

        if ($nomi === '' && $yonalishKod === '') return null;

        if ($nomi === '')        throw new \Exception("'nomi' ustuni bo'sh");
        if ($yonalishKod === '') throw new \Exception("'yonalish_kodi' ustuni bo'sh");
        if ($kurs === '')        throw new \Exception("'kurs' ustuni bo'sh");

        $direction = Direction::where('code', $yonalishKod)->first();
        if (!$direction) {
            throw new \Exception("Yo'nalish topilmadi: '{$yonalishKod}'");
        }

        $group = new Group();
        $group->name               = $nomi;
        $group->code               = trim((string)($row['kodi'] ?? $nomi));
        $group->direction_id       = $direction->id;
        $group->academic_year_id   = $this->academicYearId;
        $group->course             = (int)$kurs;
        $group->student_count      = (int)($row['talabalar'] ?? 0);
        $group->education_type     = $this->parseEducationType($row['talim_turi'] ?? '');
        $group->education_language = strtolower(trim($row['til'] ?? '')) === 'russian' ? 'russian' : 'uzbek';
        $group->is_active          = true;

        return $group;
    }

    private function parseEducationType(string $val): string
    {
        $val = strtolower(trim($val));
        return in_array($val, ['kunduzgi', 'sirtqi', 'kechki', 'masofaviy']) ? $val : 'kunduzgi';
    }
}
