<?php

namespace App\Exports;

use App\Models\Teacher;
use App\Models\Workload;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TeacherReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    protected $teacherId;
    protected $semesterId;
    protected $teacher;
    protected $rowNumber = 0;

    public function __construct($teacherId, $semesterId = null)
    {
        $this->teacherId = $teacherId;
        $this->semesterId = $semesterId;
        $this->teacher = Teacher::with('user', 'department')->find($teacherId);
    }

    public function collection()
    {
        $this->teacher = Teacher::with('user', 'department')->findOrFail($this->teacherId);

        $query = Workload::where('teacher_id', $this->teacherId)
            ->with(['subject', 'group', 'semester']);

        if ($this->semesterId) {
            $query->where('semester_id', $this->semesterId);
        } else {
            $query->whereHas('semester', function ($q) {
                $q->where('is_current', true);
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            ['O\'QITUVCHI YUKLAMA HISOBOTI'],
            [''],
            ['O\'qituvchi:', $this->teacher->user->name ?? ''],
            ['Lavozim:', $this->teacher->position ?? ''],
            ['Kafedra:', $this->teacher->department->name ?? ''],
            [''],
            [
                '№',
                'Fan',
                'Guruh',
                'Ma\'ruza',
                'Seminar',
                'Amaliy',
                'Imtihon',
                'Jami'
            ]
        ];
    }

    public function map($workload): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            ($workload->subject->name ?? '') . "\n" . ($workload->subject->code ?? ''),
            $workload->group->name ?? '',
            $workload->lecture_hours ?? 0,
            $workload->seminar_hours ?? 0,
            $workload->practical_hours ?? 0,
            $workload->exam_hours ?? 0,
            $workload->total_hours ?? 0,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        // Title style
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // ✅ Info qatorlarini merge qilish
        $sheet->mergeCells('B3:H3');
        $sheet->mergeCells('B4:H4');
        $sheet->mergeCells('B5:H5');

        // Info section style
        $sheet->getStyle('A3:A5')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);

        // Header style
        $sheet->getStyle('A7:H7')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // ✅ Ma'lumotlar uchun wrap text va alignment
        if ($lastRow > 7) {
            $sheet->getStyle("B8:B{$lastRow}")->applyFromArray([
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => Alignment::VERTICAL_TOP,
                ],
            ]);
            
            // ✅ Raqamlar uchun o'ng tomonga tekislash
            $sheet->getStyle("D8:H{$lastRow}")->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ]);
        }

        // Data borders
        $sheet->getStyle("A7:H{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // ✅ JAMI qatorini qo'shish
        $totalRow = $lastRow + 1;
        $dataStartRow = 8;

        $sheet->setCellValue("A{$totalRow}", 'JAMI:');
        $sheet->mergeCells("A{$totalRow}:C{$totalRow}");
        $sheet->setCellValue("D{$totalRow}", "=SUM(D{$dataStartRow}:D{$lastRow})");
        $sheet->setCellValue("E{$totalRow}", "=SUM(E{$dataStartRow}:E{$lastRow})");
        $sheet->setCellValue("F{$totalRow}", "=SUM(F{$dataStartRow}:F{$lastRow})");
        $sheet->setCellValue("G{$totalRow}", "=SUM(G{$dataStartRow}:G{$lastRow})");
        $sheet->setCellValue("H{$totalRow}", "=SUM(H{$dataStartRow}:H{$lastRow})");

        // Total row style
        $sheet->getStyle("A{$totalRow}:H{$totalRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E7FF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        return [];
    }

    public function title(): string
    {
        return 'O\'qituvchi hisoboti';
    }
}