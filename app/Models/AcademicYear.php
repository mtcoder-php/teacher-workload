<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }


    /**
     * O'quv yiliga tegishli yuklamalar
     */
    public function workloads(): HasMany
    {
        return $this->hasMany(Workload::class);
    }
     /**
     * Guruh ga tegishli yuklamalar
     */
    public function groups()
    {
        return $this->hasMany(Group::class);
    }
    /**
     * Joriy o'quv yilini olish
     */
    public static function current()
    {
        return static::where('is_active', true)->first();
    }



    /**
     * O'quv yilini aktivlashtirish
     * Boshqa barcha o'quv yillarini deaktiv qiladi
     */
    public function activate(): void
    {
        // Barcha o'quv yillarni deaktiv qilish
        static::where('is_active', true)
            ->where('id', '!=', $this->id)
            ->update(['is_active' => false]);

        // Bu o'quv yilini aktiv qilish
        $this->update(['is_active' => true]);
    }

    /**
     * Model eventlari
     */
    protected static function booted(): void
    {
        // Yangi o'quv yili yaratilganda, agar is_active true bo'lsa
        // boshqa barcha o'quv yillarni false qilish
        static::creating(function ($academicYear) {
            if ($academicYear->is_active) {
                static::where('is_active', true)->update(['is_active' => false]);
            }
        });

        // Yangilanganda ham xuddi shunday
        static::updating(function ($academicYear) {
            if ($academicYear->is_active && $academicYear->isDirty('is_active')) {
                static::where('is_active', true)
                    ->where('id', '!=', $academicYear->id)
                    ->update(['is_active' => false]);
            }
        });
    }

    /**
     * O'quv yili formatini olish
     */
    public function getFormattedNameAttribute(): string
    {
        return "{$this->name} o'quv yili";
    }

    /**
     * O'quv yili holatini tekshirish
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * O'quv yili davom etayotganligini tekshirish
     */
    public function isOngoing(): bool
    {
        $now = now();
        return $now->between($this->start_date, $this->end_date);
    }

    /**
     * O'quv yili tugaganligini tekshirish
     */
    public function isCompleted(): bool
    {
        return now()->greaterThan($this->end_date);
    }

    /**
     * O'quv yili boshlanmaganligini tekshirish
     */
    public function isPending(): bool
    {
        return now()->lessThan($this->start_date);
    }


    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
