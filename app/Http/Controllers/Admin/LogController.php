<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\SystemActionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class LogController extends Controller
{
    // ============================================
    // SYSTEM ACTIONS
    // ============================================
    
    /**
     * Tizim amallarini bajarish
     */
    public function systemAction(SystemActionRequest $request)
    {
        $validated = $request->validated();

        try {
            $message = match($validated['action']) {
                'cache_clear' => $this->clearCache(),
                'config_cache' => $this->cacheConfig(),
                'route_cache' => $this->cacheRoutes(),
                'view_cache' => $this->cacheViews(),
                'optimize' => $this->optimize(),
                'clear_all' => $this->clearAll(),
            };

            Log::info("System action executed: {$validated['action']}", [
                'user' => auth()->user()->name,
            ]);

            return redirect()->back()
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('System action error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Cache tozalash
     */
    private function clearCache(): string
    {
        try {
            Artisan::call('cache:clear');
            Cache::flush();
            return 'Cache tozalandi ✓';
        } catch (\Exception $e) {
            throw new \Exception('Cache tozalashda xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Config cache
     */
    private function cacheConfig(): string
    {
        try {
            Artisan::call('config:cache');
            return 'Config cache yaratildi ✓';
        } catch (\Exception $e) {
            throw new \Exception('Config cache xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Route cache
     */
    private function cacheRoutes(): string
    {
        try {
            Artisan::call('route:cache');
            return 'Route cache yaratildi ✓';
        } catch (\Exception $e) {
            throw new \Exception('Route cache xatolik: ' . $e->getMessage());
        }
    }

    /**
     * View cache
     */
    private function cacheViews(): string
    {
        try {
            Artisan::call('view:cache');
            return 'View cache yaratildi ✓';
        } catch (\Exception $e) {
            throw new \Exception('View cache xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Optimize
     */
    private function optimize(): string
    {
        try {
            Artisan::call('optimize');
            return 'Tizim optimallashtirildi ✓';
        } catch (\Exception $e) {
            throw new \Exception('Optimize xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Barchasini tozalash
     */
    private function clearAll(): string
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Artisan::call('optimize:clear');
            
            return 'Barcha cache\'lar tozalandi ✓';
        } catch (\Exception $e) {
            throw new \Exception('Tozalash xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Batafsil tizim ma'lumotlari
     */
    public function systemInfo()
    {
        try {
            $info = [
                'php' => [
                    'version' => PHP_VERSION,
                    'memory_limit' => ini_get('memory_limit'),
                    'max_execution_time' => ini_get('max_execution_time'),
                    'upload_max_filesize' => ini_get('upload_max_filesize'),
                    'post_max_size' => ini_get('post_max_size'),
                    'max_input_vars' => ini_get('max_input_vars'),
                ],
                'laravel' => [
                    'version' => app()->version(),
                    'environment' => app()->environment(),
                    'debug_mode' => config('app.debug'),
                    'timezone' => config('app.timezone'),
                    'locale' => config('app.locale'),
                    'url' => config('app.url'),
                ],
                'database' => [
                    'driver' => config('database.default'),
                    'host' => config('database.connections.mysql.host'),
                    'port' => config('database.connections.mysql.port'),
                    'database' => config('database.connections.mysql.database'),
                ],
                'cache' => [
                    'driver' => config('cache.default'),
                ],
                'queue' => [
                    'driver' => config('queue.default'),
                ],
                'mail' => [
                    'driver' => config('mail.default'),
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                ],
                'session' => [
                    'driver' => config('session.driver'),
                    'lifetime' => config('session.lifetime'),
                ],
            ];
            
            return Inertia::render('Admin/Settings/SystemInfo', [
                'systemInfo' => $info,
            ]);
            
        } catch (\Exception $e) {
            Log::error('System info error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Tizim ma\'lumotlari xatolik');
        }
    }

    // ============================================
    // LOGS
    // ============================================
    
    /**
     * Loglar sahifasi
     */
    public function index()
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            
            if (!File::exists($logFile)) {
                return Inertia::render('Admin/Settings/Logs', [
                    'logs' => [],
                    'message' => 'Log fayli topilmadi'
                ]);
            }

            $logs = File::get($logFile);
            $logsArray = explode("\n", $logs);
            $logsArray = array_filter($logsArray);
            $logsArray = array_slice(array_reverse($logsArray), 0, 100); // Last 100 lines

            return Inertia::render('Admin/Settings/Logs', [
                'logs' => $logsArray,
                'fileSize' => File::size($logFile),
                'lastModified' => File::lastModified($logFile),
            ]);
        } catch (\Exception $e) {
            Log::error('View logs error: ' . $e->getMessage());
            
            return Inertia::render('Admin/Settings/Logs', [
                'logs' => [],
                'message' => 'Loglarni o\'qishda xatolik'
            ]);
        }
    }

    /**
     * Loglarni tozalash
     */
    public function clear()
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            
            if (File::exists($logFile)) {
                // Backup yaratish
                $backupFile = storage_path('logs/laravel-backup-' . now()->format('Y-m-d-His') . '.log');
                File::copy($logFile, $backupFile);
                
                // Asosiy log'ni tozalash
                File::put($logFile, '');
                
                Log::info('Logs cleared', [
                    'user' => auth()->user()->name,
                ]);
            }

            return redirect()->back()
                ->with('success', 'Loglar tozalandi (backup yaratildi)');

        } catch (\Exception $e) {
            Log::error('Clear logs error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Loglar tozalash xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Loglarni yuklab olish
     */
    public function download()
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            
            if (!File::exists($logFile)) {
                throw new \Exception('Log fayli topilmadi');
            }
            
            $filename = 'logs-' . now()->format('Y-m-d-His') . '.log';
            
            Log::info("Logs downloaded", [
                'user' => auth()->user()->name,
            ]);
            
            return response()->download($logFile, $filename);
            
        } catch (\Exception $e) {
            Log::error('Download logs error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Loglar yuklab olish xatolik: ' . $e->getMessage());
        }
    }
}