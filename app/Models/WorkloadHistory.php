<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkloadHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'workload_history';

    protected $fillable = [
        'workload_id',
        'user_id',
        'action',
        'old_data',
        'new_data',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'old_data' => 'array',
            'new_data' => 'array',
            'created_at' => 'datetime',
        ];
    }

    // ========== RELATIONSHIPS ==========

    /**
     * Yuklama
     */
    public function workload(): BelongsTo
    {
        return $this->belongsTo(Workload::class);
    }

    /**
     * Foydalanuvchi
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ========== ACCESSORS ==========

    /**
     * Amal nomi
     */
    public function getActionNameAttribute(): string
    {
        return match($this->action) {
            'created' => 'Yaratildi',
            'updated' => 'O\'zgartirildi',
            'approved' => 'Tasdiqlandi',
            'rejected' => 'Rad etildi',
            'deleted' => 'O\'chirildi',
            'restored' => 'Tiklandi',
            default => 'Noma\'lum'
        };
    }

    /**
     * O'zgarishlarni formatlash
     */
    public function getFormattedChangesAttribute(): array
    {
        $changes = [];

        if ($this->old_data && $this->new_data) {
            foreach ($this->new_data as $key => $newValue) {
                $oldValue = $this->old_data[$key] ?? null;

                if ($oldValue !== $newValue) {
                    $changes[] = [
                        'field' => $this->formatFieldName($key),
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }
        }

        return $changes;
    }

    /**
     * Maydon nomini formatlash
     */
    private function formatFieldName(string $field): string
    {
        return match($field) {
            'semester_1_lecture' => '1-semestr ma\'ruza',
            'semester_1_practical' => '1-semestr amaliy',
            'semester_1_laboratory' => '1-semestr laboratoriya',
            'semester_1_seminar' => '1-semestr seminar',
            'semester_2_lecture' => '2-semestr ma\'ruza',
            'semester_2_practical' => '2-semestr amaliy',
            'semester_2_laboratory' => '2-semestr laboratoriya',
            'semester_2_seminar' => '2-semestr seminar',
            'total_students' => 'Talabalar soni',
            'rating' => 'Reyting',
            'status' => 'Status',
            'notes' => 'Izoh',
            default => ucfirst(str_replace('_', ' ', $field))
        };
    }
}
