<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_stats', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            
            // Umumiy hisobotlar
            $table->integer('total_workloads')->default(0);
            $table->integer('total_groups')->default(0);
            $table->integer('total_students')->default(0);
            
            // Soatlar bo'yicha
            $table->decimal('total_lecture_hours', 10, 2)->default(0);
            $table->decimal('total_practical_hours', 10, 2)->default(0);
            $table->decimal('total_laboratory_hours', 10, 2)->default(0);
            $table->decimal('total_hours', 10, 2)->default(0);
            
            // Reyting
            $table->decimal('total_rating', 10, 2)->default(0);
            $table->decimal('average_rating', 10, 2)->default(0);
            
            // Yangilanish vaqti
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();
            
            // UNIQUE
            $table->unique(['teacher_id', 'academic_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_stats');
    }
};
