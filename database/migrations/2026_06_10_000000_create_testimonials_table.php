<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_claim')->nullable()->constrained('claims')->onDelete('cascade');
            $table->foreignId('id_report')->constrained('reports')->onDelete('cascade');
            $table->text('isi_testimoni');
            $table->tinyInteger('rating')->unsigned(); // 1-5
            $table->timestamps();

            // Prevent duplicate testimonials for same claim
            $table->unique(['id_user', 'id_claim']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};