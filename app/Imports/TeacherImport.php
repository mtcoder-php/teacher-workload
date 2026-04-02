<?php

namespace App\Imports;

use App\Models\Department;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class TeacherImport implements
    ToModel,
    WithHeadingRow,
    WithStartRow,
    SkipsEmptyRows,
    WithChunkReading
{
    private ?int $teacherRoleId;

    public function __construct()
    {
        $this->teacherRoleId = Role::where('name', 'oqituvchi')->value('id');
    }

    public function headingRow(): int { return 1; }
    public function startRow(): int   { return 4; }
    public function chunkSize(): int  { return 50; }

    public function model(array $row): ?Teacher
    {
        $fish    = trim((string)($row['fish']    ?? ''));
        $email   = trim((string)($row['email']   ?? ''));
        $kafedra = trim((string)($row['kafedra'] ?? ''));

        if ($fish === '' && $email === '') return null;

        if ($fish === '')    throw new \Exception("'fish' ustuni bo'sh");
        if ($email === '')   throw new \Exception("'email' ustuni bo'sh");
        if ($kafedra === '') throw new \Exception("'kafedra' ustuni bo'sh");

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Email noto'g'ri formatda: '{$email}'");
        }

        $department = Department::where('name', $kafedra)->first();
        if (!$department) {
            throw new \Exception("Kafedra topilmadi: '{$kafedra}'");
        }

        DB::beginTransaction();
        try {
            $user = User::firstOrCreate(
                ['email' => strtolower($email)],
                [
                    'name'      => $fish,
                    'password'  => Hash::make(trim($row['parol'] ?? 'password123')),
                    'is_active' => true,
                    'role_id'   => $this->teacherRoleId,
                ]
            );

            if (!$user->wasRecentlyCreated) {
                $user->update(['name' => $fish]);
            }

            $teacher = Teacher::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'department_id'   => $department->id,
                    'position'        => trim($row['lavozim']      ?? ''),
                    'academic_degree' => trim($row['ilmiy_daraja'] ?? ''),
                    'academic_title'  => trim($row['ilmiy_unvon']  ?? ''),
                    'employment_type' => $this->parseEmploymentType($row['ish_turi'] ?? ''),
                    'is_active'       => true,
                ]
            );

            DB::commit();
            return $teacher;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function parseEmploymentType(string $val): string
    {
        return match(strtolower(trim($val))) {
            'asosiy',   'main_job'            => 'main_job',
            'ichki',    'internal_part_time'  => 'internal_part_time',
            'ichki_qo', 'internal_additional' => 'internal_additional',
            'tashqi',   'external_part_time'  => 'external_part_time',
            'soatbay',  'hourly'              => 'hourly',
            default                           => 'main_job',
        };
    }
}
