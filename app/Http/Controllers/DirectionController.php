<?php

namespace App\Http\Controllers;

use App\Models\Direction;
use App\Models\Department;
use App\Http\Requests\StoreDirectionRequest;
use App\Http\Requests\UpdateDirectionRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DirectionController extends Controller
{
    public function index(Request $request)
    {
        $query = Direction::with(['department.faculty']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by degree type
        if ($request->filled('degree_type')) {
            $query->where('degree_type', $request->degree_type);
        }

        $directions = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('Directions/Index', [
            'directions' => $directions,
            'departments' => Department::with('faculty')
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'filters' => $request->only([
                'search',
                'department_id',
                'degree_type'
            ])
        ]);
    }

    public function create()
    {
        return Inertia::render('Directions/Create', [
            'departments' => Department::with('faculty')
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreDirectionRequest $request)
    {
        try {
            Direction::create($request->validated());

            return redirect()->route('directions.index')
                ->with('success', 'Yo\'nalish muvaffaqiyatli yaratildi! ✅');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }

    public function show(Direction $direction)
    {
        $direction->load(['department.faculty', 'groups']);

        return Inertia::render('Directions/Show', [
            'direction' => $direction
        ]);
    }

    public function edit(Direction $direction)
    {
        $direction->load(['department.faculty']);

        return Inertia::render('Directions/Edit', [
            'direction' => $direction,
            'departments' => Department::with('faculty')
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(UpdateDirectionRequest $request, Direction $direction)
    {
        try {
            $direction->update($request->validated());

            return redirect()->route('directions.index')
                ->with('success', 'Yo\'nalish muvaffaqiyatli yangilandi! ✅');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }

    public function destroy(Direction $direction)
    {
        try {
            // Check if direction has groups
            if ($direction->groups()->count() > 0) {
                return redirect()->route('directions.index')
                    ->with('error', 'Bu yo\'nalishda guruhlar mavjud. Avval guruhlarni o\'chiring! ❌');
            }

            $direction->delete();

            return redirect()->route('directions.index')
                ->with('success', 'Yo\'nalish muvaffaqiyatli o\'chirildi! ✅');
        } catch (\Exception $e) {
            return redirect()->route('directions.index')
                ->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }
}
