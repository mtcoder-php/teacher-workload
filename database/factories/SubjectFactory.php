<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Direction;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $semester1Lecture = $this->faker->numberBetween(16, 48);
        $semester1Practical = $this->faker->numberBetween(8, 32);
        $semester1Laboratory = $this->faker->numberBetween(0, 24);
        
        $semester2Lecture = $this->faker->numberBetween(0, 48);
        $semester2Practical = $this->faker->numberBetween(0, 32);
        $semester2Laboratory = $this->faker->numberBetween(0, 24);

        return [
            'name' => $this->faker->randomElement([
                'Dasturlash asoslari',
                'Ma\'lumotlar bazasi',
                'Kompyuter tarmoqlari',
                'Web texnologiyalar',
                'Mobil dasturlash',
                'Sun\'iy intellekt',
                'Ma\'lumotlar tuzilmasi',
                'Algoritmlar nazariyasi',
                'Diskret matematika',
                'Oliy matematika',
                'Chiziqli algebra',
                'Ehtimollik nazariyasi',
            ]) . ' ' . $this->faker->numberBetween(1, 4),
            'code' => strtoupper($this->faker->lexify('???')) . $this->faker->numberBetween(100, 499),
            'department_id' => Department::factory(),
            'direction_id' => $this->faker->boolean(70) ? Direction::factory() : null,
            'course_level' => $this->faker->numberBetween(1, 4),
            'credit_hours' => $this->faker->numberBetween(2, 6),
            
            // 1-semestr
            'semester_1_lecture' => $semester1Lecture,
            'semester_1_practical' => $semester1Practical,
            'semester_1_laboratory' => $semester1Laboratory,
            'semester_1_seminar' => $this->faker->numberBetween(0, 16),
            'semester_1_practice' => $this->faker->numberBetween(0, 8),
            'semester_1_exam' => $this->faker->boolean(80) ? 4 : 0,
            'semester_1_test' => $this->faker->boolean(20) ? 2 : 0,
            
            // 2-semestr
            'semester_2_lecture' => $semester2Lecture,
            'semester_2_practical' => $semester2Practical,
            'semester_2_laboratory' => $semester2Laboratory,
            'semester_2_seminar' => $this->faker->numberBetween(0, 16),
            'semester_2_practice' => $this->faker->numberBetween(0, 8),
            'semester_2_exam' => $this->faker->boolean(70) ? 4 : 0,
            'semester_2_test' => $this->faker->boolean(30) ? 2 : 0,
            
            // Qo'shimcha
            'coursework_hours' => $this->faker->boolean(30) ? $this->faker->numberBetween(10, 30) : 0,
            'diploma_hours' => $this->faker->boolean(10) ? $this->faker->numberBetween(20, 50) : 0,
            'consultation_hours' => $this->faker->numberBetween(0, 10),
            
            'subject_type' => $this->faker->randomElement(['asosiy', 'yordamchi', 'ixtiyoriy']),
            'education_form' => $this->faker->randomElement(['kunduzgi', 'kechki', 'sirtqi']),
            
            'can_be_potok' => $this->faker->boolean(40),
            'min_groups_for_potok' => $this->faker->numberBetween(2, 5),
            
            'semester_1_control' => $semester1Lecture > 0 || $semester1Practical > 0 
                ? $this->faker->randomElement(['imtihon', 'test', 'baholash']) 
                : null,
            'semester_2_control' => $semester2Lecture > 0 || $semester2Practical > 0 
                ? $this->faker->randomElement(['imtihon', 'test', 'baholash']) 
                : null,
            
            'description' => $this->faker->boolean(50) ? $this->faker->paragraph() : null,
            'is_active' => $this->faker->boolean(90),
        ];
    }

    /**
     * 1-semestrda o'qiladigan fan
     */
    public function semester1Only(): static
    {
        return $this->state(fn (array $attributes) => [
            'semester_1_lecture' => $this->faker->numberBetween(24, 48),
            'semester_1_practical' => $this->faker->numberBetween(16, 32),
            'semester_1_exam' => 4,
            'semester_1_control' => 'imtihon',
            
            'semester_2_lecture' => 0,
            'semester_2_practical' => 0,
            'semester_2_laboratory' => 0,
            'semester_2_seminar' => 0,
            'semester_2_practice' => 0,
            'semester_2_exam' => 0,
            'semester_2_test' => 0,
            'semester_2_control' => null,
        ]);
    }

    /**
     * 2-semestrda o'qiladigan fan
     */
    public function semester2Only(): static
    {
        return $this->state(fn (array $attributes) => [
            'semester_1_lecture' => 0,
            'semester_1_practical' => 0,
            'semester_1_laboratory' => 0,
            'semester_1_seminar' => 0,
            'semester_1_practice' => 0,
            'semester_1_exam' => 0,
            'semester_1_test' => 0,
            'semester_1_control' => null,
            
            'semester_2_lecture' => $this->faker->numberBetween(24, 48),
            'semester_2_practical' => $this->faker->numberBetween(16, 32),
            'semester_2_exam' => 4,
            'semester_2_control' => 'imtihon',
        ]);
    }

    /**
     * Ikki semestrda ham o'qiladigan fan
     */
    public function bothSemesters(): static
    {
        return $this->state(fn (array $attributes) => [
            'semester_1_lecture' => $this->faker->numberBetween(20, 36),
            'semester_1_practical' => $this->faker->numberBetween(12, 24),
            'semester_1_test' => 2,
            'semester_1_control' => 'test',
            
            'semester_2_lecture' => $this->faker->numberBetween(20, 36),
            'semester_2_practical' => $this->faker->numberBetween(12, 24),
            'semester_2_exam' => 4,
            'semester_2_control' => 'imtihon',
        ]);
    }

    /**
     * Asosiy fan
     */
    public function mainSubject(): static
    {
        return $this->state(fn (array $attributes) => [
            'subject_type' => 'asosiy',
            'credit_hours' => $this->faker->numberBetween(4, 6),
        ]);
    }

    /**
     * Ixtiyoriy fan
     */
    public function electiveSubject(): static
    {
        return $this->state(fn (array $attributes) => [
            'subject_type' => 'ixtiyoriy',
            'credit_hours' => $this->faker->numberBetween(2, 4),
        ]);
    }

    /**
     * Patok qilinadigan fan
     */
    public function potokSubject(): static
    {
        return $this->state(fn (array $attributes) => [
            'can_be_potok' => true,
            'min_groups_for_potok' => $this->faker->numberBetween(2, 4),
            'semester_1_lecture' => $this->faker->numberBetween(32, 48),
        ]);
    }

    /**
     * Kurs ishi bor fan
     */
    public function withCoursework(): static
    {
        return $this->state(fn (array $attributes) => [
            'coursework_hours' => $this->faker->numberBetween(15, 30),
            'course_level' => $this->faker->numberBetween(2, 3),
        ]);
    }

    /**
     * Diplom ishi bor fan
     */
    public function withDiploma(): static
    {
        return $this->state(fn (array $attributes) => [
            'diploma_hours' => $this->faker->numberBetween(30, 60),
            'course_level' => 4,
        ]);
    }
}