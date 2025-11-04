<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'is_active',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Avatar URL ni olish
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return null;
    }

    /**
     * Foydalanuvchining roli
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Foydalanuvchining o'qituvchi profili
     */
    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    /**
     * Foydalanuvchi berilgan rolga egami?
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role?->name === $roleName;
    }

    /**
     * Foydalanuvchi berilgan ruxsatga egami?
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->role?->hasPermission($permissionName) ?? false;
    }

    /**
     * Foydalanuvchi aktiv holatdami?
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Foydalanuvchi adminmi?
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Foydalanuvchi dekanmi?
     */
    public function isDean(): bool
    {
        return $this->hasRole('dekan');
    }

    /**
     * Foydalanuvchi kafedra mudirimmi?
     */
    public function isDepartmentHead(): bool
    {
        return $this->hasRole('kafedra_mudiri');
    }

    /**
     * Foydalanuvchi o'qituvchimi?
     */
    public function isTeacher(): bool
    {
        return $this->hasRole('oqituvchi');
    }


    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Laravel Gates uchun can() metodini override qilamiz
     */
public function can($ability, $arguments = []): bool
{
    // Agar argument berilgan bo'lsa (Policy check), parent'ga yo'naltirish
    if (!empty($arguments)) {
        return parent::can($ability, $arguments);
    }
    
    // Agar faqat permission nomi berilgan bo'lsa
    if (is_string($ability) && !method_exists($this, $ability)) {
        return $this->hasPermission($ability);
    }

    // Boshqa holatlarda parent metodini chaqiramiz
    return parent::can($ability, $arguments);
}
    
}
