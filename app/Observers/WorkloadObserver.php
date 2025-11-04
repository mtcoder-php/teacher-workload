<?php

namespace App\Observers;

use App\Models\Workload;
use App\Models\WorkloadHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WorkloadObserver
{
    /**
     * Yuklama yaratilganda
     */
    public function created(Workload $workload): void
    {
        try {
            WorkloadHistory::create([
                'workload_id' => $workload->id,
                'user_id' => Auth::id() ?? 1, // Default system user
                'action' => 'created',
                'new_data' => $workload->toArray(),
                'comment' => $workload->is_potok
                    ? 'Potok yuklama yaratildi: ' . $workload->potok_code
                    : 'Oddiy yuklama yaratildi',
            ]);

            Log::info('Yuklama yaratildi', [
                'workload_id' => $workload->id,
                'teacher_id' => $workload->teacher_id,
                'subject_id' => $workload->subject_id,
                'is_potok' => $workload->is_potok,
            ]);

        } catch (\Exception $e) {
            Log::error('WorkloadObserver::created xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Yuklama yangilanganda
     */
    public function updated(Workload $workload): void
    {
        try {
            $changes = $workload->getChanges();

            if (!empty($changes) && !isset($changes['updated_at'])) {
                WorkloadHistory::create([
                    'workload_id' => $workload->id,
                    'user_id' => Auth::id() ?? 1,
                    'action' => 'updated',
                    'old_data' => $workload->getOriginal(),
                    'new_data' => $changes,
                    'comment' => 'Yuklama yangilandi',
                ]);

                Log::info('Yuklama yangilandi', [
                    'workload_id' => $workload->id,
                    'changes' => array_keys($changes),
                ]);
            }

        } catch (\Exception $e) {
            Log::error('WorkloadObserver::updated xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Yuklama o'chirilganda (soft delete)
     */
    public function deleted(Workload $workload): void
    {
        try {
            WorkloadHistory::create([
                'workload_id' => $workload->id,
                'user_id' => Auth::id() ?? 1,
                'action' => 'deleted',
                'old_data' => $workload->toArray(),
                'comment' => 'Yuklama o\'chirildi',
            ]);

            Log::info('Yuklama o\'chirildi', [
                'workload_id' => $workload->id,
                'teacher_id' => $workload->teacher_id,
            ]);

        } catch (\Exception $e) {
            Log::error('WorkloadObserver::deleted xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Yuklama tiklanganida
     */
    public function restored(Workload $workload): void
    {
        try {
            WorkloadHistory::create([
                'workload_id' => $workload->id,
                'user_id' => Auth::id() ?? 1,
                'action' => 'restored',
                'new_data' => $workload->toArray(),
                'comment' => 'Yuklama tiklandi',
            ]);

            Log::info('Yuklama tiklandi', [
                'workload_id' => $workload->id,
            ]);

        } catch (\Exception $e) {
            Log::error('WorkloadObserver::restored xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Yuklama butunlay o'chirilganda (force delete)
     */
    public function forceDeleted(Workload $workload): void
    {
        try {
            Log::warning('Yuklama butunlay o\'chirildi', [
                'workload_id' => $workload->id,
                'teacher_id' => $workload->teacher_id,
                'deleted_by' => Auth::id(),
            ]);

        } catch (\Exception $e) {
            Log::error('WorkloadObserver::forceDeleted xatolik: ' . $e->getMessage());
        }
    }
}
