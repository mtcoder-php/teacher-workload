<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false); // is_active ni shu yerga qo‘shamiz
            $table->boolean('is_current')->default(false);
            $table->timestamps();
        });

        // Eng so‘nggi o‘quv yilini active qilish
        $latestYear = DB::table('academic_years')
            ->orderBy('start_date', 'desc')
            ->first();

        if ($latestYear) {
            DB::table('academic_years')
                ->where('id', $latestYear->id)
                ->update(['is_active' => true]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
