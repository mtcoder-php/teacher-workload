<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFacultyRequest;
use App\Http\Requests\UpdateFacultyRequest;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FacultyController extends Controller
{
    public function index(Request $request)
    {
        $query = Faculty::with(['dean', 'departments'])
            ->withCount('departments');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('dean', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $faculties = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('Faculties/Index', [
            'faculties' => $faculties,
            'filters' => $request->only(['search', 'status'])
        ]);
    }

    public function create()
    {
        try {
            $deans = User::whereHas('role', function ($query) {
                $query->where('name', 'dekan');
            })->where('is_active', true)
              ->select('id', 'name', 'email')
              ->get();
        } catch (\Exception $e) {
            $deans = User::where('is_active', true)
                ->select('id', 'name', 'email')
                ->get();
        }

        return Inertia::render('Faculties/Create', [
            'deans' => $deans
        ]);
    }

    public function store(StoreFacultyRequest $request)
    {
        Faculty::create($request->validated());

        return redirect()
            ->route('faculties.index')
            ->with('success', 'Fakultet muvaffaqiyatli qo\'shildi! ✅');
    }

    public function show(Faculty $faculty)
    {
        try {
            $faculty->load([
                'dean',
                'departments' => function($query) {
                    $query->withCount('teachers');
                },
                'groups'
            ]);

            return Inertia::render('Faculties/Show', [
                'faculty' => $faculty
            ]);
        } catch (\Exception $e) {
            return redirect()->route('faculties.index')
                ->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }

    public function edit(Faculty $faculty)
    {
        try {
            $deans = User::where('is_active', true)
                ->select('id', 'name', 'email')
                ->get();

            return Inertia::render('Faculties/Edit', [
                'faculty' => $faculty,
                'deans' => $deans
            ]);
        } catch (\Exception $e) {
            return redirect()->route('faculties.index')
                ->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }

    public function update(UpdateFacultyRequest $request, Faculty $faculty)
    {
        try {
            // Debug: Yangilanayotgan ma'lumotlarni ko'rish
            \Log::info('Update Faculty:', [
                'faculty_id' => $faculty->id,
                'old_dean_id' => $faculty->dean_id,
                'new_data' => $request->validated()
            ]);
            
            $faculty->update($request->validated());
            
            // Dean o'zgarganini tekshirish
            if ($request->filled('dean_id')) {
                \Log::info('Dean updated:', [
                    'new_dean_id' => $faculty->dean_id,
                    'dean' => $faculty->fresh()->dean
                ]);
            }

            return redirect()
                ->route('faculties.index')
                ->with('success', 'Fakultet muvaffaqiyatli yangilandi! ✅');
                
        } catch (\Exception $e) {
            \Log::error('Faculty update error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }

    public function destroy(Faculty $faculty)
    {
        try {
            if ($faculty->departments()->count() > 0) {
                return redirect()
                    ->route('faculties.index')
                    ->with('error', 'Bu fakultetda kafedralar mavjud. Avval kafedralarni o\'chiring! ❌');
            }

            if ($faculty->groups()->count() > 0) {
                return redirect()
                    ->route('faculties.index')
                    ->with('error', 'Bu fakultetda guruhlar mavjud. Avval guruhlarni o\'chiring! ❌');
            }

            $faculty->delete();

            return redirect()
                ->route('faculties.index')
                ->with('success', 'Fakultet muvaffaqiyatli o\'chirildi! ✅');

        } catch (\Exception $e) {
            return redirect()
                ->route('faculties.index')
                ->with('error', 'Fakultetni o\'chirishda xatolik yuz berdi! ❌');
        }
    }
}