<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Bu funksiya bitta Group obyekti uchun qanday ma'lumotlarni
        // JSON ga o'girish kerakligini belgilaydi.
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'course' => $this->course,
            'student_count' => $this->student_count,
            'education_type' => $this->education_type,
            'education_language' => $this->education_language,
            'is_active' => $this->is_active,

            // Bog'liq ma'lumotlar (agar kerak bo'lsa)
            // 'direction' => new DirectionResource($this->whenLoaded('direction')),
        ];
    }
}
