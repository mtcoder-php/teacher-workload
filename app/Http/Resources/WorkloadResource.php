<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkloadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            // Bog'liqliklar
            'subject' => $this->whenLoaded('subject', function () {
                return [
                    'id' => $this->subject->id,
                    'name' => $this->subject->name,
                    'code' => $this->subject->code,
                ];
            }),

            'teacher' => $this->whenLoaded('teacher', function () {
                return [
                    'id' => $this->teacher->id,
                    'full_name' => $this->teacher->user->name ?? 'N/A',
                    'position' => $this->teacher->position ?? null,
                ];
            }),

            'direction' => $this->whenLoaded('direction', function () {
                return [
                    'id' => $this->direction->id,
                    'name' => $this->direction->name,
                    'code' => $this->direction->code,
                ];
            }),

            'academic_year' => $this->whenLoaded('academicYear', function () {
                return [
                    'id' => $this->academicYear->id,
                    'name' => $this->academicYear->name,
                ];
            }),

            'department' => $this->whenLoaded('department', function () {
                return [
                    'id' => $this->department->id,
                    'name' => $this->department->name,
                ];
            }),

            // ✅ MUHIM O'ZGARISH: groups massivi
            'groups' => $this->whenLoaded('groups', function () {
                return $this->groups->map(function ($group) {
                    return [
                        'id' => $group->id,
                        'name' => $group->name,
                        'code' => $group->code,
                        'course' => $group->course,
                        'student_count' => $group->student_count,
                        'education_form' => $group->education_form,
                    ];
                });
            }),

            // Potok ma'lumotlari
            'is_potok' => $this->is_potok,
            'potok_code' => $this->potok_code,

            // Soatlar - 1-semestr
            'semester_1_lecture' => (float) $this->semester_1_lecture,
            'semester_1_practical' => (float) $this->semester_1_practical,
            'semester_1_laboratory' => (float) $this->semester_1_laboratory,
            'semester_1_seminar' => (float) $this->semester_1_seminar,
            'semester_1_practice' => (float) $this->semester_1_practice,
            'semester_1_exam' => (float) $this->semester_1_exam,
            'semester_1_test' => (float) $this->semester_1_test,

            // Soatlar - 2-semestr
            'semester_2_lecture' => (float) $this->semester_2_lecture,
            'semester_2_practical' => (float) $this->semester_2_practical,
            'semester_2_laboratory' => (float) $this->semester_2_laboratory,
            'semester_2_seminar' => (float) $this->semester_2_seminar,
            'semester_2_practice' => (float) $this->semester_2_practice,
            'semester_2_exam' => (float) $this->semester_2_exam,
            'semester_2_test' => (float) $this->semester_2_test,

            // Qo'shimcha soatlar
            'coursework_hours' => (float) $this->coursework_hours,
            'diploma_hours' => (float) $this->diploma_hours,
            'consultation_hours' => (float) $this->consultation_hours,

            // Statistika
            'total_students' => (int) $this->total_students,
            'rating' => (float) $this->rating,
            'total_hours' => (float) $this->total_hours, // Accessor'dan

            // Status
            'status' => $this->status,
            'status_label' => $this->status_label,

            // Tasdiqlash
            'approved_by' => $this->whenLoaded('approver', function () {
                return [
                    'id' => $this->approver->id,
                    'name' => $this->approver->name,
                ];
            }),
            'approved_at' => $this->approved_at?->format('d.m.Y H:i'),

            // Izohlar
            'notes' => $this->notes,

            // Timestamps
            'created_at' => $this->created_at->format('d.m.Y H:i'),
            'updated_at' => $this->updated_at->format('d.m.Y H:i'),
        ];
    }
}
