<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rating_history', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('workload_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            
            // Reyting ma'lumotlari
            $table->decimal('total_hours', 10, 2);
            $table->integer('total_students');
            $table->decimal('rating', 10, 2);
            
            // Qaysi oyga tegishli
            $table->tinyInteger('month')->comment('1-12');
            $table->integer('year');
            
            $table->timestamp('calculated_at')->useCurrent();
            $table->timestamps();
            
            // INDEXES
            $table->index(['teacher_id', 'academic_year_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rating_history');
    }
};
