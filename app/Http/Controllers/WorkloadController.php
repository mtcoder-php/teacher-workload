<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Direction;
use App\Models\Group;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Workload;
use App\Http\Requests\StoreWorkloadRequest;
use App\Services\WorkloadService;
use App\Services\WorkloadValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class WorkloadController extends Controller
{
    protected WorkloadService $workloadService;
    protected WorkloadValidationService $validationService;

    public function __construct(
        WorkloadService $workloadService,
        WorkloadValidationService $validationService
    ) {
        $this->workloadService = $workloadService;
        $this->validationService = $validationService;
    }

    /**
     * Yuklamalar ro'yxati
     */
    public function index(Request $request)
    {
        $query = Workload::query();

        if ($request->search) {
            $query->where('notes', 'like', "%{$request->search}%");
        }
        if ($request->department_id)   $query->where('department_id',   $request->department_id);
        if ($request->teacher_id)      $query->where('teacher_id',      $request->teacher_id);
        if ($request->subject_id)      $query->where('subject_id',      $request->subject_id);
        if ($request->academic_year_id)$query->where('academic_year_id',$request->academic_year_id);
        if ($request->direction_id)    $query->where('direction_id',    $request->direction_id);
        if ($request->status)          $query->where('status',          $request->status);
        if ($request->is_potok !== null) {
            $query->where('is_potok', $request->is_potok == '1');
        }

        $workloads = $query->with(['groups', 'teacher.user', 'subject', 'direction', 'academicYear'])
            ->latest()->paginate(10)->withQueryString();

        $currentAcademicYear = AcademicYear::where('is_active', true)->first();

        return Inertia::render('Workloads/Index', [
            'workloads'          => $workloads,
            'filters'            => $request->only(['search','department_id','teacher_id','subject_id','academic_year_id','direction_id','status','is_potok']),
            'departments'        => Department::where('is_active', true)->get(['id','name']),
            'teachers'           => Teacher::with('user')->get()->map(fn($t) => ['id'=>$t->id,'name'=>$t->user?->name??'']),
            'subjects'           => Subject::where('is_active', true)->get(['id','name']),
            'academicYears'      => AcademicYear::orderBy('start_date','desc')->get(['id','name','is_active']),
            'directions'         => Direction::where('is_active', true)->get(['id','name']),
            'currentAcademicYear'=> $currentAcademicYear ? ['id'=>$currentAcademicYear->id,'name'=>$currentAcademicYear->name] : null,
        ]);
    }

    /**
     * WIZARD create() metodi — faqat shu qism yangilandi
     * app/Http/Controllers/WorkloadController.php ichida almashtiring
     */
    public function create()
    {
        $user = Auth::user();
        $isKafedraMudiri  = $user->hasRole('kafedra_mudiri');
        $userDepartmentId = $user->teacher?->department_id;

        // Kafedralar
        $deptQ = Department::where('is_active', true)->with('faculty');
        if ($isKafedraMudiri && $userDepartmentId) {
            $deptQ->where('id', $userDepartmentId);
        }
        $departments = $deptQ->orderBy('name')->get()->map(fn($d) => [
            'id'         => $d->id,
            'name'       => $d->name,
            'faculty_id' => $d->faculty_id,
            'faculty'    => $d->faculty ? ['id' => $d->faculty->id, 'name' => $d->faculty->name] : null,
        ]);

        // Yo'nalishlar — degree_type va duration_years bilan
        $directions = Direction::where('is_active', true)
            ->orderBy('name')->get()
            ->map(fn($d) => [
                'id'             => $d->id,
                'name'           => $d->name,
                'code'           => $d->code ?? '',
                'department_id'  => $d->department_id,
                'degree_type'    => $d->degree_type    ?? 'bakalavr',   // 'bakalavr' | 'magistratura'
                'duration_years' => $d->duration_years ?? 4,
            ]);

        // Fanlar
        $subjects = Subject::where('is_active', true)
            ->orderBy('name')->get()
            ->map(fn($s) => [
                'id'                    => $s->id,
                'name'                  => $s->name,
                'code'                  => $s->code ?? '',
                'department_id'         => $s->department_id,
                'direction_id'          => $s->direction_id,
                'can_be_potok'          => (bool)($s->can_be_potok ?? false),
                'min_groups_for_potok'  => $s->min_groups_for_potok ?? 2,
                'semester_1_lecture'    => (float)($s->semester_1_lecture    ?? 0),
                'semester_1_practical'  => (float)($s->semester_1_practical  ?? 0),
                'semester_1_laboratory' => (float)($s->semester_1_laboratory ?? 0),
                'semester_1_seminar'    => (float)($s->semester_1_seminar    ?? 0),
                'semester_1_practice'   => (float)($s->semester_1_practice   ?? 0),
                'semester_1_exam'       => (float)($s->semester_1_exam       ?? 0),
                'semester_1_test'       => (float)($s->semester_1_test       ?? 0),
                'semester_2_lecture'    => (float)($s->semester_2_lecture    ?? 0),
                'semester_2_practical'  => (float)($s->semester_2_practical  ?? 0),
                'semester_2_laboratory' => (float)($s->semester_2_laboratory ?? 0),
                'semester_2_seminar'    => (float)($s->semester_2_seminar    ?? 0),
                'semester_2_practice'   => (float)($s->semester_2_practice   ?? 0),
                'semester_2_exam'       => (float)($s->semester_2_exam       ?? 0),
                'semester_2_test'       => (float)($s->semester_2_test       ?? 0),
                'coursework_hours'      => (float)($s->coursework_hours      ?? 0),
                'diploma_hours'         => (float)($s->diploma_hours         ?? 0),
                'consultation_hours'    => (float)($s->consultation_hours    ?? 0),
            ]);

        // Guruhlar — direction_id va course bilan (frontend filtrlaydi)
        $activeYear = AcademicYear::where('is_active', true)->first();
        $groups = Group::where('is_active', true)
            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
            ->orderBy('course')->orderBy('name')
            ->get()
            ->map(fn($g) => [
                'id'            => $g->id,
                'name'          => $g->name,
                'code'          => $g->code ?? $g->name,
                'direction_id'  => $g->direction_id,
                'course'        => $g->course,
                'student_count' => $g->student_count ?? 0,
                'education_type'=> $g->education_type ?? 'kunduzgi',
            ]);

        // O'qituvchilar
        $teachers = Teacher::with('user')
            ->whereHas('user', fn($q) => $q->where('is_active', true))
            ->orderBy('id')->get()
            ->map(fn($t) => [
                'id'              => $t->id,
                'name'            => $t->user?->name ?? 'Noma\'lum',
                'department_id'   => $t->department_id,
                'position'        => $t->position ?? '',
                'academic_degree' => $t->academic_degree ?? '',
            ]);

        return Inertia::render('Workloads/CreateWizard', [
            'departments'         => $departments,
            'directions'          => $directions,
            'subjects'            => $subjects,
            'groups'              => $groups,
            'teachers'            => $teachers,
            'currentAcademicYear' => $activeYear
                ? ['id' => $activeYear->id, 'name' => $activeYear->name]
                : null,
            'currentUser' => [
                'id'                => $user->id,
                'name'              => $user->name,
                'is_kafedra_mudiri' => $isKafedraMudiri,
                'department_id'     => $userDepartmentId,
            ],
        ]);
    }

    /**
     * Yuklamani saqlash
     */
    public function store(StoreWorkloadRequest $request)
    {
        try {
            $data     = $request->validated();
            $workload = $this->workloadService->createWorkload($data);

            $groupCount = count($data['group_ids'] ?? []);
            $isPotok    = $data['is_potok'] ?? false;

            if ($request->input('status') === 'draft') {
                $msg = 'Yuklama qoralama sifatida saqlandi!';
            } elseif (!$isPotok && $groupCount > 1) {
                $msg = "{$groupCount} ta guruh uchun yuklamalar muvaffaqiyatli yaratildi!";
            } else {
                $msg = 'Yuklama muvaffaqiyatli yaratildi!';
            }

            return redirect()->route('workloads.index')->with('success', $msg);

        } catch (\Exception $e) {
            Log::error('store workload: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Yuklama yaratishda xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Yuklamani ko'rsatish
     */
    public function show(Workload $workload)
    {
        $user = Auth::user();

        $workload->load([
            'teacher.user',
            'subject',
            'department',
            'groups',
            'academicYear',
            'direction',
        ]);

        // Soatlar statistikasi
        $stats = [
            'semester_1_total' =>
                $workload->semester_1_lecture    + $workload->semester_1_practical  +
                $workload->semester_1_laboratory + $workload->semester_1_seminar    +
                $workload->semester_1_practice,
            'semester_2_total' =>
                $workload->semester_2_lecture    + $workload->semester_2_practical  +
                $workload->semester_2_laboratory + $workload->semester_2_seminar    +
                $workload->semester_2_practice,
        ];
        $stats['grand_total'] =
            $stats['semester_1_total'] +
            $stats['semester_2_total'] +
            $workload->coursework_hours  +
            $workload->diploma_hours     +
            $workload->consultation_hours;

        $isAdmin    = $user->isAdmin();
        $isDeptHead = $user->isDepartmentHead();
        $isOwnDept  = $isDeptHead && $user->teacher?->department_id === $workload->department_id;

        return Inertia::render('Workloads/Show', [
            'workload' => $workload,
            'stats'    => $stats,

            // Tahrirlash: draft (admin + o'z kafedra mudiri) | pending (faqat admin)
            'canEdit' => match(true) {
                $isAdmin                                    => in_array($workload->status, ['draft', 'pending']),
                $isOwnDept && $workload->status === 'draft' => true,
                default                                     => false,
            },

            'canDelete' => match(true) {
                $workload->status === 'completed'                             => false,
                $workload->status === 'confirmed' && $isAdmin                 => true,
                $isAdmin && in_array($workload->status, ['draft', 'pending']) => true,
                $isOwnDept && $workload->status === 'draft'                   => true,
                default                                                        => false,
            },

            // Tekshiruvga yuborish: faqat draft (admin + o'z kafedra mudiri)
            'canSubmit' => match(true) {
                $workload->status !== 'draft'               => false,
                $isAdmin                                    => true,
                $isOwnDept                                  => true,
                default                                     => false,
            },

            // Tasdiqlash: faqat admin, faqat pending
            'canApprove' => $isAdmin && $workload->status === 'pending',

            // Qaytarish: faqat admin, faqat pending
            'canReject'  => $isAdmin && $workload->status === 'pending',

            'currentUser' => [
                'is_admin'     => $isAdmin,
                'is_dept_head' => $isDeptHead,
            ],
        ]);
    }

    /**
     * Tahrirlash formasi
     */
    public function edit(Workload $workload)
    {
        $workload->load(['groups', 'teacher.user', 'subject']);

        return Inertia::render('Workloads/Edit', [
            'workload' => $workload,
            'teachers' => Teacher::with(['user', 'department'])
                ->whereHas('user', fn($q) => $q->where('is_active', true))
                ->orderBy('id')->get()
                ->map(fn($t) => [
                    'id'              => $t->id,
                    'name'            => $t->user?->name ?? "Noma'lum",
                    'department_id'   => $t->department_id,
                    'position'        => $t->position ?? '',
                    'academic_degree' => $t->academic_degree ?? '',
                ]),
            'subject' => $workload->subject ? [
                'id'                    => $workload->subject->id,
                'name'                  => $workload->subject->name,
                'semester_1_lecture'    => (float)($workload->subject->semester_1_lecture    ?? 0),
                'semester_1_practical'  => (float)($workload->subject->semester_1_practical  ?? 0),
                'semester_1_laboratory' => (float)($workload->subject->semester_1_laboratory ?? 0),
                'semester_1_seminar'    => (float)($workload->subject->semester_1_seminar    ?? 0),
                'semester_1_practice'   => (float)($workload->subject->semester_1_practice   ?? 0),
                'semester_1_exam'       => (float)($workload->subject->semester_1_exam       ?? 0),
                'semester_1_test'       => (float)($workload->subject->semester_1_test       ?? 0),
                'semester_2_lecture'    => (float)($workload->subject->semester_2_lecture    ?? 0),
                'semester_2_practical'  => (float)($workload->subject->semester_2_practical  ?? 0),
                'semester_2_laboratory' => (float)($workload->subject->semester_2_laboratory ?? 0),
                'semester_2_seminar'    => (float)($workload->subject->semester_2_seminar    ?? 0),
                'semester_2_practice'   => (float)($workload->subject->semester_2_practice   ?? 0),
                'semester_2_exam'       => (float)($workload->subject->semester_2_exam       ?? 0),
                'semester_2_test'       => (float)($workload->subject->semester_2_test       ?? 0),
                'coursework_hours'      => (float)($workload->subject->coursework_hours      ?? 0),
                'diploma_hours'         => (float)($workload->subject->diploma_hours         ?? 0),
                'consultation_hours'    => (float)($workload->subject->consultation_hours    ?? 0),
            ] : null,
        ]);
    }

    /**
     * Yuklamani yangilash
     *
     * Qoidalar:
     *   draft   → admin + kafedra mudiri barcha soatlarni o'zgartira oladi
     *   pending → faqat admin (o'qituvchi va izoh)
     *   confirmed/completed → TAHRIRLASH YO'Q
     */
    public function update(Request $request, Workload $workload)
    {
        $user = Auth::user();

        // confirmed va completed — tahrirlab bo'lmaydi
        if (in_array($workload->status, ['confirmed', 'completed'])) {
            return back()->with('error',
                'Tasdiqlangan yoki tugatilgan yuklamani tahrirlash mumkin emas!'
            );
        }

        // pending — faqat admin, faqat o'qituvchi va izoh
        if ($workload->status === 'pending' && !$user->isAdmin()) {
            return back()->with('error',
                'Tekshiruvdagi yuklamani faqat admin tahrirlaya oladi!'
            );
        }

        // draft — admin yoki o'z kafedrasi kafedra mudiri
        if ($workload->status === 'draft' && !$user->isAdmin()) {
            $isOwnDept = $user->isDepartmentHead()
                && $user->teacher?->department_id === $workload->department_id;

            if (!$isOwnDept) {
                return back()->with('error',
                    'Sizda bu yuklamani tahrirlash huquqi yo\'q!'
                );
            }
        }

        try {
            $this->workloadService->updateWorkload($workload, $request->all());

            return redirect()->route('workloads.show', $workload->id)
                ->with('success', 'Yuklama muvaffaqiyatli yangilandi!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Yuklamani o'chirish
     *
     * Qoidalar:
     *   draft/pending   → admin + kafedra mudiri (o'z kafedrasi) → hard delete (groups detach)
     *   confirmed       → FAQAT admin → soft delete (deleted_at belgilanadi, audit saqlanadi)
     *   completed       → HECH KIM o'chira olmaydi
     */
    public function destroy(Workload $workload)
    {
        $user = Auth::user();

        // completed — mutlaq himoyalangan
        if ($workload->status === 'completed') {
            return back()->with('error',
                'Tugatilgan yuklamani o\'chirib bo\'lmaydi!'
            );
        }

        // confirmed — faqat admin, soft delete
        if ($workload->status === 'confirmed') {
            if (!$user->isAdmin()) {
                return back()->with('error',
                    'Tasdiqlangan yuklamani faqat admin o\'chira oladi!'
                );
            }

            // Soft delete — deleted_at belgilanadi, ma'lumot saqlanadi
            $workload->delete();

            return redirect()->route('workloads.index')
                ->with('success', 'Yuklama arxivlandi (soft delete). Ma\'lumotlar saqlanib qoldi.');
        }

        // draft/pending — admin yoki o'z kafedrasi kafedra mudiri
        if (!$user->isAdmin()) {
            $isOwnDept = $user->isDepartmentHead()
                && $user->teacher?->department_id === $workload->department_id;

            if (!$isOwnDept) {
                return back()->with('error',
                    'Sizda bu yuklamani o\'chirish huquqi yo\'q!'
                );
            }
        }

        // draft/pending → workload_groups ni ham tozalab o'chirish
        DB::beginTransaction();
        try {
            // Potok bo'lsa, uning qoldiqlari ham o'chiriladi
            if ($workload->is_potok) {
                Workload::where('parent_potok_id', $workload->id)->delete();
            }

            $workload->groups()->detach();
            $workload->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('destroy workload: ' . $e->getMessage());
            return back()->with('error', 'O\'chirishda xatolik: ' . $e->getMessage());
        }

        return redirect()->route('workloads.index')
            ->with('success', 'Yuklama muvaffaqiyatli o\'chirildi!');
    }

    /**
     * Potok qoldig'i yaratish
     */
    public function createRemainder(Request $request, Workload $potok)
    {
        try {
            if (!$potok->is_potok) {
                return back()->withErrors(['error' => 'Bu yuklama potok emas']);
            }
            $remainder = $this->workloadService->createPotokRemainder($request->all(), $potok->id);
            return redirect()->route('workloads.show', $remainder)->with('success', 'Potok qoldig\'i yaratildi!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }


    /**
     * Kafedra mudiri → yuklamani tekshiruvga yuborish
     * POST /workloads/{id}/submit
     *
     * draft → pending
     */
    public function submit(Workload $workload)
    {
        $user = Auth::user();

        // Faqat draft yuborilishi mumkin
        if ($workload->status !== 'draft') {
            return back()->with('error', 'Faqat qoralama yuklamani yuborish mumkin!');
        }

        // Kafedra mudiri — faqat o'z kafedrasi
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
     * Admin → yuklamani tasdiqlash
     * POST /workloads/{id}/approve
     *
     * pending → confirmed
     */
    public function approve(Workload $workload)
    {
        $user = Auth::user();

        // Faqat admin tasdiqlaydi
        if (!$user->isAdmin()) {
            return back()->with('error', 'Faqat admin tasdiqlashi mumkin!');
        }

        // Faqat pending tasdiqlash mumkin
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
     * Admin → yuklamani rad etish (pending → draft)
     * POST /workloads/{id}/reject
     *
     * pending → draft
     */
    public function reject(Request $request, Workload $workload)
    {
        $user = Auth::user();

        // Faqat admin rad etadi
        if (!$user->isAdmin()) {
            return back()->with('error', 'Faqat admin rad etishi mumkin!');
        }

        // Faqat pending rad etish mumkin
        if ($workload->status !== 'pending') {
            return back()->with('error', 'Faqat tekshiruvdagi yuklamani rad etish mumkin!');
        }

        $workload->update([
            'status'      => 'draft',
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return back()->with('success', 'Yuklama qaytarildi — kafedra mudiri qayta ko\'rib chiqishi mumkin.');
    }



// WorkloadController.php ga qo'shing
// Route: GET /workloads/ajax/rating-status

    /**
     * Bulk action: bir nechta yuklamani bir manda o'zgartirish
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
        $ids     = $request->input('ids');
        $action  = $request->input('action');

        $workloads = Workload::whereIn('id', $ids)->get();

        $success = 0;
        $skipped = 0;

        foreach ($workloads as $workload) {
            try {
                match ($action) {
                    'submit'  => $this->bulkSubmit($workload, $user),
                    'approve' => $this->bulkApprove($workload, $user),
                    'reject'  => $this->bulkReject($workload, $user),
                };
                $success++;
            } catch (\Exception $e) {
                $skipped++;
            }
        }

        $msg = match ($action) {
            'submit'  => "tekshiruvga yuborildi",
            'approve' => "tasdiqlandi",
            'reject'  => "qaytarildi",
        };

        $flash = "{$success} ta yuklama {$msg}";
        if ($skipped > 0) $flash .= ", {$skipped} ta o'tkazib yuborildi";

        return back()->with('success', $flash);
    }

    private function bulkSubmit(Workload $workload, $user): void
    {
        if ($workload->status !== 'draft') throw new \Exception('Not draft');

        if (!$user->isAdmin()) {
            $isOwnDept = $user->isDepartmentHead()
                && $user->teacher?->department_id === $workload->department_id;
            if (!$isOwnDept) throw new \Exception('No permission');
        }

        $workload->update(['status' => 'pending']);
    }

    private function bulkApprove(Workload $workload, $user): void
    {
        if (!$user->isAdmin()) throw new \Exception('Not admin');
        if ($workload->status !== 'pending') throw new \Exception('Not pending');

        $workload->update([
            'status'      => 'confirmed',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);
    }

    private function bulkReject(Workload $workload, $user): void
    {
        if (!$user->isAdmin()) throw new \Exception('Not admin');
        if ($workload->status !== 'pending') throw new \Exception('Not pending');

        $workload->update([
            'status'      => 'draft',
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

}
