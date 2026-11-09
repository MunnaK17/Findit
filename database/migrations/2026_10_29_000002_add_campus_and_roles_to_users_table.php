<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('mahasiswa', 'staff', 'admin') DEFAULT 'mahasiswa'");

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('id_campus')->nullable()->after('role')->constrained('campuses')->nullOnDelete();
            $table->string('nip', 30)->nullable()->after('nim');
            $table->string('prodi_unit', 100)->nullable()->after('id_campus');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_campus']);
            $table->dropColumn(['id_campus', 'nip', 'prodi_unit']);
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('mahasiswa', 'admin') DEFAULT 'mahasiswa'");
    }
};
