<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\DirectionController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\WorkloadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Bosh sahifa - login sahifasiga yo'naltirish
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'active'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Profile Management
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update'); // PATCH → POST
        Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update'); // PUT → POST
        Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Faculties - Fakultetlar
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:faculties.view')->prefix('faculties')->name('faculties.')->group(function () {
        Route::get('/', [FacultyController::class, 'index'])->name('index');
        Route::get('/create', [FacultyController::class, 'create'])->middleware('permission:faculties.create')->name('create');
        Route::post('/', [FacultyController::class, 'store'])->middleware('permission:faculties.create')->name('store');
        Route::get('/{faculty}', [FacultyController::class, 'show'])->name('show');
        Route::get('/{faculty}/edit', [FacultyController::class, 'edit'])->middleware('permission:faculties.edit')->name('edit');
        Route::put('/{faculty}', [FacultyController::class, 'update'])->middleware('permission:faculties.edit')->name('update');
        Route::delete('/{faculty}', [FacultyController::class, 'destroy'])->middleware('permission:faculties.delete')->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Departments - Kafedralar
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:departments.view')->prefix('departments')->name('departments.')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('index');
        Route::get('/create', [DepartmentController::class, 'create'])->middleware('permission:departments.create')->name('create');
        Route::post('/', [DepartmentController::class, 'store'])->middleware('permission:departments.create')->name('store');
        Route::get('/{department}', [DepartmentController::class, 'show'])->name('show');
        Route::get('/{department}/edit', [DepartmentController::class, 'edit'])->middleware('permission:departments.edit')->name('edit');
        Route::put('/{department}', [DepartmentController::class, 'update'])->middleware('permission:departments.edit')->name('update');
        Route::delete('/{department}', [DepartmentController::class, 'destroy'])->middleware('permission:departments.delete')->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Directions - Yo'nalishlar
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth')->prefix('directions')->name('directions.')->group(function () {
        Route::get('/', [DirectionController::class, 'index'])->name('index');
        Route::get('/create', [DirectionController::class, 'create'])->name('create');
        Route::post('/', [DirectionController::class, 'store'])->name('store');
        Route::get('/{direction}', [DirectionController::class, 'show'])->name('show');
        Route::get('/{direction}/edit', [DirectionController::class, 'edit'])->name('edit');
        Route::put('/{direction}', [DirectionController::class, 'update'])->name('update');
        Route::delete('/{direction}', [DirectionController::class, 'destroy'])->name('destroy');

        // API endpoints for AJAX
        Route::get('/faculty/{faculty}/departments', [DirectionController::class, 'getDepartmentsByFaculty'])
            ->name('faculty.departments');
    });
    /*
    |--------------------------------------------------------------------------
    | Teachers - O'qituvchilar
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:teachers.view')->prefix('teachers')->name('teachers.')->group(function () {
        Route::get('/', [TeacherController::class, 'index'])->name('index');
        Route::get('/create', [TeacherController::class, 'create'])->middleware('permission:teachers.create')->name('create');
        Route::post('/', [TeacherController::class, 'store'])->middleware('permission:teachers.create')->name('store');
        Route::get('/{teacher}', [TeacherController::class, 'show'])->name('show');
        Route::get('/{teacher}/edit', [TeacherController::class, 'edit'])->middleware('permission:teachers.edit')->name('edit');
        Route::put('/{teacher}', [TeacherController::class, 'update'])->middleware('permission:teachers.edit')->name('update');
        Route::delete('/{teacher}', [TeacherController::class, 'destroy'])->middleware('permission:teachers.delete')->name('destroy');
    });

    /*
|--------------------------------------------------------------------------
| Subjects - Fanlar
|--------------------------------------------------------------------------
*/
    Route::middleware(['auth', 'verified', 'permission:subjects.view'])->prefix('subjects')->name('subjects.')->group(function () {

        // ==========================================
        // ASOSIY CRUD
        // ==========================================

        // Ro'yxat
        Route::get('/', [SubjectController::class, 'index'])->name('index');
        // Yaratish
        Route::get('/create', [SubjectController::class, 'create'])->name('create')->middleware('permission:subjects.create');

        Route::post('/', [SubjectController::class, 'store'])->name('store')->middleware('permission:subjects.create');

        // ==========================================
        // AJAX / API ROUTES (bular {subject} dan oldin bo'lishi kerak)
        // ==========================================

        // Yo'nalish bo'yicha fanlar
        Route::get('/by-direction/{direction}', [SubjectController::class, 'getByDirection'])->name('by-direction');

        // Kafedra bo'yicha fanlar
        Route::get('/by-department/{department}', [SubjectController::class, 'getByDepartment'])->name('by-department');

        // Kurs darajasi bo'yicha fanlar
        Route::get('/by-course/{courseLevel}', [SubjectController::class, 'getByCourseLevel'])->name('by-course');

        // Fan turi bo'yicha
        Route::get('/by-type/{type}', [SubjectController::class, 'getByType'])->name('by-type');

        // Patok fanlar
        Route::get('/potok-subjects', [SubjectController::class, 'getPotokSubjects'])->name('potok-subjects');

        // ==========================================
        // MASSAVIY AMALLAR
        // ==========================================

        Route::middleware('permission:subjects.edit')->group(function () {
            // Massaviy faollashtirish/faolsizlantirish
            Route::post('/bulk-toggle-active', [SubjectController::class, 'bulkToggleActive'])
                ->name('bulk-toggle-active');

            // Massaviy yangilash
            Route::post('/bulk-update', [SubjectController::class, 'bulkUpdate'])
                ->name('bulk-update');
        });

        Route::middleware('permission:subjects.delete')->group(function () {
            // Massaviy o'chirish
            Route::post('/bulk-delete', [SubjectController::class, 'bulkDelete'])
                ->name('bulk-delete');
        });

        // ==========================================
        // IMPORT / EXPORT
        // ==========================================

        Route::middleware('permission:subjects.create')->group(function () {
            // Import sahifasi
            Route::get('/import', [SubjectController::class, 'importPage'])
                ->name('import');

            // Import jarayoni
            Route::post('/import', [SubjectController::class, 'import'])
                ->name('import.process');

            // Import shablonini yuklab olish
            Route::get('/import/template', [SubjectController::class, 'downloadTemplate'])
                ->name('import.template');
        });

        // Export
        Route::get('/export', [SubjectController::class, 'export'])
            ->name('export')
            ->middleware('permission:subjects.view');

        // ==========================================
        // STATISTIKA VA TAHLIL
        // ==========================================

        // Umumiy statistika
        Route::get('/statistics', [SubjectController::class, 'statistics'])
            ->name('statistics');

        // Kafedra tahlili
        Route::get('/department-analysis/{department}', [SubjectController::class, 'departmentAnalysis'])
            ->name('department-analysis');

        // ==========================================
        // BITTA FAN BILAN ISHLAR (bular oxirida bo'lishi kerak)
        // ==========================================

        // Ko'rish
        Route::get('/{subject}', [SubjectController::class, 'show'])
            ->name('show');

        // Tahrirlash
        Route::get('/{subject}/edit', [SubjectController::class, 'edit'])
            ->name('edit')
            ->can('update', 'subject');

        Route::put('/{subject}', [SubjectController::class, 'update'])
            ->name('update')
            ->can('update', 'subject');

        // O'chirish
        Route::delete('/{subject}', [SubjectController::class, 'destroy'])
            ->name('destroy')
            ->can('delete', 'subject');

        // ==========================================
        // FAN BILAN BOG'LIQ QO'SHIMCHA AMALLAR
        // ==========================================

        // Faollashtirish/Faolsizlantirish
        Route::patch('/{subject}/toggle-active', [SubjectController::class, 'toggleActive'])
            ->name('toggle-active')
            ->can('update', 'subject');

        // Fan soatlari
        Route::get('/{subject}/hours', [SubjectController::class, 'hours'])
            ->name('hours')
            ->can('view', 'subject');

        // Fan statistikasi
        Route::get('/{subject}/statistics', [SubjectController::class, 'subjectStatistics'])
            ->name('subject-statistics')
            ->can('view', 'subject');

        // Fan nusxasini yaratish
        Route::post('/{subject}/duplicate', [SubjectController::class, 'duplicate'])
            ->name('duplicate')
            ->middleware('permission:subjects.create');

        // Fan yuklamalari
        Route::get('/{subject}/workloads', [SubjectController::class, 'workloads'])
            ->name('workloads')
            ->can('view', 'subject');

        // Patok imkoniyatini tekshirish
        Route::post('/{subject}/check-potok', [SubjectController::class, 'checkPotok'])
            ->name('check-potok')
            ->can('view', 'subject');

        // Fan tarixini ko'rish (audit log)
        Route::get('/{subject}/history', [SubjectController::class, 'history'])
            ->name('history')
            ->can('view', 'subject');

        // Tiklash (soft delete dan)
        Route::post('/{subject}/restore', [SubjectController::class, 'restore'])
            ->name('restore')
            ->middleware('permission:subjects.delete')
            ->withTrashed();
    });

    /*
    |--------------------------------------------------------------------------
    | Groups - Guruhlar
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:groups.view')->prefix('groups')->name('groups.')->group(function () {
        Route::get('/', [GroupController::class, 'index'])->name('index');
        Route::get('/create', [GroupController::class, 'create'])->middleware('permission:groups.create')->name('create');
        Route::post('/', [GroupController::class, 'store'])->middleware('permission:groups.create')->name('store');
        Route::get('/{group}', [GroupController::class, 'show'])->name('show');
        Route::get('/{group}/edit', [GroupController::class, 'edit'])->middleware('permission:groups.edit')->name('edit');
        Route::put('/{group}', [GroupController::class, 'update'])->middleware('permission:groups.edit')->name('update');
        Route::delete('/{group}', [GroupController::class, 'destroy'])->middleware('permission:groups.delete')->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Academic Years - O'quv Yillari
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,dekan')->prefix('academic-years')->name('academic-years.')->group(function () {
        Route::get('/', [AcademicYearController::class, 'index'])->name('index');
        Route::get('/create', [AcademicYearController::class, 'create'])->name('create');
        Route::post('/', [AcademicYearController::class, 'store'])->name('store');
        Route::get('/{academicYear}', [AcademicYearController::class, 'show'])->name('show');
        Route::get('/{academicYear}/edit', [AcademicYearController::class, 'edit'])->name('edit');
        Route::put('/{academicYear}', [AcademicYearController::class, 'update'])->name('update');
        Route::delete('/{academicYear}', [AcademicYearController::class, 'destroy'])->name('destroy');
        Route::post('/{academicYear}/set-current', [AcademicYearController::class, 'setCurrent'])->name('set-current');
    });


// AJAX endpoints - auth required
    Route::middleware(['auth'])->prefix('workloads/ajax')->name('workloads.ajax.')->group(function () {
        Route::get('/subject/{subjectId}/details', [WorkloadController::class, 'getSubjectDetails'])->name('subject-details');
        Route::post('/check-groups-status', [WorkloadController::class, 'checkGroupsStatus'])->name('check-groups-status');
    });

// Main workload routes
    Route::middleware(['auth'])->prefix('workloads')->name('workloads.')->group(function () {
        // Static routes
        Route::get('/', [WorkloadController::class, 'index'])->name('index');
        Route::get('/create', [WorkloadController::class, 'create'])->name('create');
        Route::post('/', [WorkloadController::class, 'store'])->name('store');

        // Parametric routes last
        Route::get('/{workload}', [WorkloadController::class, 'show'])->name('show');
        Route::get('/{workload}/edit', [WorkloadController::class, 'edit'])->name('edit');
        Route::put('/{workload}', [WorkloadController::class, 'update'])->name('update');
        Route::delete('/{workload}', [WorkloadController::class, 'destroy'])->name('destroy');
        Route::post('/{workload}/remainder', [WorkloadController::class, 'createRemainder'])->name('create-remainder');


        // Tasdiqlash tizimi
        Route::post('/{workload}/submit',  [WorkloadController::class, 'submit']) ->name('submit');
        Route::post('/{workload}/approve', [WorkloadController::class, 'approve'])->name('approve');
        Route::post('/{workload}/reject',  [WorkloadController::class, 'reject']) ->name('reject');

        Route::get('/ajax/rating-status', [WorkloadController::class, 'getRatingStatus']);
    });


    /*
    |--------------------------------------------------------------------------
    | Reports - Hisobotlar
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:reports.view')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');

        // O'qituvchi bo'yicha hisobot
        Route::get('/teacher/{teacher}', [ReportController::class, 'teacher'])->name('teacher');

        // Kafedra bo'yicha hisobot
        Route::get('/department/{department}', [ReportController::class, 'department'])->name('department');

        // Fakultet bo'yicha hisobot
        Route::get('/faculty/{faculty}', [ReportController::class, 'faculty'])->name('faculty');

        // Export
        Route::middleware('permission:reports.export')->group(function () {
            Route::get('/export/excel', [ReportController::class, 'exportExcel'])->name('export.excel');
            Route::get('/export/pdf', [ReportController::class, 'exportPdf'])->name('export.pdf');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Panel - Faqat Admin uchun
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {

        // Users Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('destroy');

            // Toggle active status
            Route::post('/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Roles Management
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\RoleController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('store');
            Route::get('/{role}/edit', [\App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('edit');
            Route::put('/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('update');
            Route::delete('/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('destroy');

            // Permissions assignment
            Route::get('/{role}/permissions', [\App\Http\Controllers\Admin\RoleController::class, 'permissions'])->name('permissions');
            Route::post('/{role}/permissions', [\App\Http\Controllers\Admin\RoleController::class, 'updatePermissions'])->name('permissions.update');
        });

        // Permissions Management
        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\PermissionController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\PermissionController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\PermissionController::class, 'store'])->name('store');
            Route::get('/{permission}/edit', [\App\Http\Controllers\Admin\PermissionController::class, 'edit'])->name('edit');
            Route::put('/{permission}', [\App\Http\Controllers\Admin\PermissionController::class, 'update'])->name('update');
            Route::delete('/{permission}', [\App\Http\Controllers\Admin\PermissionController::class, 'destroy'])->name('destroy');
        });


        // ============================================
        // SETTINGS ROUTES
        // ============================================
        Route::prefix('settings')->name('settings.')->group(function () {
            // Main
            Route::get('/', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('index');
            Route::put('/{setting}', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('update');
            Route::put('/', [\App\Http\Controllers\Admin\SettingController::class, 'bulkUpdate'])->name('bulk-update');

            // Import/Export
            Route::get('/export', [\App\Http\Controllers\Admin\SettingController::class, 'export'])->name('export');
            Route::post('/import', [\App\Http\Controllers\Admin\SettingController::class, 'import'])->name('import');

            // Logo
            Route::post('/upload-logo', [\App\Http\Controllers\Admin\SettingController::class, 'uploadLogo'])->name('upload-logo');
        });

        // ============================================
        // BACKUP ROUTES
        // ============================================
        Route::prefix('backups')->name('backups.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\BackupController::class, 'create'])->name('create');
            Route::get('/{filename}/download', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('download');
            Route::delete('/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('destroy');
            Route::post('/cleanup', [\App\Http\Controllers\Admin\BackupController::class, 'cleanup'])->name('cleanup');
            Route::post('/{filename}/restore', [\App\Http\Controllers\Admin\BackupController::class, 'restore'])->name('restore');
        });

        // ============================================
        // LOGS ROUTES
        // ============================================
        Route::prefix('logs')->name('logs.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\LogController::class, 'index'])->name('index');
            Route::delete('/', [\App\Http\Controllers\Admin\LogController::class, 'clear'])->name('clear');
            Route::get('/download', [\App\Http\Controllers\Admin\LogController::class, 'download'])->name('download');
        });

        // ============================================
        // SYSTEM ROUTES
        // ============================================
        Route::post('/system/action', [\App\Http\Controllers\Admin\LogController::class, 'systemAction'])->name('system.action');
        Route::get('/system/info', [\App\Http\Controllers\Admin\LogController::class, 'systemInfo'])->name('system.info');
    });

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/mark-as-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/mark-all-as-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
        Route::delete('/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
