<?php

namespace App\Exports;

use App\Models\AcademicYear;
use App\Models\Teacher;
use App\Models\Workload;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TeacherReportExport
{
    public function __construct(
        private int $teacherId,
        private int $academicYearId
    ) {}

    public function download(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $spreadsheet = $this->build();
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'oqituvchi_hisoboti.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function build(): Spreadsheet
    {
        $teacher     = Teacher::with(['user:id,name', 'department:id,name'])->findOrFail($this->teacherId);
        $academicYear= AcademicYear::findOrFail($this->academicYearId);

        $workloads = Workload::where('teacher_id', $this->teacherId)
            ->where('academic_year_id', $this->academicYearId)
            ->with([
                'subject:id,name',
                'direction:id,name',
                'groups:id,name,course,student_count',
            ])
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("O'qituvchi hisoboti");

        // ── Sarlavha ───────────────────────────────────────────────────────────
        $sheet->setCellValue('A1', $teacher->user?->name . ' — ' . $academicYear->name . " o'quv yili yuklamasi");
        $sheet->mergeCells('A1:V1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDEEFF']],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // O'qituvchi ma'lumotlari
        $sheet->setCellValue('A2', 'Kafedra:');
        $sheet->setCellValue('B2', $teacher->department?->name ?? '—');
        $sheet->setCellValue('A3', 'Lavozim:');
        $sheet->setCellValue('B3', $teacher->position ?? '—');
        $sheet->setCellValue('A4', 'Daraja:');
        $sheet->setCellValue('B4', ($teacher->academic_degree ?? '') . ' ' . ($teacher->academic_title ?? ''));
        $sheet->getStyle('A2:A4')->getFont()->setBold(true);

        // ── Ustun sarlavhalari ─────────────────────────────────────────────────
        $headers = [
            'A' => "FAN NOMI",
            'B' => "YO'NALISH",
            'C' => "GURUHLAR",
            'D' => "KURS",
            'E' => "TALABA",
            // 1-semestr
            'F' => "MA'RUZA",
            'G' => "AMALIY",
            'H' => "LAB",
            'I' => "SEMINAR",
            'J' => "AMALIYOT",
            // 2-semestr
            'K' => "MA'RUZA",
            'L' => "AMALIY",
            'M' => "LAB",
            'N' => "SEMINAR",
            'O' => "AMALIYOT",
            // Extra
            'P' => "KURS ISHI",
            'Q' => "DIPLOM",
            'R' => "KONSUL.",
            'S' => "REYTING",
            'T' => "JAMI",
            'U' => "HOLAT",
        ];

        // Semestr header
        $sheet->setCellValue('F5', "1-SEMESTR");
        $sheet->mergeCells('F5:J5');
        $sheet->setCellValue('K5', "2-SEMESTR");
        $sheet->mergeCells('K5:O5');
        $sheet->getStyle('F5:O5')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E74B5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        foreach ($headers as $col => $label) {
            $sheet->setCellValue($col . '6', $label);
        }
        $sheet->getStyle('A6:U6')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F3864']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
        ]);
        $sheet->getRowDimension(6)->setRowHeight(35);

        // ── Ma'lumotlar ────────────────────────────────────────────────────────
        $dataRow = 7;
        foreach ($workloads as $w) {
            $groups = $w->groups;
            $values = [
                'A' => $w->subject?->name ?? '—',
                'B' => $w->direction?->name ?? '—',
                'C' => $groups->pluck('name')->join(', '),
                'D' => $groups->first()?->course ?? '',
                'E' => $w->total_students ?? $groups->sum('student_count'),
                'F' => $w->semester_1_lecture ?: 0,
                'G' => $w->semester_1_practical ?: 0,
                'H' => $w->semester_1_laboratory ?: 0,
                'I' => $w->semester_1_seminar ?: 0,
                'J' => $w->semester_1_practice ?: 0,
                'K' => $w->semester_2_lecture ?: 0,
                'L' => $w->semester_2_practical ?: 0,
                'M' => $w->semester_2_laboratory ?: 0,
                'N' => $w->semester_2_seminar ?: 0,
                'O' => $w->semester_2_practice ?: 0,
                'P' => $w->coursework_hours ?: 0,
                'Q' => $w->diploma_hours ?: 0,
                'R' => $w->consultation_hours ?: 0,
                'S' => round($w->rating ?? 0, 1),
                'T' => round($w->total_hours, 1),
                'U' => match($w->status) {
                    'confirmed' => 'Tasdiqlangan',
                    'pending'   => 'Tekshiruvda',
                    'draft'     => 'Qoralama',
                    default     => $w->status,
                },
            ];

            foreach ($values as $col => $val) {
                $sheet->setCellValue($col . $dataRow, $val);
            }

            $bgColor = $dataRow % 2 === 0 ? 'F2F7FF' : 'FFFFFF';
            $sheet->getStyle("A{$dataRow}:U{$dataRow}")->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
            ]);
            $dataRow++;
        }

        // ── Jami ──────────────────────────────────────────────────────────────
        $sheet->setCellValue("A{$dataRow}", 'JAMI');
        $sumCols = ['F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T'];
        foreach ($sumCols as $col) {
            $sheet->setCellValue("{$col}{$dataRow}", "=SUM({$col}7:{$col}" . ($dataRow-1) . ")");
        }
        $sheet->getStyle("A{$dataRow}:U{$dataRow}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF2CC']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '999999']]],
        ]);

        // ── Ustun kengligi ─────────────────────────────────────────────────────
        $widths = ['A'=>28,'B'=>18,'C'=>20,'D'=>5,'E'=>7,
            'F'=>8,'G'=>8,'H'=>7,'I'=>7,'J'=>8,
            'K'=>8,'L'=>8,'M'=>7,'N'=>7,'O'=>8,
            'P'=>8,'Q'=>8,'R'=>7,'S'=>8,'T'=>10,'U'=>12];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $sheet->setSelectedCell('A1');
        return $spreadsheet;
    }
}
