<?php

namespace App\Services;

use App\Models\Workload;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFile;
use Exception;

class ExportService
{
    /**
     * Yuklamalarni CSV ga export qilish
     */
    public static function toCSV(array $filters = []): string
    {
        try {
            $workloads = self::getFilteredWorkloads($filters);
            $filename = 'workloads_' . date('Y-m-d_H-i-s') . '.csv';

            // CSV content yaratish
            $csv = self::generateCSV($workloads);

            // File saqlash
            $path = storage_path('exports/' . $filename);
            file_put_contents($path, $csv);

            return $path;
        } catch (Exception $e) {
            throw new Exception("CSV export error: {$e->getMessage()}");
        }
    }

    /**
     * Yuklamalarni Excel ga export qilish
     */
    public static function toExcel(array $filters = []): string
    {
        try {
            $workloads = self::getFilteredWorkloads($filters);
            $filename = 'workloads_' . date('Y-m-d_H-i-s') . '.xlsx';

            $data = $workloads->map(function ($workload) {
                return [
                    'ID' => $workload->id,
                    'O\'qituvchi' => $workload->teacher->user->name,
                    'Fan' => $workload->subject->name,
                    'Yo\'nalish' => $workload->direction->name,
                    'O\'quv yili' => $workload->academicYear->name,
                    'Ma\'ruza (1-sem)' => $workload->semester_1_lecture,
                    'Amaliy (1-sem)' => $workload->semester_1_practical,
                    'Laboratoriya (1-sem)' => $workload->semester_1_laboratory,
                    'Ma\'ruza (2-sem)' => $workload->semester_2_lecture,
                    'Amaliy (2-sem)' => $workload->semester_2_practical,
                    'Laboratoriya (2-sem)' => $workload->semester_2_laboratory,
                    'Jami soatlar' => $workload->total_hours,
                    'Talabalar soni' => $workload->total_students,
                    'Status' => $workload->status_label,
                    'Tasdiqlandi' => $workload->approved_at?->format('Y-m-d H:i:s') ?? '-',
                ];
            });

            // Excel ga export qilish
            $path = storage_path('exports/' . $filename);
            Excel::store(
                new WorkloadExport($data),
                'exports/' . $filename
            );

            return $path;
        } catch (Exception $e) {
            throw new Exception("Excel export error: {$e->getMessage()}");
        }
    }

    /**
     * Yuklamalarni JSON ga export qilish
     */
    public static function toJSON(array $filters = []): string
    {
        try {
            $workloads = self::getFilteredWorkloads($filters);
            $filename = 'workloads_' . date('Y-m-d_H-i-s') . '.json';

            $data = $workloads->map(function ($workload) {
                return [
                    'id' => $workload->id,
                    'teacher' => $workload->teacher->user->name,
                    'subject' => $workload->subject->name,
                    'direction' => $workload->direction->name,
                    'academic_year' => $workload->academicYear->name,
                    'hours' => [
                        'semester_1' => [
                            'lecture' => $workload->semester_1_lecture,
                            'practical' => $workload->semester_1_practical,
                            'laboratory' => $workload->semester_1_laboratory,
                            'total' => $workload->semester_1_total,
                        ],
                        'semester_2' => [
                            'lecture' => $workload->semester_2_lecture,
                            'practical' => $workload->semester_2_practical,
                            'laboratory' => $workload->semester_2_laboratory,
                            'total' => $workload->semester_2_total,
                        ],
                        'total' => $workload->total_hours,
                    ],
                    'students' => $workload->total_students,
                    'status' => $workload->status,
                    'approved_at' => $workload->approved_at,
                ];
            })->toArray();

            $path = storage_path('exports/' . $filename);
            file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return $path;
        } catch (Exception $e) {
            throw new Exception("JSON export error: {$e->getMessage()}");
        }
    }

    /**
     * Yuklamalarni PDF ga export qilish
     */
    public static function toPDF(array $filters = []): string
    {
        try {
            $workloads = self::getFilteredWorkloads($filters);
            $filename = 'workloads_' . date('Y-m-d_H-i-s') . '.pdf';

            // Dompdf yordamida PDF yaratish
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('workloads.export.pdf', [
                'workloads' => $workloads,
            ]);

            $path = storage_path('exports/' . $filename);
            $pdf->save($path);

            return $path;
        } catch (Exception $e) {
            throw new Exception("PDF export error: {$e->getMessage()}");
        }
    }

    /**
     * Filtered workloads olish
     */
    private static function getFilteredWorkloads(array $filters = []): Collection
    {
        $query = Workload::with([
            'teacher.user',
            'subject',
            'direction',
            'academicYear',
        ]);

        if (isset($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }

        if (isset($filters['teacher_id'])) {
            $query->where('teacher_id', $filters['teacher_id']);
        }

        if (isset($filters['direction_id'])) {
            $query->where('direction_id', $filters['direction_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * CSV content yaratish
     */
    private static function generateCSV(Collection $workloads): string
    {
        $csv = "ID,O'qituvchi,Fan,Yo'nalish,O'quv yili,Ma'ruza (1-sem),Amaliy (1-sem),Laboratoriya (1-sem),Ma'ruza (2-sem),Amaliy (2-sem),Laboratoriya (2-sem),Jami soatlar,Talabalar soni,Status,Tasdiqlandi\n";

        foreach ($workloads as $workload) {
            $csv .= sprintf(
                '%d,%s,%s,%s,%s,%.2f,%.2f,%.2f,%.2f,%.2f,%.2f,%.2f,%d,%s,%s' . "\n",
                $workload->id,
                $workload->teacher->user->name,
                $workload->subject->name,
                $workload->direction->name,
                $workload->academicYear->name,
                $workload->semester_1_lecture,
                $workload->semester_1_practical,
                $workload->semester_1_laboratory,
                $workload->semester_2_lecture,
                $workload->semester_2_practical,
                $workload->semester_2_laboratory,
                $workload->total_hours,
                $workload->total_students,
                $workload->status_label,
                $workload->approved_at?->format('Y-m-d H:i:s') ?? '-'
            );
        }

        return $csv;
    }
}

?>