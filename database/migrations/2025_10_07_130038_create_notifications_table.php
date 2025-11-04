<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type', 100);
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Indexlar — nomlari bilan aniq ko‘rsatilgan
            $table->index(['user_id', 'is_read'], 'notifications_user_id_is_read_index');
            $table->index(['user_id', 'created_at'], 'notifications_user_id_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
