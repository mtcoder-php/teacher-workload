<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\BulkUpdateSettingsRequest;
use App\Http\Requests\Settings\ImportSettingsRequest;
use App\Http\Requests\Settings\UpdateSettingRequest;
use App\Http\Requests\Settings\UploadLogoRequest;
use App\Models\Setting;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SettingController extends Controller
{
    /**
     * Sozlamalar sahifasi
     */
    public function index()
    {
        try {
            $settings = Setting::all()->groupBy('group');

            $systemInfo = [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'database' => config('database.default'),
                'cache_driver' => config('cache.default'),
                'queue_driver' => config('queue.default'),
                'timezone' => config('app.timezone'),
                'locale' => config('app.locale'),
                'debug_mode' => config('app.debug'),
                'environment' => app()->environment(),
            ];

            $totalSpace = disk_total_space(base_path());
            $freeSpace = disk_free_space(base_path());
            
            $diskInfo = [
                'total' => $totalSpace,
                'free' => $freeSpace,
                'used' => $totalSpace - $freeSpace,
                'used_percentage' => $totalSpace > 0 
                    ? round((($totalSpace - $freeSpace) / $totalSpace) * 100, 2) 
                    : 0,
            ];

            $cacheInfo = [
                'config_cached' => File::exists(base_path('bootstrap/cache/config.php')),
                'routes_cached' => File::exists(base_path('bootstrap/cache/routes-v7.php')),
                'events_cached' => File::exists(base_path('bootstrap/cache/events.php')),
            ];

            return Inertia::render('Admin/Settings/Index', [
                'settings' => $settings,
                'systemInfo' => $systemInfo,
                'diskInfo' => $diskInfo,
                'cacheInfo' => $cacheInfo,
            ]);
        } catch (\Exception $e) {
            Log::error('Settings index error: ' . $e->getMessage());
            
            return Inertia::render('Admin/Settings/Index', [
                'settings' => collect([]),
                'systemInfo' => [],
                'diskInfo' => [],
                'cacheInfo' => [],
            ])->with('error', 'Sozlamalarni yuklashda xatolik');
        }
    }

    /**
     * Sozlamani yangilash
     */
    public function update(UpdateSettingRequest $request, Setting $setting)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            if ($setting->type === 'boolean') {
                $validated['value'] = in_array($validated['value'], [1, '1', 'true', true], true) ? '1' : '0';
            }
            
            $setting->value = $validated['value'];
            $setting->save();

            try {
                $admins = \App\Models\User::whereHas('role', function($q) {
                    $q->where('name', 'admin');
                })->where('id', '!=', auth()->id())->get();

                foreach ($admins as $admin) {
                    NotificationService::info(
                        $admin,
                        'Sozlama o\'zgartirildi',
                        auth()->user()->name . " tomonidan '{$setting->label}' sozlamasi o'zgartirildi",
                        ['setting_key' => $setting->key]
                    );
                }
            } catch (\Exception $e) {
                Log::warning('Notification error: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->back()
                ->with('success', "'{$setting->label}' muvaffaqiyatli yangilandi");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Setting update error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Ko'p sozlamalarni yangilash
     */
    public function bulkUpdate(BulkUpdateSettingsRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $updatedCount = 0;
            
            foreach ($validated['settings'] as $settingData) {
                $setting = Setting::find($settingData['id']);
                
                if ($setting) {
                    if ($setting->type === 'boolean') {
                        $settingData['value'] = in_array($settingData['value'], [1, '1', 'true', true], true) ? '1' : '0';
                    }
                    
                    $setting->value = $settingData['value'];
                    $setting->save();
                    $updatedCount++;
                }
            }

            DB::commit();

            return redirect()->back()
                ->with('success', "{$updatedCount} ta sozlama saqlandi");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk update error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Eksport (JSON)
     */
    public function export()
    {
        try {
            $settings = Setting::all();
            $filename = 'settings-export-' . now()->format('Y-m-d-His') . '.json';
            
            $data = [
                'exported_at' => now()->toDateTimeString(),
                'exported_by' => auth()->user()->name,
                'settings' => $settings->toArray(),
            ];
            
            Log::info('Settings exported', [
                'user' => auth()->user()->name,
                'count' => $settings->count(),
            ]);
            
            return response()->json($data)
                ->header('Content-Type', 'application/json')
                ->header('Content-Disposition', "attachment; filename={$filename}");
            
        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Eksport xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Import (JSON)
     */
    public function import(ImportSettingsRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $content = File::get($file->getRealPath());
            $data = json_decode($content, true);
            
            if (!isset($data['settings'])) {
                throw new \Exception('Noto\'g\'ri fayl formati');
            }
            
            $importedCount = 0;
            
            foreach ($data['settings'] as $settingData) {
                Setting::updateOrCreate(
                    ['key' => $settingData['key']],
                    $settingData
                );
                $importedCount++;
            }
            
            DB::commit();
            
            Log::info('Settings imported', [
                'user' => auth()->user()->name,
                'count' => $importedCount,
            ]);
            
            return redirect()->back()
                ->with('success', "{$importedCount} ta sozlama import qilindi");
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Import xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Logo yuklash
     */
    public function uploadLogo(UploadLogoRequest $request)
    {
        DB::beginTransaction();
        try {
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $filename = 'logo-' . time() . '.' . $file->getClientOriginalExtension();
                
                $oldLogo = Setting::where('key', 'app_logo')->value('value');
                if ($oldLogo && File::exists(public_path($oldLogo))) {
                    File::delete(public_path($oldLogo));
                }
                
                $path = $file->storeAs('logos', $filename, 'public');
                
                Setting::updateOrCreate(
                    ['key' => 'app_logo'],
                    [
                        'value' => '/storage/' . $path,
                        'type' => 'text',
                        'group' => 'general',
                        'label' => 'Logo',
                        'description' => 'Tizim logosi',
                        'is_public' => true,
                    ]
                );

                DB::commit();
                Log::info("Logo uploaded: {$filename}");

                return redirect()->back()
                    ->with('success', 'Logo muvaffaqiyatli yuklandi');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Logo upload error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Logo yuklash xatolik: ' . $e->getMessage());
        }
    }
}