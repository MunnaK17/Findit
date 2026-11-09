<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add soft deletes & admin notes to reports
        Schema::table('reports', function (Blueprint $table) {
            $table->softDeletes();
            $table->text('admin_notes')->nullable();
        });

        // Add soft deletes to claims
        Schema::table('claims', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to users
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('admin_notes');
        });

        Schema::table('claims', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
