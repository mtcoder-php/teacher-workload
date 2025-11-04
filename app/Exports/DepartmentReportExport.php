<?php

namespace App\Exports;

use App\Models\Department;
use App\Models\Teacher;
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

class DepartmentReportExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    protected $departmentId;
    protected $semesterId;
    protected $department;
    protected $rowNumber = 0;

    public function __construct($departmentId, $semesterId = null)
    {
        $this->departmentId = $departmentId;
        $this->semesterId = $semesterId;
        
        // ✅ Department ma'lumotini constructorda yuklash
        $this->department = Department::with(['faculty', 'head'])
            ->findOrFail($this->departmentId);
    }

    public function collection()
    {
        // ✅ Department allaqachon yuklangan, faqat o'qituvchilarni olish
        $query = Teacher::where('department_id', $this->departmentId)
            ->with(['user', 'workloads' => function ($query) {
                if ($this->semesterId) {
                    $query->where('semester_id', $this->semesterId);
                } else {
                    $query->whereHas('semester', function ($q) {
                        $q->where('is_current', true);
                    });
                }
            }])
            ->where('is_active', true);

        return $query->get();
    }

    public function headings(): array
    {
        // ✅ Xavfsiz usul bilan head nomini olish
        $headName = $this->department->head?->name ?? 'Belgilanmagan';
        
        return [
            ['KAFEDRA YUKLAMA HISOBOTI'],
            [''],
            ['Kafedra:', $this->department->name ?? ''],
            ['Fakultet:', $this->department->faculty?->name ?? ''],
            ['Mudiri:', $headName],
            [''],
            [
                '№',
                'O\'qituvchi',
                'Lavozim',
                'Yuklamalar soni',
                'Ma\'ruza',
                'Seminar',
                'Amaliy',
                'Jami soat'
            ]
        ];
    }

    public function map($teacher): array
    {
        $this->rowNumber++;
        
        return [
            $this->rowNumber,
            $teacher->user->name ?? '',
            $teacher->position ?? '',
            $teacher->workloads->count(),
            $teacher->workloads->sum('lecture_hours'),
            $teacher->workloads->sum('seminar_hours'),
            $teacher->workloads->sum('practical_hours'),            
            $teacher->workloads->sum('total_hours'),
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
                'startColor' => ['rgb' => '10B981'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Data borders
        $sheet->getStyle("A7:H{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Calculate totals row
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
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D1FAE5'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
        ]);

        return [];
    }

    public function title(): string
    {
        return 'Kafedra hisoboti';
    }
}