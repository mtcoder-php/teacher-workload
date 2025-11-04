<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('directions', function (Blueprint $table) {
            $table->id();

            // ✅ TO'G'RI - Faqat department_id (faculty_id o'chirildi)
            $table->foreignId('department_id')->constrained()->onDelete('cascade');

            // Basic info
            $table->string('name');
            $table->string('code', 50)->unique();

            // Ta'lim darajasi
            $table->enum('degree_type', ['bakalavr', 'magistratura'])->default('bakalavr');


            $table->integer('duration_years')->default(4);

            // Additional
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Indexes
            $table->index(['department_id', 'is_active']);
            $table->index('degree_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('directions');
    }
};
