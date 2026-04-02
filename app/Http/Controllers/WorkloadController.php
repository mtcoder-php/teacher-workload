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
            ->latest()->paginate(15)->withQueryString();

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
            $workload = $this->workloadService->createWorkload($request->validated());

            $msg = $request->input('status') === 'draft'
                ? 'Yuklama qoralama sifatida saqlandi!'
                : 'Yuklama muvaffaqiyatli yaratildi!';

            return redirect()->route('workloads.index')->with('success', $msg);

        } catch (\Exception $e) {
            Log::error('store workload: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * AJAX: Fan soatlarini yuklash — guruh bo'yicha mustaqil hisoblash
     *
     * Mantiq:
     *   Har bir guruh uchun limit = fan soatlari (mustaqil)
     *   Sarflangan = faqat SHU guruhga berilgan soatlar
     *   Qolgan = fan soati - shu guruhga berilgan soat
     *
     * GET /workloads/ajax/subject/{subjectId}/details
     *   ?academic_year_id=1
     *   &group_ids[]=5&group_ids[]=6    ← tanlangan guruhlar
     */
    public function getSubjectDetails(Request $request, $subjectId)
    {
        try {
            $subject        = Subject::findOrFail($subjectId);
            $academicYearId = $request->input('academic_year_id')
                ?? AcademicYear::where('is_active', true)->value('id');

            $query = Workload::where('subject_id', $subjectId)
                ->where('academic_year_id', $academicYearId);

            // Edit paytida bu yuklamaning o'z soatlarini chiqarib tashlaymiz
            if ($excludeId = $request->input('exclude_workload_id')) {
                $query->where('id', '!=', (int)$excludeId);
            }

            // Agar group_ids berilgan bo'lsa — faqat shu guruhlarga tegishli yuklamalarni hisoblaymiz
            // Bu potoksiz yuklama yaratishda to'g'ri remaining ni olish uchun zarur
            $groupIds = $request->input('group_ids', []);
            if (!empty($groupIds)) {
                $query->whereHas('groups', function ($q) use ($groupIds) {
                    $q->whereIn('groups.id', $groupIds);
                });
            }

            $dist = $query->selectRaw('
                COALESCE(SUM(semester_1_lecture),0)    as s1_lec,
                COALESCE(SUM(semester_1_practical),0)  as s1_pra,
                COALESCE(SUM(semester_1_laboratory),0) as s1_lab,
                COALESCE(SUM(semester_1_seminar),0)    as s1_sem,
                COALESCE(SUM(semester_1_practice),0)   as s1_prc,
                COALESCE(SUM(semester_1_exam),0)       as s1_ex,
                COALESCE(SUM(semester_1_test),0)       as s1_tst,
                COALESCE(SUM(semester_2_lecture),0)    as s2_lec,
                COALESCE(SUM(semester_2_practical),0)  as s2_pra,
                COALESCE(SUM(semester_2_laboratory),0) as s2_lab,
                COALESCE(SUM(semester_2_seminar),0)    as s2_sem,
                COALESCE(SUM(semester_2_practice),0)   as s2_prc,
                COALESCE(SUM(semester_2_exam),0)       as s2_ex,
                COALESCE(SUM(semester_2_test),0)       as s2_tst,
                COALESCE(SUM(coursework_hours),0)      as cw,
                COALESCE(SUM(diploma_hours),0)         as dip,
                COALESCE(SUM(consultation_hours),0)    as con
            ')->first();

            $map = [
                'semester_1_lecture'    => 's1_lec',
                'semester_1_practical'  => 's1_pra',
                'semester_1_laboratory' => 's1_lab',
                'semester_1_seminar'    => 's1_sem',
                'semester_1_practice'   => 's1_prc',
                'semester_1_exam'       => 's1_ex',
                'semester_1_test'       => 's1_tst',
                'semester_2_lecture'    => 's2_lec',
                'semester_2_practical'  => 's2_pra',
                'semester_2_laboratory' => 's2_lab',
                'semester_2_seminar'    => 's2_sem',
                'semester_2_practice'   => 's2_prc',
                'semester_2_exam'       => 's2_ex',
                'semester_2_test'       => 's2_tst',
                'coursework_hours'      => 'cw',
                'diploma_hours'         => 'dip',
                'consultation_hours'    => 'con',
            ];

            $remainingHours = [];
            $maxHours       = [];
            foreach ($map as $field => $distKey) {
                $max                    = (float)($subject->{$field} ?? 0);
                $used                   = (float)($dist->{$distKey} ?? 0);
                $maxHours[$field]       = $max;
                $remainingHours[$field] = max(0, $max - $used);
            }

            // Fan to'liq taqsimlangan bo'lsa is_fully_used = true
            $isFullyUsed = collect($remainingHours)->every(fn($v) => $v == 0)
                && collect($maxHours)->some(fn($v) => $v > 0);

            return response()->json([
                'success'         => true,
                'max_hours'       => $maxHours,
                'remaining_hours' => $remainingHours,
                'is_fully_used'   => $isFullyUsed,
            ]);

        } catch (\Exception $e) {
            Log::error('getSubjectDetails: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }


    /**
     * AJAX: Guruhlar holati
     * POST /workloads/ajax/check-groups-status
     */
    public function checkGroupsStatus(Request $request)
    {
        try {
            $groupIds       = $request->input('group_ids', []);
            $subjectId      = $request->input('subject_id');
            $academicYearId = $request->input('academic_year_id');

            if (empty($groupIds) || !$subjectId || !$academicYearId) {
                return response()->json(['success' => false, 'message' => 'Parametrlar to\'liq emas'], 400);
            }

            $results = $this->validationService->checkMultipleGroupsStatus(
                $groupIds, $subjectId, $academicYearId
            );

            return response()->json(['success' => true, 'data' => $results]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Xatolik'], 500);
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
     * AJAX: Bu guruhlar + fan uchun reyting allaqachon berilganmi?
     * GET /workloads/ajax/rating-status?subject_id=X&group_ids[]=1&group_ids[]=2
     */
    public function getRatingStatus(Request $request)
    {
        $subjectId      = $request->input('subject_id');
        $groupIds       = $request->input('group_ids', []);
        $academicYearId = $request->input('academic_year_id');

        if (!$subjectId || empty($groupIds)) {
            return response()->json([
                'success'     => true,
                'is_assigned' => false,
                'assigned_to' => null,
            ]);
        }

        if (!$academicYearId) {
            $academicYearId = AcademicYear::where('is_active', true)->value('id');
        }

        // Bu guruhlardan BIRORTASIDA shu fan uchun has_rating = true yuklama bormi?
        // group_ids[] ichidan birortasi workload_groups jadvalida has_rating=true yuklamaga tegishlimi?
        $assigned = Workload::where('subject_id', $subjectId)
            ->where('academic_year_id', $academicYearId)
            ->where('has_rating', true)
            ->whereHas('groups', function ($q) use ($groupIds) {
                $q->whereIn('groups.id', $groupIds);
            })
            ->with('teacher.user')
            ->first();

        return response()->json([
            'success'     => true,
            'is_assigned' => $assigned !== null,
            'assigned_to' => $assigned?->teacher?->user?->name,
        ]);
    }







}
