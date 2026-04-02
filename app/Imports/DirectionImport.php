<?php

namespace App\Imports;

use App\Models\Department;
use App\Models\Direction;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class DirectionImport implements
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

    public function model(array $row): ?Direction
    {
        $nomi   = trim((string)($row['nomi']   ?? ''));
        $kodi   = trim((string)($row['kodi']   ?? ''));
        $kafedra= trim((string)($row['kafedra']?? ''));
        $daraja = trim((string)($row['daraja'] ?? ''));
        $yillar = trim((string)($row['yillar'] ?? ''));

        // Bo'sh qatorni o'tkazib yuborish
        if ($nomi === '' && $kodi === '' && $kafedra === '') return null;

        // Validatsiya
        if ($nomi === '')   throw new \Exception("'nomi' ustuni bo'sh");
        if ($kodi === '')   throw new \Exception("'kodi' ustuni bo'sh");
        if ($kafedra === '') throw new \Exception("'kafedra' ustuni bo'sh");
        if ($daraja === '') throw new \Exception("'daraja' ustuni bo'sh (bakalavr yoki magistratura)");
        if ($yillar === '') throw new \Exception("'yillar' ustuni bo'sh");

        $department = Department::where('name', $kafedra)->first();
        if (!$department) {
            throw new \Exception("Kafedra topilmadi: '{$kafedra}'. Ma'lumotnoma varag'ini tekshiring.");
        }

        return new Direction([
            'name'          => $nomi,
            'code'          => $kodi,
            'department_id' => $department->id,
            'degree_type'   => strtolower($daraja) === 'magistratura' ? 'magistratura' : 'bakalavr',
            'duration_years'=> (int)$yillar,
            'is_active'     => true,
        ]);
    }
}
