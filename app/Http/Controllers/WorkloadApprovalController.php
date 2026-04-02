<?php

namespace App\Http\Controllers;

use App\Models\Workload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkloadApprovalController extends Controller
{
    /**
     * Kafedra mudiri → tekshiruvga yuborish
     * POST /workloads/{workload}/submit
     * draft → pending
     */
    public function submit(Workload $workload)
    {
        $user = Auth::user();

        if ($workload->status !== 'draft') {
            return back()->with('error', 'Faqat qoralama yuklamani yuborish mumkin!');
        }

        if (!$user->isAdmin()) {
            $isOwnDept = $user->isDepartmentHead()
                && $user->teacher?->department_id === $workload->department_id;

            if (!$isOwnDept) {
                return back()->with('error', 'Sizda bu yuklamani yuborish huquqi yo\'q!');
            }
        }

        $workload->update(['status' => 'pending']);

        return back()->with('success', 'Yuklama tekshiruvga yuborildi! ✅');
    }

    /**
     * Admin → tasdiqlash
     * POST /workloads/{workload}/approve
     * pending → confirmed
     */
    public function approve(Workload $workload)
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            return back()->with('error', 'Faqat admin tasdiqlashi mumkin!');
        }

        if ($workload->status !== 'pending') {
            return back()->with('error', 'Faqat tekshiruvdagi yuklamani tasdiqlash mumkin!');
        }

        $workload->update([
            'status'      => 'confirmed',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Yuklama tasdiqlandi! ✅');
    }

    /**
     * Admin → qaytarish
     * POST /workloads/{workload}/reject
     * pending → draft
     */
    public function reject(Workload $workload)
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            return back()->with('error', 'Faqat admin rad etishi mumkin!');
        }

        if ($workload->status !== 'pending') {
            return back()->with('error', 'Faqat tekshiruvdagi yuklamani rad etish mumkin!');
        }

        $workload->update([
            'status'      => 'draft',
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return back()->with('success', 'Yuklama qaytarildi.');
    }

    /**
     * Bulk action — bir nechta yuklamani bir manda o'zgartirish
     * POST /workloads/bulk-action
     * { ids: [1,2,3], action: 'submit'|'approve'|'reject' }
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer|exists:workloads,id',
            'action' => 'required|in:submit,approve,reject',
        ]);

        $user    = Auth::user();
        $action  = $request->input('action');
        $success = 0;
        $skipped = 0;

        foreach (Workload::whereIn('id', $request->input('ids'))->get() as $workload) {
            try {
                match ($action) {
                    'submit'  => $this->doSubmit($workload, $user),
                    'approve' => $this->doApprove($workload, $user),
                    'reject'  => $this->doReject($workload, $user),
                };
                $success++;
            } catch (\Exception) {
                $skipped++;
            }
        }

        $label = match ($action) {
            'submit'  => 'tekshiruvga yuborildi',
            'approve' => 'tasdiqlandi',
            'reject'  => 'qaytarildi',
        };

        $msg = "{$success} ta yuklama {$label}";
        if ($skipped > 0) $msg .= ", {$skipped} ta o'tkazib yuborildi";

        return back()->with('success', $msg);
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    private function doSubmit(Workload $w, $user): void
    {
        if ($w->status !== 'draft') throw new \Exception();

        if (!$user->isAdmin()) {
            $ok = $user->isDepartmentHead()
                && $user->teacher?->department_id === $w->department_id;
            if (!$ok) throw new \Exception();
        }

        $w->update(['status' => 'pending']);
    }

    private function doApprove(Workload $w, $user): void
    {
        if (!$user->isAdmin() || $w->status !== 'pending') throw new \Exception();

        $w->update([
            'status'      => 'confirmed',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);
    }

    private function doReject(Workload $w, $user): void
    {
        if (!$user->isAdmin() || $w->status !== 'pending') throw new \Exception();

        $w->update([
            'status'      => 'draft',
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }
}
