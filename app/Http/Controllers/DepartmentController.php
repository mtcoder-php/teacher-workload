<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    /**
     * Barcha kafedralar ro'yxati
     */
    public function index(Request $request)
    {
        $query = Department::with(['faculty', 'head'])
            ->withCount('teachers');

        // Filter bo'yicha
        if ($request->filled('faculty_id')) {
            $query->where('faculty_id', $request->faculty_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $departments = $query->latest()->paginate(15)->withQueryString();
        $faculties = Faculty::where('is_active', true)->get();

        return Inertia::render('Departments/Index', [
            'departments' => $departments,
            'faculties' => $faculties,
            'filters' => $request->only(['search', 'faculty_id'])
        ]);
    }

    /**
     * Yangi kafedra qo'shish formasi
     */
    public function create()
    {
        $faculties = Faculty::where('is_active', true)->get();
        
        // VARIANT 1: Agar role relation bor bo'lsa
        try {
            $heads = User::whereHas('role', function ($query) {
                $query->where('name', 'kafedra_mudiri');
            })->where('is_active', true)->get();
        } catch (\Exception $e) {
            // VARIANT 2: Agar role yo'q bo'lsa, barcha active userlarni olamiz
            $heads = User::where('is_active', true)->get();
        }

        return Inertia::render('Departments/Create', [
            'faculties' => $faculties,
            'heads' => $heads
        ]);
    }

    /**
     * Yangi kafedrani saqlash
     */
    public function store(StoreDepartmentRequest $request)
    {
        Department::create($request->validated());

        return redirect()->route('departments.index')
            ->with('success', 'Kafedra muvaffaqiyatli qo\'shildi');
    }

    /**
     * Kafedraning to'liq ma'lumotlarini ko'rish
     */
    public function show(Department $department)
    {
        $department->load(['faculty', 'head', 'teachers.user', 'subjects']);

        // Statistika
        $stats = [
            'teachers_count' => $department->teachers()->where('is_active', true)->count(),
            'subjects_count' => $department->subjects()->where('is_active', true)->count(),
        ];

        return Inertia::render('Departments/Show', [
            'department' => $department,
            'stats' => $stats
        ]);
    }

    /**
     * Kafedraning tahrirlash formasi
     */
    public function edit(Department $department)
    {
        $faculties = Faculty::where('is_active', true)->get();
        
        // TO'G'RILANGAN QISM - error handling qo'shildi
        try {
            // VARIANT 1: Agar User modelida 'role' relation mavjud bo'lsa
            $heads = User::whereHas('role', function ($query) {
                $query->where('name', 'kafedra_mudiri');
            })->where('is_active', true)->get();
        } catch (\Exception $e) {
            // VARIANT 2: Agar role relation yo'q bo'lsa
            // Barcha active userlarni olamiz yoki bo'sh array
            $heads = User::where('is_active', true)
                ->select('id', 'name', 'email')
                ->get();
            
            // Yoki faqat teacher role'li userlar (agar teachers jadvali bor bo'lsa)
            // $heads = User::whereHas('teacher')->where('is_active', true)->get();
        }

        return Inertia::render('Departments/Edit', [
            'department' => $department,
            'faculties' => $faculties,
            'heads' => $heads
        ]);
    }

    /**
     * Kafedraning ma'lumotlarini yangilash
     */
    public function update(UpdateDepartmentRequest $request, Department $department)
    {
     
        $department->update($request->validated());

        return redirect()->route('departments.index')
            ->with('success', 'Kafedra muvaffaqiyatli yangilandi');
    }

    /**
     * Kafedraning o'chirish
     */
    public function destroy(Department $department)
    {
        // Kafedraga o'qituvchilar biriktirilgan bo'lsa, o'chirishga ruxsat bermaslik
        if ($department->teachers()->count() > 0) {
            return redirect()->route('departments.index')
                ->with('error', 'Bu kafedrada o\'qituvchilar mavjud. Avval ularni boshqa kafedralarga o\'tkazing. ❌');
        }

        $department->delete();

        return redirect()->route('departments.index')
            ->with('success', 'Kafedra muvaffaqiyatli o\'chirildi');
    }
    
    /**
     * YORDAMCHI METHOD: Get users for department head selection
     */
    private function getDepartmentHeads()
    {
        // Check if User model has role relationship
        if (method_exists(User::class, 'role')) {
            return User::whereHas('role', function ($query) {
                $query->where('name', 'kafedra_mudiri');
            })->where('is_active', true)->get();
        }
        
        // Fallback: return all active users
        return User::where('is_active', true)
            ->select('id', 'name', 'email')
            ->get();
    }
}