<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Sozlama kaliti');
            $table->text('value')->nullable()->comment('Sozlama qiymati');
            $table->string('type')->default('text')->comment('Qiymat turi: text, number, boolean, json');
            $table->string('group')->default('general')->comment('Sozlama guruhi');
            $table->string('label')->comment('Ko\'rinadigan nom');
            $table->text('description')->nullable()->comment('Tushuntirish');
            $table->boolean('is_public')->default(false)->comment('Public API da ko\'rinsin');
            $table->timestamps();
            
            // Indexes
            $table->index('group');
            $table->index('is_public');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};