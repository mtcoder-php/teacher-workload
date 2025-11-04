<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workloads', function (Blueprint $table) {
            $table->id();

            // ==========================================
            // ASOSIY BOG'LANISHLAR
            // ==========================================
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('direction_id')->constrained('directions')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');

            // ==========================================
            // POTOK TIZIMI
            // ==========================================
            $table->boolean('is_potok')->default(false)->comment('Agar bir nechta guruhga tegishli bo\'lsa true');
            $table->string('potok_code', 50)->nullable()->comment('Potoklar uchun unikal kod');

            // Yuklama turi: lecture_only, practice_only, full, combined
            $table->enum('workload_type', ['lecture_only', 'practice_only', 'full', 'combined'])
                ->default('full')
                ->comment('Yuklama turi');

            // Potok qoldig'i belgisi
            $table->boolean('is_potok_remainder')->default(false)
                ->comment('Agar bu potok ma\'ruzasining amaliy qismi bo\'lsa');

            // Parent potok (amaliy guruh uchun ma'ruza potokiga reference)
            $table->foreignId('parent_potok_id')->nullable()
                ->constrained('workloads')
                ->onDelete('cascade')
                ->comment('Potok ma\'ruzasiga havola');

            // ==========================================
            // 1-SEMESTR SOATLARI
            // ==========================================
            $table->decimal('semester_1_lecture', 10, 2)->default(0)->comment('1-sem: Ma\'ruza');
            $table->decimal('semester_1_practical', 10, 2)->default(0)->comment('1-sem: Amaliy');
            $table->decimal('semester_1_seminar', 10, 2)->default(0)->comment('1-sem: Seminar');
            $table->decimal('semester_1_laboratory', 10, 2)->default(0)->comment('1-sem: Laboratoriya');
            $table->decimal('semester_1_practice', 10, 2)->default(0)->comment('1-sem: Amaliyot');
            $table->decimal('semester_1_exam', 10, 2)->default(0)->comment('1-sem: Imtihon');
            $table->decimal('semester_1_test', 10, 2)->default(0)->comment('1-sem: Test');

            // ==========================================
            // 2-SEMESTR SOATLARI
            // ==========================================
            $table->decimal('semester_2_lecture', 10, 2)->default(0)->comment('2-sem: Ma\'ruza');
            $table->decimal('semester_2_practical', 10, 2)->default(0)->comment('2-sem: Amaliy');
            $table->decimal('semester_2_seminar', 10, 2)->default(0)->comment('2-sem: Seminar');
            $table->decimal('semester_2_laboratory', 10, 2)->default(0)->comment('2-sem: Laboratoriya');
            $table->decimal('semester_2_practice', 10, 2)->default(0)->comment('2-sem: Amaliyot');
            $table->decimal('semester_2_exam', 10, 2)->default(0)->comment('2-sem: Imtihon');
            $table->decimal('semester_2_test', 10, 2)->default(0)->comment('2-sem: Test');

            // ==========================================
            // UMUMIY SOATLAR (semestrlardan tashqari)
            // ==========================================
            $table->decimal('coursework_hours', 10, 2)->default(0)->comment('Kurs ishi');
            $table->decimal('diploma_hours', 10, 2)->default(0)->comment('Diplom ishi');
            $table->decimal('consultation_hours', 10, 2)->default(0)->comment('Konsultatsiya');

            // ==========================================
            // STATISTIKA VA STATUS
            // ==========================================
            $table->integer('total_students')->default(0)->comment('Jami talabalar soni');
            $table->decimal('rating', 10, 2)->default(0)->comment('Reyting (talabalar / 2)');
            $table->decimal('total_hours', 10, 2)->default(0)->comment('Jami soatlar');

            // Status
            $table->enum('status', ['draft', 'pending', 'confirmed', 'completed'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // ==========================================
            // INDEXES
            // ==========================================
            $table->index(['teacher_id', 'academic_year_id']);
            $table->index(['department_id', 'academic_year_id']);
            $table->index(['subject_id', 'academic_year_id', 'is_potok']);
            $table->index('potok_code');
            $table->index('parent_potok_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workloads');
    }
};
