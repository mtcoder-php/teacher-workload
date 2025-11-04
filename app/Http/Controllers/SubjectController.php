<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Department;
use App\Models\Direction;
use App\Models\Subject;
use App\Services\SubjectService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubjectController extends Controller
{
    public function __construct(
        private ?SubjectService $subjectService = null
    ) {
        // Service ixtiyoriy, agar yo'q bo'lsa ham ishlaydi
    }

    /**
     * Fanlar ro'yxati
     */
    public function index(Request $request)
    {
        $query = Subject::with(['department.faculty', 'direction']);

        // Filtrlar
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('direction_id')) {
            $query->where('direction_id', $request->direction_id);
        }

        if ($request->filled('course_level')) {
            $query->where('course_level', $request->course_level);
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        if ($request->filled('education_form')) {
            $query->where('education_form', $request->education_form);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('can_be_potok')) {
            $query->where('can_be_potok', $request->boolean('can_be_potok'));
        }

        // Qidiruv
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Saralash
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $subjects = $query->paginate(20)->withQueryString();

        // Filtr uchun ma'lumotlar
        $departments = Department::where('is_active', true)
            ->with('faculty:id,name')
            ->get(['id', 'name', 'faculty_id']);

        $directions = Direction::where('is_active', true)
            ->get(['id', 'name', 'code']);

        return Inertia::render('Subjects/Index', [
            'subjects' => $subjects,
            'departments' => $departments,
            'directions' => $directions,
            'filters' => $request->only([
                'search', 
                'department_id', 
                'direction_id',
                'course_level',
                'subject_type',
                'education_form',
                'is_active',
                'can_be_potok'
            ]),
            'courseLevels' => [
                ['value' => 1, 'label' => '1-kurs'],
                ['value' => 2, 'label' => '2-kurs'],
                ['value' => 3, 'label' => '3-kurs'],
                ['value' => 4, 'label' => '4-kurs'],
            ],
            'subjectTypes' => [
                ['value' => 'asosiy', 'label' => 'Asosiy'],
                ['value' => 'yordamchi', 'label' => 'Yordamchi'],
                ['value' => 'ixtiyoriy', 'label' => 'Ixtiyoriy'],
            ],
            'educationForms' => [
                ['value' => 'kunduzgi', 'label' => 'Kunduzgi'],
                ['value' => 'kechki', 'label' => 'Kechki'],
                ['value' => 'sirtqi', 'label' => 'Sirtqi'],
            ],
        ]);
    }

    /**
     * Yangi fan qo'shish sahifasi
     */
    public function create()
    {
        $departments = Department::where('is_active', true)
            ->with('faculty:id,name')
            ->get(['id', 'name', 'faculty_id']);

        $directions = Direction::where('is_active', true)
            ->get(['id', 'name', 'code', 'department_id']);

        return Inertia::render('Subjects/Create', [
            'departments' => $departments,
            'directions' => $directions,
            'courseLevels' => [
                ['value' => 1, 'label' => '1-kurs'],
                ['value' => 2, 'label' => '2-kurs'],
                ['value' => 3, 'label' => '3-kurs'],
                ['value' => 4, 'label' => '4-kurs'],
            ],
            'subjectTypes' => [
                ['value' => 'asosiy', 'label' => 'Asosiy'],
                ['value' => 'majburiy', 'label' => 'Majburiy'],
                ['value' => 'tanlov', 'label' => 'Tanlov'],
            ],
            'educationForms' => [
                ['value' => 'kunduzgi', 'label' => 'Kunduzgi'],
                ['value' => 'kechki', 'label' => 'Kechki'],
                ['value' => 'sirtqi', 'label' => 'Sirtqi'],
            ],
            'controlTypes' => [
                ['value' => 'imtihon', 'label' => 'Imtihon'],
                ['value' => 'test', 'label' => 'Test'],
                ['value' => 'baholash', 'label' => 'Baholash'],
            ],
        ]);
    }

    /**
     * Yangi fan saqlash
     */
    public function store(StoreSubjectRequest $request)
    {
        Subject::create($request->validated());

        return redirect()->route('subjects.index')
            ->with('success', 'Fan muvaffaqiyatli qo\'shildi');
    }

    /**
     * Fan tafsilotlari
     */
    public function show(Subject $subject)
    {
        $subject->load([
            'department.faculty', 
            'direction',
        ]);

        // Statistika
        $stats = [
            'semester_1' => [
                'total_hours' => $subject->semester_1_total_hours,
                'auditory_hours' => $subject->semester_1_auditory_hours,
                'lecture' => $subject->semester_1_lecture,
                'practical' => $subject->semester_1_practical,
                'laboratory' => $subject->semester_1_laboratory,
                'seminar' => $subject->semester_1_seminar,
                'practice' => $subject->semester_1_practice,
                'exam' => $subject->semester_1_exam,
                'test' => $subject->semester_1_test,
            ],
            'semester_2' => [
                'total_hours' => $subject->semester_2_total_hours,
                'auditory_hours' => $subject->semester_2_auditory_hours,
                'lecture' => $subject->semester_2_lecture,
                'practical' => $subject->semester_2_practical,
                'laboratory' => $subject->semester_2_laboratory,
                'seminar' => $subject->semester_2_seminar,
                'practice' => $subject->semester_2_practice,
                'exam' => $subject->semester_2_exam,
                'test' => $subject->semester_2_test,
            ],
            'additional' => [
                'coursework' => $subject->coursework_hours,
                'diploma' => $subject->diploma_hours,
                'consultation' => $subject->consultation_hours,
            ],
            'total_hours' => $subject->total_hours,
            'total_auditory_hours' => $subject->total_auditory_hours,
        ];
        
        return Inertia::render('Subjects/Show', [
            'subject' => $subject,
            'stats' => $stats,
        ]);
    }

    /**
     * Fan tahrirlash sahifasi
     */
    public function edit(Subject $subject)
    {
        $departments = Department::where('is_active', true)
            ->with('faculty:id,name')
            ->get(['id', 'name', 'faculty_id']);

        $directions = Direction::where('is_active', true)
            ->get(['id', 'name', 'code', 'department_id']);

        return Inertia::render('Subjects/Edit', [
            'subject' => $subject,
            'departments' => $departments,
            'directions' => $directions,
            'courseLevels' => [
                ['value' => 1, 'label' => '1-kurs'],
                ['value' => 2, 'label' => '2-kurs'],
                ['value' => 3, 'label' => '3-kurs'],
                ['value' => 4, 'label' => '4-kurs'],
            ],
             'subjectTypes' => [
                ['value' => 'asosiy', 'label' => 'Asosiy'],
                ['value' => 'majburiy', 'label' => 'Majburiy'],
                ['value' => 'tanlov', 'label' => 'Tanlov'],
            ],
            'educationForms' => [
                ['value' => 'kunduzgi', 'label' => 'Kunduzgi'],
                ['value' => 'kechki', 'label' => 'Kechki'],
                ['value' => 'sirtqi', 'label' => 'Sirtqi'],
            ],
            'controlTypes' => [
                ['value' => 'imtihon', 'label' => 'Imtihon'],
                ['value' => 'test', 'label' => 'Test'],
                ['value' => 'baholash', 'label' => 'Baholash'],
            ],
        ]);
    }

    /**
     * Fan yangilash
     */
    public function update(UpdateSubjectRequest $request, Subject $subject)
    {
        $subject->update($request->validated());

        return redirect()->route('subjects.index')
            ->with('success', 'Fan muvaffaqiyatli yangilandi');
    }

    /**
     * Fan o'chirish
     */
    public function destroy(Subject $subject)
    {
        // Agar fanga yuklamalar biriktirilgan bo'lsa
        if ($subject->workloads()->exists()) {
            return redirect()->back()
                ->with('error', 'Bu fanga yuklamalar biriktirilgan. Avval yuklamalarni o\'chiring!');
        }

        $subject->delete();

        return redirect()->route('subjects.index')
            ->with('success', 'Fan muvaffaqiyatli o\'chirildi');
    }

    /**
     * Fanni faollashtirish/faolsizlantirish
     */
    public function toggleActive(Subject $subject)
    {
        $subject->update([
            'is_active' => !$subject->is_active
        ]);

        $status = $subject->is_active ? 'faollashtirildi' : 'faolsizlantirildi';

        return redirect()->back()
            ->with('success', "Fan muvaffaqiyatli {$status}");
    }

    /**
     * Yo'nalishga tegishli fanlarni olish (AJAX)
     */
    public function getByDirection(Direction $direction)
    {
        $subjects = Subject::where('direction_id', $direction->id)
            ->where('is_active', true)
            ->get(['id', 'name', 'code', 'course_level', 'credit_hours']);

        return response()->json($subjects);
    }

    /**
     * Kafedra fanlarini olish (AJAX)
     */
    public function getByDepartment(Department $department)
    {
        $subjects = Subject::where('department_id', $department->id)
            ->where('is_active', true)
            ->get(['id', 'name', 'code', 'course_level', 'credit_hours']);

        return response()->json($subjects);
    }

    /**
     * Fan soatlarini ko'rish
     */
    public function hours(Subject $subject, Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        $semester = $request->get('semester', 1);

        if (!$academicYearId) {
            return response()->json([
                'error' => 'Academic year ID is required'
            ], 400);
        }

        $semesterHours = $subject->getSemesterHours($semester);
        $distributed = $subject->getDistributedHours($academicYearId, $semester);
        $remaining = $subject->getRemainingHours($academicYearId, $semester);
        $percentage = $subject->getDistributionPercentage($academicYearId, $semester);

        return response()->json([
            'semester_hours' => $semesterHours,
            'distributed' => $distributed,
            'remaining' => $remaining,
            'percentage' => $percentage,
            'is_fully_distributed' => $subject->isFullyDistributed($academicYearId, $semester),
        ]);
    }
}