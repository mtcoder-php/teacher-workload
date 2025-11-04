<?php

namespace App\Exports;

use App\Models\Faculty;
use App\Models\Department;
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

class FacultyReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    protected $facultyId;
    protected $semesterId;
    protected $faculty;
    protected $rowNumber = 0;

    public function __construct($facultyId, $semesterId = null)
    {
        $this->facultyId = $facultyId;
        $this->semesterId = $semesterId;
        $this->faculty = Faculty::with('dean')->find($facultyId);
    }


    public function collection()
    {

       

        $query = Department::where('faculty_id', $this->facultyId)
            ->with(['head', 'teachers' => function ($query) {
                $query->where('is_active', true)
                    ->with(['workloads' => function ($q) {
                        if ($this->semesterId) {
                            $q->where('semester_id', $this->semesterId);
                        } else {
                            $q->whereHas('semester', function ($sq) {
                                $sq->where('is_current', true);
                            });
                        }
                    }]);
            }])
            ->where('is_active', true);

        return $query->get();
    }

    public function headings(): array
    {
        return [
            ['FAKULTET YUKLAMA HISOBOTI'],
            [''],
            ['Fakultet:', $this->faculty->name ?? ''],
            ['Kod:', $this->faculty->code ?? ''],
            ['Dekan:', $this->faculty->dean ? $this->faculty->dean->name : ''],
            [''],
            [
                '№',
                'Kafedra',
                'Mudiri',
                'O\'qituvchilar soni',
                'Jami soat',
                'Akademik soat'
            ]
        ];
    }

    public function map($department): array
    {
        $this->rowNumber++;

        $teachersCount = $department->teachers->count();
        $totalHours = 0;

        foreach ($department->teachers as $teacher) {
            $totalHours += $teacher->workloads->sum('total_hours');
        }

        $averageHours = $teachersCount > 0 ? round($totalHours / $teachersCount, 2) : 0;

        return [
            $this->rowNumber,
            $department->name ?? '',
            $department->head ? $department->head->name : '',
            $teachersCount,
            $totalHours,
            $totalHours / 2,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        // Title style
        $sheet->mergeCells('A1:F1');
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

        // Info section style
        $sheet->getStyle('A3:B5')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);

        // Header style
        $sheet->getStyle('A7:F7')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '8B5CF6'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Data borders
        $sheet->getStyle("A7:F{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Calculate totals row
        $totalRow = $lastRow + 1;
        $dataStartRow = 8; // First data row after header

        $sheet->setCellValue("A{$totalRow}", 'JAMI:');
        $sheet->mergeCells("A{$totalRow}:C{$totalRow}");
        $sheet->setCellValue("D{$totalRow}", "=SUM(D{$dataStartRow}:D{$lastRow})");
        $sheet->setCellValue("E{$totalRow}", "=SUM(E{$dataStartRow}:E{$lastRow})");
        $sheet->setCellValue("F{$totalRow}", "=AVERAGE(F{$dataStartRow}:F{$lastRow})");

        // Total row style
        $sheet->getStyle("A{$totalRow}:F{$totalRow}")->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EDE9FE'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
        ]);

        return [];
    }

    public function title(): string
    {
        return 'Fakultet hisoboti';
    }
}
