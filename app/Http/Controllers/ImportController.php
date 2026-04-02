<?php

namespace App\Http\Controllers;

use App\Imports\DirectionImport;
use App\Imports\GroupImport;
use App\Imports\SubjectImport;
use App\Imports\TeacherImport;
use App\Models\Department;
use App\Models\Direction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ImportController extends Controller
{
    /**
     * Import sahifasi
     * GET /admin/import
     */
    public function index()
    {
        $departments = Department::where('is_active', true)
            ->orderBy('name')->get(['id', 'name']);

        $directions = Direction::where('is_active', true)
            ->with('department:id,name')
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'department_id', 'degree_type', 'duration_years']);

        return Inertia::render('Admin/Import/Index', [
            'departments' => $departments,
            'directions'  => $directions->map(fn($d) => [
                'id'            => $d->id,
                'name'          => $d->name,
                'code'          => $d->code,
                'department'    => $d->department?->name,
                'degree_type'   => $d->degree_type,
                'duration_years'=> $d->duration_years,
            ]),
        ]);
    }

    // ─── Import ───────────────────────────────────────────────────────────────

    public function directions(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:5120']);
        return $this->runImport(new DirectionImport, $request->file('file'), "Yo'nalishlar");
    }

    public function groups(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:5120']);
        return $this->runImport(new GroupImport, $request->file('file'), 'Guruhlar');
    }

    public function teachers(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:5120']);
        return $this->runImport(new TeacherImport, $request->file('file'), "O'qituvchilar");
    }

    public function subjects(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:5120']);
        return $this->runImport(new SubjectImport, $request->file('file'), 'Fanlar');
    }

    // ─── Namuna Excel yuklab olish ────────────────────────────────────────────

    public function sampleDirections()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->pluck('name');

        return $this->buildSample(
            "Yo'nalishlar_namuna",
            ['nomi', 'kodi', 'kafedra', 'daraja', 'yillar'],
            ["Yo'nalish nomi *", "Yo'nalish kodi *", 'Kafedra nomi (quyidagilardan) *', 'bakalavr yoki magistratura *', "Ta'lim davomiyligi (yil) *"],
            ['Dasturiy injiniring', '60110100', $departments->first() ?? 'Kafedra nomi', 'bakalavr', '4'],
            'Mavjud kafedralar (kafedra ustuniga shu nomlarni yozing):',
            $departments->toArray()
        );
    }

    public function sampleGroups()
    {
        $directions = Direction::where('is_active', true)
            ->with('department:id,name')->orderBy('name')
            ->get(['id', 'name', 'code', 'department_id']);

        return $this->buildSample(
            'Guruhlar_namuna',
            ['nomi', 'kodi', 'yonalish_kodi', 'kurs', 'talabalar', 'talim_turi', 'til'],
            ['Guruh nomi *', 'Guruh kodi *', "Yo'nalish kodi (quyidagilardan) *", 'Kurs (1-6) *', 'Talabalar soni *', 'kunduzgi/sirtqi/kechki/masofaviy *', 'uzbek yoki russian *'],
            ['DI-101', 'DI-101', $directions->first()?->code ?? '60110100', '1', '25', 'kunduzgi', 'uzbek'],
            "Mavjud yo'nalishlar (yonalish_kodi ustuniga KODni yozing):",
            $directions->map(fn($d) => "  {$d->code}  |  {$d->name}  |  {$d->department?->name}")->toArray()
        );
    }

    public function sampleTeachers()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->pluck('name');

        return $this->buildSample(
            "O'qituvchilar_namuna",
            ['fish', 'email', 'kafedra', 'lavozim', 'ilmiy_daraja', 'ilmiy_unvon', 'ish_turi', 'parol'],
            ['F.I.Sh *', 'Email *', 'Kafedra nomi (quyidagilardan) *', 'Lavozim', 'Ilmiy daraja', 'Ilmiy unvon', 'asosiy/ichki/tashqi/soatbay', 'Kirish paroli (bo\'sh bo\'lsa password123)'],
            ['Aliyev Vohid Baxtiyorovich', 'aliyev@edu.uz', $departments->first() ?? 'Kafedra nomi', 'Dotsent', 'PhD', 'Dotsent', 'asosiy', 'Parol123!'],
            'Mavjud kafedralar (kafedra ustuniga shu nomlarni yozing):',
            $departments->toArray()
        );
    }

    public function sampleSubjects()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->pluck('name');
        $directions  = Direction::where('is_active', true)->orderBy('name')->get(['code', 'name']);

        $headers = [
            'nomi', 'kodi', 'kafedra', 'yonalish_kodi', 'kurs', 'kredit', 'turi',
            's1_maruza', 's1_amaliy', 's1_laboratoriya', 's1_seminar', 's1_amaliyot', 's1_imtihon', 's1_sinov',
            's2_maruza', 's2_amaliy', 's2_laboratoriya', 's2_seminar', 's2_amaliyot', 's2_imtihon', 's2_sinov',
            'kurs_ishi', 'diplom_ishi', 'konsultatsiya', 'potok_mumkin', 'min_guruhlar',
        ];
        $labels = [
            'Fan nomi *', 'Fan kodi *', 'Kafedra (quyidagilardan) *', "Yo'nalish kodi (ixtiyoriy)", 'Kurs', 'Kredit', 'majburiy/ixtiyoriy',
            "1-sem Ma'ruza", '1-sem Amaliy', '1-sem Lab', '1-sem Seminar', '1-sem Amaliyot', '1-sem Imtihon', '1-sem Sinov',
            "2-sem Ma'ruza", '2-sem Amaliy', '2-sem Lab', '2-sem Seminar', '2-sem Amaliyot', '2-sem Imtihon', '2-sem Sinov',
            'Kurs ishi', 'Diplom ishi', 'Konsultatsiya', 'Potok (1=ha, 0=yo\'q)', 'Min guruhlar',
        ];
        $example = [
            'Matematika', 'MAT101', $departments->first() ?? 'Kafedra', $directions->first()?->code ?? '',
            '1', '3', 'asosiy',
            '36', '18', '0', '0', '0', '0', '0',
            '0',  '0',  '0', '0', '0', '0', '0',
            '0', '0', '4', '1', '2',
        ];

        $refData = array_merge(
            ['── KAFEDRALAR ──'],
            $departments->toArray(),
            [''],
            ["── YO'NALISHLAR (kod | nom) ──"],
            $directions->map(fn($d) => "  {$d->code}  |  {$d->name}")->toArray()
        );

        return $this->buildSample('Fanlar_namuna', $headers, $labels, $example,
            'Ma\'lumotnoma (kafedra va yo\'nalish nomlari):', $refData);
    }

    // ─── Yordamchi metodlar ───────────────────────────────────────────────────

    private function runImport($import, $file, string $name)
    {
        try {
            Excel::import($import, $file);
            return back()->with('success', "{$name} muvaffaqiyatli import qilindi! ✅");
        } catch (\Exception $e) {
            return back()->with('error', 'Import amalga oshmadi: ' . $e->getMessage());
        }
    }

    private function buildSample(
        string $filename,
        array  $headers,
        array  $labels,
        array  $example,
        string $refTitle,
        array  $refData
    ) {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Import');
        $colCount = count($headers);
        $lastCol  = Coordinate::stringFromColumnIndex($colCount);

        // ── Qator 1: sarlavhalar (key) — import shu qatorni o'qiydi ───────
        foreach ($headers as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $h);
        }
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '3730A3']]],
        ]);

        // ── Qator 2: tavsif — import o'tkazib yuboradi (startRow=4) ───────
        foreach ($labels as $i => $l) {
            $sheet->setCellValueByColumnAndRow($i + 1, 2, $l);
        }
        $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280'], 'size' => 9],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
        ]);

        // ── Qator 3: misol — import o'tkazib yuboradi (startRow=4) ────────
        foreach ($example as $i => $v) {
            $sheet->setCellValueByColumnAndRow($i + 1, 3, $v);
        }
        $sheet->getStyle("A3:{$lastCol}3")->applyFromArray([
            'font' => ['color' => ['rgb' => '065F46'], 'bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D1FAE5']],
        ]);

        // ── Qator 4+ : foydalanuvchi o'z ma'lumotlarini shu yerdan yozadi ─
        for ($r = 4; $r <= 15; $r++) {
            $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
            ]);
        }

        // ── Ustun kengligi ─────────────────────────────────────────────────
        foreach (range(1, $colCount) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setWidth(22);
        }
        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getRowDimension(2)->setRowHeight(25);

        // ── Ma'lumotnoma varag'i: kafedralar, yo'nalishlar ─────────────────
        $ref = $spreadsheet->createSheet();
        $ref->setTitle("Ma'lumotnoma");
        $ref->getColumnDimension('A')->setWidth(60);

        $ref->setCellValue('A1', "⚠️ {$refTitle}");
        $ref->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '92400E']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']],
        ]);
        $ref->getRowDimension(1)->setRowHeight(28);

        foreach ($refData as $i => $rd) {
            $row = $i + 2;
            $ref->setCellValue("A{$row}", $rd);
            if (str_starts_with((string)$rd, '──')) {
                $ref->getStyle("A{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '4F46E5']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EEF2FF']],
                ]);
            }
        }

        $spreadsheet->setActiveSheetIndex(0);
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
