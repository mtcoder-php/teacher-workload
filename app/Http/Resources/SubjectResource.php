<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            
            // Asosiy ma'lumotlar
            'department_id' => $this->department_id,
            'direction_id' => $this->direction_id,
            'course_level' => $this->course_level,
            'credit_hours' => $this->credit_hours,
            
            // 1-semestr soatlari
            'semester_1' => [
                'lecture' => (float) $this->semester_1_lecture,
                'practical' => (float) $this->semester_1_practical,
                'laboratory' => (float) $this->semester_1_laboratory,
                'seminar' => (float) $this->semester_1_seminar,
                'practice' => (float) $this->semester_1_practice,
                'exam' => (float) $this->semester_1_exam,
                'test' => (float) $this->semester_1_test,
                'total' => (float) $this->semester_1_total_hours,
                'auditory' => (float) $this->semester_1_auditory_hours,
                'control' => $this->semester_1_control,
            ],
            
            // 2-semestr soatlari
            'semester_2' => [
                'lecture' => (float) $this->semester_2_lecture,
                'practical' => (float) $this->semester_2_practical,
                'laboratory' => (float) $this->semester_2_laboratory,
                'seminar' => (float) $this->semester_2_seminar,
                'practice' => (float) $this->semester_2_practice,
                'exam' => (float) $this->semester_2_exam,
                'test' => (float) $this->semester_2_test,
                'total' => (float) $this->semester_2_total_hours,
                'auditory' => (float) $this->semester_2_auditory_hours,
                'control' => $this->semester_2_control,
            ],
            
            // Qo'shimcha soatlar
            'additional_hours' => [
                'coursework' => (float) $this->coursework_hours,
                'diploma' => (float) $this->diploma_hours,
                'consultation' => (float) $this->consultation_hours,
            ],
            
            // Jami soatlar
            'total_hours' => (float) $this->total_hours,
            'total_auditory_hours' => (float) $this->total_auditory_hours,
            
            // Fan turlari
            'subject_type' => $this->subject_type,
            'subject_type_label' => match($this->subject_type) {
                'asosiy' => 'Asosiy',
                'yordamchi' => 'Yordamchi',
                'ixtiyoriy' => 'Ixtiyoriy',
                default => $this->subject_type,
            },
            'education_form' => $this->education_form,
            'education_form_label' => match($this->education_form) {
                'kunduzgi' => 'Kunduzgi',
                'kechki' => 'Kechki',
                'sirtqi' => 'Sirtqi',
                default => $this->education_form,
            },
            
            // Patok
            'can_be_potok' => (bool) $this->can_be_potok,
            'min_groups_for_potok' => $this->min_groups_for_potok,
            
            'description' => $this->description,
            'is_active' => (bool) $this->is_active,
            
            // Relationships
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'direction' => new DirectionResource($this->whenLoaded('direction')),
            'workloads' => WorkloadResource::collection($this->whenLoaded('workloads')),
            'workloads_count' => $this->when(
                $this->relationLoaded('workloads'),
                fn() => $this->workloads->count()
            ),
            
            // Timestamps
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
        ];
    }
}