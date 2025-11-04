<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\Group;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Workload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Admin uchun
        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        // Dekan uchun
        if ($user->isDean()) {
            return $this->deanDashboard();
        }

        // Kafedra mudiri uchun
        if ($user->isDepartmentHead()) {
            return $this->departmentHeadDashboard();
        }

        // O'qituvchi uchun
        if ($user->isTeacher()) {
            return $this->teacherDashboard();
        }

        // Default dashboard
        return Inertia::render('Dashboard', [
            'stats' => [
                'total_workloads' => 0,
                'teachers_count' => 0,
                'active_teachers' => 0,
                'subjects_count' => 0,
                'active_subjects' => 0,
                'pending_approvals' => 0,
            ],
            'recentActivities' => [],
        ]);
    }

    protected function adminDashboard()
    {
        $stats = [
            'total_workloads' => Workload::count(),
            'teachers_count' => Teacher::where('is_active', true)->count(),
            'active_teachers' => Teacher::where('is_active', true)->count(),
            'subjects_count' => Subject::where('is_active', true)->count(),
            'active_subjects' => Subject::where('is_active', true)->count(),
            
            // ✅ TO'G'RILANDI: Barcha tasdiqlanmagan yuklamalar
            'pending_approvals' => Workload::where('status', '!=', 'approved')
                ->orWhereNull('approved_at')
                ->count(),
        ];

        // So'nggi faoliyatlar
        $recentActivities = Workload::with(['teacher.user', 'subject'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($workload) {
                return [
                    'id' => $workload->id,
                    'title' => 'Yangi yuklama qo\'shildi',
                    'description' => $workload->teacher->user->name . ' - ' . $workload->subject->name,
                    'time' => $workload->created_at->diffForHumans(),
                    'status' => $workload->status,
                ];
            });

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentActivities' => $recentActivities,
        ]);
    }

    protected function deanDashboard()
    {
        $user = Auth::user();
        
        // Dekan boshqaradigan fakultet
        $faculty = Faculty::where('dean_id', $user->id)->first();

        if (!$faculty) {
            return Inertia::render('Dashboard', [
                'stats' => [
                    'total_workloads' => 0,
                    'teachers_count' => 0,
                    'active_teachers' => 0,
                    'subjects_count' => 0,
                    'active_subjects' => 0,
                    'pending_approvals' => 0,
                ],
                'recentActivities' => [],
            ]);
        }

        $stats = [
            'total_workloads' => Workload::whereHas('teacher.department', function ($query) use ($faculty) {
                $query->where('faculty_id', $faculty->id);
            })->count(),
            
            'teachers_count' => Teacher::whereHas('department', function ($query) use ($faculty) {
                $query->where('faculty_id', $faculty->id);
            })->where('is_active', true)->count(),
            
            'active_teachers' => Teacher::whereHas('department', function ($query) use ($faculty) {
                $query->where('faculty_id', $faculty->id);
            })->where('is_active', true)->count(),
            
            'subjects_count' => Subject::whereHas('department', function ($query) use ($faculty) {
                $query->where('faculty_id', $faculty->id);
            })->where('is_active', true)->count(),
            
            'active_subjects' => Subject::whereHas('department', function ($query) use ($faculty) {
                $query->where('faculty_id', $faculty->id);
            })->where('is_active', true)->count(),
            
            // ✅ TO'G'RILANDI
            'pending_approvals' => Workload::whereHas('teacher.department', function ($query) use ($faculty) {
                $query->where('faculty_id', $faculty->id);
            })
            ->where(function($query) {
                $query->where('status', '!=', 'approved')
                      ->orWhereNull('approved_at');
            })
            ->count(),
        ];

        $recentActivities = Workload::whereHas('teacher.department', function ($query) use ($faculty) {
            $query->where('faculty_id', $faculty->id);
        })
        ->with(['teacher.user', 'subject'])
        ->latest()
        ->take(5)
        ->get()
        ->map(function ($workload) {
            return [
                'id' => $workload->id,
                'title' => $workload->subject->name,
                'description' => $workload->teacher->user->name . ' - ' . $workload->status_name,
                'time' => $workload->created_at->diffForHumans(),
            ];
        });

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentActivities' => $recentActivities,
        ]);
    }

    protected function departmentHeadDashboard()
    {
        $user = Auth::user();
        
        // Kafedra mudiri boshqaradigan kafedra
        $department = Department::where('head_id', $user->id)->first();

        if (!$department) {
            return Inertia::render('Dashboard', [
                'stats' => [
                    'total_workloads' => 0,
                    'teachers_count' => 0,
                    'active_teachers' => 0,
                    'subjects_count' => 0,
                    'active_subjects' => 0,
                    'pending_approvals' => 0,
                ],
                'recentActivities' => [],
            ]);
        }

        $stats = [
            'total_workloads' => Workload::whereHas('teacher', function ($query) use ($department) {
                $query->where('department_id', $department->id);
            })->count(),
            
            'teachers_count' => $department->teachers()->where('is_active', true)->count(),
            'active_teachers' => $department->teachers()->where('is_active', true)->count(),
            'subjects_count' => $department->subjects()->where('is_active', true)->count(),
            'active_subjects' => $department->subjects()->where('is_active', true)->count(),
            
            // ✅ TO'G'RILANDI
            'pending_approvals' => Workload::whereHas('teacher', function ($query) use ($department) {
                $query->where('department_id', $department->id);
            })
            ->where(function($query) {
                $query->where('status', '!=', 'approved')
                      ->orWhereNull('approved_at');
            })
            ->count(),
        ];

        $recentActivities = Workload::whereHas('teacher', function ($query) use ($department) {
            $query->where('department_id', $department->id);
        })
        ->with(['teacher.user', 'subject', 'group'])
        ->latest()
        ->take(5)
        ->get()
        ->map(function ($workload) {
            return [
                'id' => $workload->id,
                'title' => $workload->subject->name,
                'description' => $workload->teacher->user->name . ' - ' . $workload->group->name,
                'time' => $workload->created_at->diffForHumans(),
            ];
        });

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentActivities' => $recentActivities,
        ]);
    }

    protected function teacherDashboard()
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return Inertia::render('Dashboard', [
                'stats' => [
                    'total_workloads' => 0,
                    'teachers_count' => 0,
                    'active_teachers' => 0,
                    'subjects_count' => 0,
                    'active_subjects' => 0,
                    'pending_approvals' => 0,
                ],
                'recentActivities' => [],
            ]);
        }

        // Joriy semestr yuklamalari
        $currentWorkloads = Workload::where('teacher_id', $teacher->id)
            ->whereHas('semester', function ($query) {
                $query->where('is_current', true);
            })
            ->with(['subject', 'semester', 'group'])
            ->get();

        $stats = [
            'total_workloads' => $currentWorkloads->sum('total_hours'),
            'teachers_count' => 0,
            'active_teachers' => 0,
            'subjects_count' => $currentWorkloads->count(),
            'active_subjects' => $currentWorkloads->count(),
            
            // ✅ TO'G'RILANDI
            'pending_approvals' => $currentWorkloads->filter(function($workload) {
                return $workload->status !== 'approved' || is_null($workload->approved_at);
            })->count(),
        ];

        $recentActivities = $currentWorkloads->take(5)->map(function ($workload) {
            return [
                'id' => $workload->id,
                'title' => $workload->subject->name,
                'description' => $workload->group->name . ' - ' . $workload->total_hours . ' soat',
                'time' => $workload->created_at->diffForHumans(),
                'status' => $workload->status_name,
            ];
        });

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'currentWorkloads' => $currentWorkloads,
        ]);
    }
}