<?php

namespace App\Exports;

use App\Models\AcademicYear;
use App\Models\Faculty;
use App\Models\Workload;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class FacultyReportExport
{
    public function __construct(
        private int $facultyId,
        private int $academicYearId
    ) {}

    public function download(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $spreadsheet = $this->build();
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'fakultet_hisoboti.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function build(): Spreadsheet
    {
        $faculty     = Faculty::findOrFail($this->facultyId);
        $academicYear= AcademicYear::findOrFail($this->academicYearId);

        $workloads = Workload::where('academic_year_id', $this->academicYearId)
            ->where('status', 'confirmed')
            ->whereHas('department', fn($q) => $q->where('faculty_id', $this->facultyId))
            ->with([
                'teacher.user:id,name',
                'subject:id,name',
                'direction:id,name',
                'groups:id,name,course,student_count',
            ])
            ->orderBy('teacher_id')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('YUKLAMA ' . $academicYear->name);

        // ── Sarlavha (0-qator) ────────────────────────────────────────────────
        $sheet->setCellValue('A1', "REJA (Ikki semestr uchun)");
        $sheet->mergeCells('A1:AJ1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDEEFF']],
        ]);

        // ── Ustun sarlavhalari (1-qator) ──────────────────────────────────────
        $headers = [
            'A' => "O'QITUVCHI",
            'B' => "FAN NOMI",
            'C' => "TA'LIM YO'NALISHI",
            'D' => "GURUHLAR",
            'E' => "KURS",
            'F' => "GURUH SONI",
            'G' => "TALABA SONI",
            'H' => "POTOK",
            // 1-semestr
            'I' => "MA'RUZA",
            'J' => "AMALIY",
            'K' => "LAB",
            'L' => "SEMINAR",
            'M' => "AMALIYOT",
            'N' => "1-SEM JAMI",
            // 2-semestr
            'O' => "MA'RUZA",
            'P' => "AMALIY",
            'Q' => "LAB",
            'R' => "SEMINAR",
            'S' => "AMALIYOT",
            'T' => "2-SEM JAMI",
            // Qo'shimcha
            'U' => "MA'RUZA JAMI",
            'V' => "AMALIY JAMI",
            'W' => "KURS ISHI",
            'X' => "BMI",
            'Y' => "REYTING",
            'Z' => "JAMI Auditor soat",
            'AA'=> "SHTAT JAMI",
            'AB'=> "SOAT",
            'AC'=> "STAVKA",
            'AD'=> "SOATBAY",
        ];

        foreach ($headers as $col => $label) {
            $sheet->setCellValue($col . '2', $label);
        }

        $sheet->getStyle('A2:AD2')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F3864']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(40);

        // ── Ma'lumotlar ────────────────────────────────────────────────────────
        $grouped  = $workloads->groupBy('teacher_id');
        $dataRow  = 3;

        foreach ($grouped as $teacherId => $teacherWorkloads) {
            $firstRow = true;
            $teacher  = $teacherWorkloads->first()->teacher;

            foreach ($teacherWorkloads as $w) {
                $groups     = $w->groups;
                $groupNames = $groups->pluck('name')->implode("\n");
                $course     = $groups->first()?->course ?? '';
                $groupCount = $groups->count();
                $students   = $w->total_students ?? $groups->sum('student_count');

                $s1Jami = $w->semester_1_lecture + $w->semester_1_practical +
                    $w->semester_1_laboratory + $w->semester_1_seminar + $w->semester_1_practice;
                $s2Jami = $w->semester_2_lecture + $w->semester_2_practical +
                    $w->semester_2_laboratory + $w->semester_2_seminar + $w->semester_2_practice;
                $maruzaJami = $w->semester_1_lecture + $w->semester_2_lecture;
                $amaliyJami = $w->semester_1_practical + $w->semester_2_practical;

                $values = [
                    'A'  => $firstRow ? ($teacher->user?->name ?? '—') : '',
                    'B'  => $w->subject?->name ?? '—',
                    'C'  => $w->direction?->name ?? '—',
                    'D'  => $groupNames,
                    'E'  => $course,
                    'F'  => $groupCount ?: '',
                    'G'  => $students ?: '',
                    'H'  => $w->is_potok ? $w->potok_code ?? 'P' : '',
                    'I'  => $w->semester_1_lecture ?: '',
                    'J'  => $w->semester_1_practical ?: '',
                    'K'  => $w->semester_1_laboratory ?: '',
                    'L'  => $w->semester_1_seminar ?: '',
                    'M'  => $w->semester_1_practice ?: '',
                    'N'  => round($s1Jami, 1) ?: '',
                    'O'  => $w->semester_2_lecture ?: '',
                    'P'  => $w->semester_2_practical ?: '',
                    'Q'  => $w->semester_2_laboratory ?: '',
                    'R'  => $w->semester_2_seminar ?: '',
                    'S'  => $w->semester_2_practice ?: '',
                    'T'  => round($s2Jami, 1) ?: '',
                    'U'  => round($maruzaJami, 1) ?: '',
                    'V'  => round($amaliyJami, 1) ?: '',
                    'W'  => $w->coursework_hours ?: '',
                    'X'  => $w->diploma_hours ?: '',
                    'Y'  => round($w->rating ?? 0, 1) ?: '',
                    'Z'  => round($w->total_hours, 1),
                    'AA' => '',  // Shtat jami — keyinchalik hisob
                    'AB' => '',  // Soat
                    'AC' => '',  // Stavka
                    'AD' => $teacher->employment_type === 'hourly' ? 'soatbay' : '',
                ];

                foreach ($values as $col => $val) {
                    $sheet->setCellValue($col . $dataRow, $val);
                }

                // Styling
                $bgColor = $dataRow % 2 === 0 ? 'F8FBFF' : 'FFFFFF';
                $sheet->getStyle("A{$dataRow}:AD{$dataRow}")->applyFromArray([
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // Ko'p qatorli matn uchun wrap
                $sheet->getStyle("D{$dataRow}")->getAlignment()->setWrapText(true);
                $sheet->getRowDimension($dataRow)->setRowHeight(max(15, $groups->count() * 13));

                $firstRow = false;
                $dataRow++;
            }
        }

        // ── Jami ──────────────────────────────────────────────────────────────
        $sheet->setCellValue("A{$dataRow}", 'JAMI');
        $sumCols = ['I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        foreach ($sumCols as $col) {
            $sheet->setCellValue("{$col}{$dataRow}", "=SUM({$col}3:{$col}" . ($dataRow-1) . ")");
        }
        $sheet->getStyle("A{$dataRow}:AD{$dataRow}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF2CC']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '999999']]],
        ]);

        // ── Ustun kengligi ─────────────────────────────────────────────────────
        $widths = [
            'A'=>28, 'B'=>28, 'C'=>20, 'D'=>20, 'E'=>6, 'F'=>8, 'G'=>8, 'H'=>8,
            'I'=>8, 'J'=>8, 'K'=>8, 'L'=>8, 'M'=>8, 'N'=>10,
            'O'=>8, 'P'=>8, 'Q'=>8, 'R'=>8, 'S'=>8, 'T'=>10,
            'U'=>10, 'V'=>10, 'W'=>8, 'X'=>8, 'Y'=>8, 'Z'=>12,
            'AA'=>10, 'AB'=>8, 'AC'=>8, 'AD'=>10,
        ];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $sheet->setSelectedCell('A1');
        return $spreadsheet;
    }
}
