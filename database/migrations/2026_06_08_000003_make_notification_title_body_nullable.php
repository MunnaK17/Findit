<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('notifications') || ! in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement('ALTER TABLE notifications MODIFY title VARCHAR(255) NULL');
        DB::statement('ALTER TABLE notifications MODIFY body TEXT NULL');
    }

    public function down(): void
    {
        if (! Schema::hasTable('notifications') || ! in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement("UPDATE notifications SET title = JSON_UNQUOTE(JSON_EXTRACT(data, '$.title')) WHERE title IS NULL");
        DB::statement("UPDATE notifications SET body = JSON_UNQUOTE(JSON_EXTRACT(data, '$.body')) WHERE body IS NULL");
        DB::statement("UPDATE notifications SET title = 'Notifikasi' WHERE title IS NULL");
        DB::statement("UPDATE notifications SET body = '' WHERE body IS NULL");

        DB::statement('ALTER TABLE notifications MODIFY title VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE notifications MODIFY body TEXT NOT NULL');
    }
};
