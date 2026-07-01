<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration ini HANYA jika kamu sudah punya tabel `schedules`.
     * Kalau nama tabel jadwal kamu beda, ganti 'schedules' di bawah.
     */
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('schedules', 'notified')) {
                $table->boolean('notified')->default(false)->after('scheduled_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('notified');
        });
    }
};
