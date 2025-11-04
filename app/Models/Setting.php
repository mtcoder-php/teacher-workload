<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $key
 * @property mixed $value
 * @property string $type
 * @property string $group
 * @property string $label
 * @property string|null $description
 * @property bool $is_public
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Boot model events
     */
    protected static function boot()
    {
        parent::boot();

        // Sozlama o'zgarganda cache'ni tozalash
        static::saved(function () {
            Cache::forget('settings.all');
            Cache::forget('settings.public');
        });

        static::deleted(function () {
            Cache::forget('settings.all');
            Cache::forget('settings.public');
        });
    }

    /**
     * Qiymatni to'g'ri formatda qaytarish
     */
    public function getValueAttribute($value)
    {
        return $this->castValue($value, $this->type);
    }

    /**
     * Qiymatni saqlashdan oldin formatga keltirish
     */
    public function setValueAttribute($value)
    {
        $this->attributes['value'] = $this->prepareValue($value, $this->type);
    }

    /**
     * Type bo'yicha qiymatni cast qilish
     */
    protected function castValue($value, $type)
    {
        return match($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($value) ? (float)$value : 0,
            'json' => json_decode($value, true) ?? [],
            default => $value,
        };
    }

    /**
     * Type bo'yicha qiymatni tayyorlash
     */
    protected function prepareValue($value, $type)
    {
        return match($type) {
            'boolean' => $value ? '1' : '0',
            'number' => (string)$value,
            'json' => is_array($value) ? json_encode($value) : $value,
            default => $value,
        };
    }

    /**
     * Barcha sozlamalarni olish (cached)
     */
    public static function getAllSettings()
    {
        return Cache::remember('settings.all', 3600, function () {
            return static::all()->keyBy('key')->map->value;
        });
    }

    /**
     * Bitta sozlamani olish
     */
    public static function get($key, $default = null)
    {
        $settings = static::getAllSettings();
        return $settings[$key] ?? $default;
    }

    /**
     * Sozlamani o'rnatish
     */
    public static function set($key, $value)
    {
        $setting = static::firstOrCreate(['key' => $key]);
        $setting->value = $value;
        $setting->save();

        return $setting;
    }

    /**
     * Guruh bo'yicha sozlamalarni olish
     */
    public static function getByGroup($group)
    {
        return static::where('group', $group)->get();
    }

    /**
     * Public sozlamalarni olish
     */
    public static function getPublicSettings()
    {
        return Cache::remember('settings.public', 3600, function () {
            return static::where('is_public', true)
                ->get()
                ->keyBy('key')
                ->map->value;
        });
    }
}