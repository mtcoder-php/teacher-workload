<?php

namespace App\Providers;

use App\Models\Subject;
use App\Models\Workload;
use App\Models\Teacher;
use App\Models\Direction;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // Rate Limiting
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Custom rate limit for workloads
        RateLimiter::for('workloads', function (Request $request) {
            return Limit::perMinute(100)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            // API routes (agar mavjud bo'lsa)
            if (file_exists(base_path('routes/api.php'))) {
                Route::middleware('api')
                    ->prefix('api')
                    ->group(base_path('routes/api.php'));
            }

            // Web routes
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        // ==========================================
        // ROUTE MODEL BINDINGS
        // ==========================================

        // WORKLOAD MODEL BINDINGS
        $this->bindWorkloadModels();

        // SUBJECT MODEL BINDINGS
        $this->bindSubjectModels();

        // OTHER MODEL BINDINGS
        $this->bindOtherModels();
    }

    /**
     * Workload model bindings
     */
    private function bindWorkloadModels(): void
    {
        // Workload - ID bo'yicha (soft delete bilan)
        Route::bind('workload', function ($value) {
            return Workload::with([
                'subject',
                'teacher.user',
                'teacher.department',
                'direction',
                'academicYear',
                'approver',
            ])->findOrFail($value);
        });

        // Workload - o'chirilganlar bilan (soft delete)
        Route::bind('workload_with_trashed', function ($value) {
            return Workload::with([
                'subject',
                'teacher',
                'direction',
                'academicYear',
            ])->withTrashed()->findOrFail($value);
        });

        // Workload - faqat confirmed
        Route::bind('workload_confirmed', function ($value) {
            return Workload::where('status', 'confirmed')
                ->with(['subject', 'teacher', 'direction', 'academicYear'])
                ->findOrFail($value);
        });

        // Workload - faqat draft
        Route::bind('workload_draft', function ($value) {
            return Workload::where('status', 'draft')
                ->with(['subject', 'teacher', 'direction', 'academicYear'])
                ->findOrFail($value);
        });
    }

    /**
     * Subject model bindings
     */
    private function bindSubjectModels(): void
    {
        // Subject - ID yoki code bo'yicha
        Route::bind('subject', function ($value) {
            if (is_numeric($value)) {
                return Subject::findOrFail($value);
            }
            
            return Subject::where('code', strtoupper($value))
                ->firstOrFail();
        });

        // Subject - o'chirilganlar bilan
        Route::bind('subject_with_trashed', function ($value) {
            if (is_numeric($value)) {
                return Subject::withTrashed()->findOrFail($value);
            }
            
            return Subject::withTrashed()
                ->where('code', strtoupper($value))
                ->firstOrFail();
        });
    }

    /**
     * Other model bindings
     */
    private function bindOtherModels(): void
    {
        // Teacher - ID bo'yicha
        Route::bind('teacher', function ($value) {
            return Teacher::with(['user', 'department.faculty'])
                ->findOrFail($value);
        });

        // Direction - ID yoki slug bo'yicha
        Route::bind('direction', function ($value) {
            if (is_numeric($value)) {
                return Direction::findOrFail($value);
            }
            
            return Direction::where('slug', $value)
                ->firstOrFail();
        });

        // AcademicYear - ID bo'yicha
        Route::bind('academicYear', function ($value) {
            return AcademicYear::findOrFail($value);
        });

        // Semester - ID bo'yicha
        Route::bind('semester', function ($value) {
            return Semester::with('academicYear')
                ->findOrFail($value);
        });
    }
}

?>