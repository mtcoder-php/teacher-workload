<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 50)->unique()->nullable();

            $table->foreignId('direction_id')->constrained()->onDelete('cascade');

            // ✅ YANGI VA MUHIM QATOR
            // Har bir guruh qaysi o'quv yiliga tegishli ekanligini belgilaydi.
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');

            $table->tinyInteger('course');
            $table->enum('education_type', ['kunduzgi', 'sirtqi', 'kechki', 'masofaviy'])->default('kunduzgi');
            $table->enum('education_language', ['uzbek', 'russian'])->default('uzbek');
            $table->integer('student_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('direction_id');
            $table->index('academic_year_id'); // ✅ YANGI INDEX
            $table->index('course');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
