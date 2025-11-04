<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workload_groups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('workload_id')->constrained()->onDelete('cascade');
            $table->foreignId('group_id')->constrained()->onDelete('cascade');

            // Bu jadvalda boshqa hech narsa kerak emas.
            // Faqat bog'liqlikni saqlaymiz.

            $table->timestamps();

            // Takrorlanishni oldini olish uchun unique index
            $table->unique(['workload_id', 'group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workload_groups');
    }
};
