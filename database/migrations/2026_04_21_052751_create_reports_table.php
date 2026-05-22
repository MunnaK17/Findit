<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_category')->constrained('categories')->onDelete('cascade');
            $table->enum('jenis_laporan', ['hilang', 'temuan']);
            $table->string('nama_barang');
            $table->text('deskripsi');
            $table->string('lokasi');
            $table->date('tanggal_kejadian');
            $table->string('foto_barang')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};