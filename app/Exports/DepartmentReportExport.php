<?php

namespace App\Exports;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Teacher;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DepartmentReportExport
{
    public function __construct(
        private int $departmentId,
        private int $academicYearId
    ) {}

    public function download(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $spreadsheet = $this->build();
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'kafedra_hisoboti.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function build(): Spreadsheet
    {
        $department  = Department::with('faculty')->findOrFail($this->departmentId);
        $academicYear= AcademicYear::findOrFail($this->academicYearId);

        $teachers = Teacher::where('department_id', $this->departmentId)
            ->where('is_active', true)
            ->with([
                'user:id,name',
                'workloads' => fn($q) => $q
                    ->where('academic_year_id', $this->academicYearId)
                    ->where('status', 'confirmed'),
            ])
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Kafedra hisoboti');

        // ── Sarlavha ───────────────────────────────────────────────────────────
        $title = "{$department->name} {$academicYear->name} o'quv yili uchun Professor-o'qituvchilarning\nO'QUV YUKLAMASI\n" . now()->format('d.m.Y') . ' holati';
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:N1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(50);

        // ── Ustun sarlavhalari ─────────────────────────────────────────────────
        $headers = [
            'A' => "T/r",
            'B' => "F.I.O",
            'C' => "Shtat birligi",
            'D' => "Stavka",
            'E' => "Lavozimi",
            'F' => "Darajasi",
            'G' => "Auditoriya soati",
            'H' => "Ma'ruza",
            'I' => "Amaliy",
            'J' => "Seminar",
            'K' => "Amaliyot",
            'L' => "Kurs ishi",
            'M' => "Reyting",
            'N' => "Umumiy yuklama",
        ];

        foreach ($headers as $col => $label) {
            $sheet->setCellValue($col . '2', $label);
        }

        $sheet->getStyle('A2:N2')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F3864']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(35);

        // ── Ma'lumotlar ────────────────────────────────────────────────────────
        $row = 3;
        $num = 1;

        foreach ($teachers as $teacher) {
            $w = $teacher->workloads;

            $auditoria = $w->sum(fn($x) =>
                $x->semester_1_lecture + $x->semester_1_practical +
                $x->semester_1_laboratory + $x->semester_1_seminar +
                $x->semester_2_lecture + $x->semester_2_practical +
                $x->semester_2_laboratory + $x->semester_2_seminar
            );
            $lecture   = $w->sum(fn($x) => $x->semester_1_lecture + $x->semester_2_lecture);
            $practical = $w->sum(fn($x) => $x->semester_1_practical + $x->semester_2_practical);
            $seminar   = $w->sum(fn($x) => $x->semester_1_seminar + $x->semester_2_seminar);
            $practice  = $w->sum(fn($x) => $x->semester_1_practice + $x->semester_2_practice);
            $coursework= $w->sum('coursework_hours');
            $rating    = $w->sum('rating');
            $total     = $w->sum('total_hours');

            $stavka = match($teacher->employment_type) {
                'main_job'           => '1.0',
                'internal_part_time' => '0.5',
                'external_part_time' => '0.5',
                'hourly'             => '—',
                default              => '0.5',
            };

            $data = [
                $num++,
                $teacher->user?->name ?? '—',
                $teacher->employmentTypeName,
                $stavka,
                $teacher->position ?? '',
                $teacher->academic_degree ?? '',
                round($auditoria, 1) ?: 0,
                round($lecture, 1)   ?: 0,
                round($practical, 1) ?: 0,
                round($seminar, 1)   ?: 0,
                round($practice, 1)  ?: 0,
                round($coursework,1) ?: 0,
                round($rating, 1)    ?: 0,
                round($total, 1)     ?: 0,
            ];

            $colLetter = 'A';
            foreach ($data as $val) {
                $sheet->setCellValue($colLetter . $row, $val);
                $colLetter++;
            }

            // Alternating row color
            $bgColor = $row % 2 === 0 ? 'F2F7FF' : 'FFFFFF';
            $sheet->getStyle("A{$row}:N{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
            ]);

            $row++;
        }

        // ── Jami qatori ────────────────────────────────────────────────────────
        $dataStart = 3;
        $dataEnd   = $row - 1;

        $sheet->setCellValue("E{$row}", 'Umumiy JAMI');
        $totalsMap = ['G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'];
        foreach ($totalsMap as $col) {
            $sheet->setCellValue("{$col}{$row}", "=SUM({$col}{$dataStart}:{$col}{$dataEnd})");
        }

        $sheet->getStyle("A{$row}:N{$row}")->applyFromArray([
            'font'  => ['bold' => true],
            'fill'  => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF2CC']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
        ]);

        // ── Chegara va ustun kengligi ──────────────────────────────────────────
        $sheet->getStyle("A2:N{$row}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
        ]);

        $widths = ['A'=>5, 'B'=>30, 'C'=>16, 'D'=>8, 'E'=>20, 'F'=>12,
            'G'=>14, 'H'=>12, 'I'=>10, 'J'=>10, 'K'=>12, 'L'=>10, 'M'=>10, 'N'=>14];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        // Raqam ustunlariga number format
        foreach (['G','H','I','J','K','L','M','N'] as $col) {
            $sheet->getStyle("{$col}3:{$col}{$row}")
                ->getNumberFormat()->setFormatCode('0.##');
        }

        $sheet->setSelectedCell('A1');
        return $spreadsheet;
    }
}
