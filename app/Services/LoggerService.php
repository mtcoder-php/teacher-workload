<?php

namespace App\Services;

use App\Models\Workload;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Exception;

class LoggerService
{
    /**
     * Log levels
     */
    const LEVEL_INFO = 'info';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR = 'error';
    const LEVEL_DEBUG = 'debug';

    /**
     * Action types
     */
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_APPROVE = 'approve';
    const ACTION_REJECT = 'reject';
    const ACTION_RESTORE = 'restore';

    /**
     * Asosiy logging method
     */
    public static function log(
        string $action,
        Workload $workload,
        ?User $user = null,
        array $changes = [],
        string $level = self::LEVEL_INFO
    ): void {
        try {
            $message = self::buildMessage($action, $workload, $user, $changes);

            Log::channel('workload')->{$level}($message, [
                'workload_id' => $workload->id,
                'subject_id' => $workload->subject_id,
                'teacher_id' => $workload->teacher_id,
                'user_id' => $user?->id,
                'action' => $action,
                'changes' => $changes,
                'timestamp' => now()->toDateTimeString(),
            ]);

            // Activity package bilan (agar o'rnatilgan bo'lsa)
            if (class_exists('Spatie\ActivityLog\Facades\Activity')) {
                activity('workload')
                    ->performedOn($workload)
                    ->causedBy($user)
                    ->withProperties($changes)
                    ->log($message);
            }
        } catch (Exception $e) {
            Log::error("Logging error: {$e->getMessage()}");
        }
    }

    /**
     * Yuklama yaratilganda log
     */
    public static function logCreated(Workload $workload, User $user): void
    {
        self::log(
            self::ACTION_CREATE,
            $workload,
            $user,
            $workload->toArray(),
            self::LEVEL_INFO
        );
    }

    /**
     * Yuklama yangilanganda log
     */
    public static function logUpdated(Workload $workload, User $user, array $changes): void
    {
        self::log(
            self::ACTION_UPDATE,
            $workload,
            $user,
            $changes,
            self::LEVEL_INFO
        );
    }

    /**
     * Yuklama o'chirilganda log
     */
    public static function logDeleted(Workload $workload, User $user): void
    {
        self::log(
            self::ACTION_DELETE,
            $workload,
            $user,
            $workload->toArray(),
            self::LEVEL_WARNING
        );
    }

    /**
     * Yuklama tasdiqlanganda log
     */
    public static function logApproved(Workload $workload, User $approver): void
    {
        self::log(
            self::ACTION_APPROVE,
            $workload,
            $approver,
            ['approved_by' => $approver->name, 'approved_at' => now()],
            self::LEVEL_INFO
        );
    }

    /**
     * Yuklama rad etilganda log
     */
    public static function logRejected(Workload $workload, User $rejector, string $reason): void
    {
        self::log(
            self::ACTION_REJECT,
            $workload,
            $rejector,
            ['rejected_by' => $rejector->name, 'reason' => $reason],
            self::LEVEL_WARNING
        );
    }

    /**
     * Yuklama qaytarilganda log (restore)
     */
    public static function logRestored(Workload $workload, User $user): void
    {
        self::log(
            self::ACTION_RESTORE,
            $workload,
            $user,
            ['restored_by' => $user->name],
            self::LEVEL_INFO
        );
    }

    /**
     * Message yaratish
     */
    private static function buildMessage(string $action, Workload $workload, ?User $user = null, array $changes = []): string
    {
        $userName = $user?->name ?? 'System';
        $subjectName = $workload->subject->name;
        $teacherName = $workload->teacher->user->name;

        return match ($action) {
            self::ACTION_CREATE => "Yuklama yaratildi: {$subjectName} ({$teacherName}) - User: {$userName}",
            self::ACTION_UPDATE => "Yuklama yangilandi: {$subjectName} ({$teacherName}) - User: {$userName}",
            self::ACTION_DELETE => "Yuklama o'chirildi: {$subjectName} ({$teacherName}) - User: {$userName}",
            self::ACTION_APPROVE => "Yuklama tasdiqlandi: {$subjectName} ({$teacherName}) - Tasdiqlovchi: {$userName}",
            self::ACTION_REJECT => "Yuklama rad etildi: {$subjectName} ({$teacherName}) - User: {$userName}",
            self::ACTION_RESTORE => "Yuklama qaytarildi: {$subjectName} ({$teacherName}) - User: {$userName}",
            default => "Yuklama operatsiyasi: {$action} - {$subjectName} ({$teacherName})",
        };
    }

    /**
     * Validation errorlarini log qilish
     */
    public static function logValidationError(int $subjectId, int $academicYearId, array $errors, ?User $user = null): void
    {
        Log::channel('workload')->warning('Validation error in workload creation', [
            'subject_id' => $subjectId,
            'academic_year_id' => $academicYearId,
            'errors' => $errors,
            'user_id' => $user?->id,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Bulk operatsiyalarni log qilish
     */
    public static function logBulkOperation(string $action, int $count, ?User $user = null): void
    {
        Log::channel('workload')->info("Bulk {$action} operation", [
            'action' => $action,
            'count' => $count,
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Export operatsiyalarni log qilish
     */
    public static function logExport(string $format, int $count, ?User $user = null): void
    {
        Log::channel('workload')->info("Workloads exported to {$format}", [
            'format' => $format,
            'count' => $count,
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Import operatsiyalarni log qilish
     */
    public static function logImport(int $successCount, int $errorCount, ?User $user = null): void
    {
        Log::channel('workload')->info('Workloads imported', [
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}

?>