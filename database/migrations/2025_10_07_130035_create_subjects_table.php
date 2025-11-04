<?php
// ==========================================
// 1. SUBJECTS JADVALI - TO'LIQ YANGILANGAN
// ==========================================
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->unique();
            
            // ✅ Kafedra va Yo'nalish
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('direction_id')->nullable()->constrained()->onDelete('cascade');
            
            // ✅ Asosiy ma'lumotlar
            $table->tinyInteger('course_level')->default(1)->comment('1,2,3,4-kurs');
            $table->integer('credit_hours');
            
            // ✅ 1-SEMESTR SOATLARI
            $table->decimal('semester_1_lecture', 10, 2)->default(0);
            $table->decimal('semester_1_practical', 10, 2)->default(0);
            $table->decimal('semester_1_laboratory', 10, 2)->default(0);
            $table->decimal('semester_1_seminar', 10, 2)->default(0);
            $table->decimal('semester_1_practice', 10, 2)->default(0);
            $table->decimal('semester_1_exam', 10, 2)->default(0);
            $table->decimal('semester_1_test', 10, 2)->default(0);
            
            // ✅ 2-SEMESTR SOATLARI
            $table->decimal('semester_2_lecture', 10, 2)->default(0);
            $table->decimal('semester_2_practical', 10, 2)->default(0);
            $table->decimal('semester_2_laboratory', 10, 2)->default(0);
            $table->decimal('semester_2_seminar', 10, 2)->default(0);
            $table->decimal('semester_2_practice', 10, 2)->default(0);
            $table->decimal('semester_2_exam', 10, 2)->default(0);
            $table->decimal('semester_2_test', 10, 2)->default(0);
            
            // ✅ QO'SHIMCHA SOATLAR
            $table->decimal('coursework_hours', 10, 2)->default(0);
            $table->decimal('diploma_hours', 10, 2)->default(0);
            $table->decimal('consultation_hours', 10, 2)->default(0);
            
            // ✅ JAMI SOATLAR (avtomatik)
            $table->decimal('total_hours', 10, 2)->storedAs(
                'semester_1_lecture + semester_1_practical + semester_1_laboratory + semester_1_seminar + 
                 semester_1_practice + semester_1_exam + semester_1_test +
                 semester_2_lecture + semester_2_practical + semester_2_laboratory + semester_2_seminar + 
                 semester_2_practice + semester_2_exam + semester_2_test +
                 coursework_hours + diploma_hours + consultation_hours'
            );
            
            // ✅ FAN TURLARI
            $table->enum('subject_type', ['asosiy', 'yordamchi', 'ixtiyoriy'])->default('asosiy');
            $table->enum('education_form', ['kunduzgi', 'kechki', 'sirtqi'])->nullable();
            
            // ✅ PATOK IMKONIYATI
            $table->boolean('can_be_potok')->default(false)->comment('Patok qilish mumkinmi?');
            $table->integer('min_groups_for_potok')->default(2);
            
            // ✅ NAZORAT TURLARI
            $table->enum('semester_1_control', ['imtihon', 'test', 'baholash'])->nullable();
            $table->enum('semester_2_control', ['imtihon', 'test', 'baholash'])->nullable();
            
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // ✅ INDEXES
            $table->index(['department_id', 'direction_id', 'is_active']);
            $table->index('course_level');
            $table->index('subject_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};