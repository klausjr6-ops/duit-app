<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Skip / hapus file ini kalau tabel `budgets` sudah ada di project kamu.
     * Sesuaikan foreign key 'categories' kalau nama tabel kategori kamu beda.
     */
    public function up(): void
    {
        if (Schema::hasTable('budgets')) {
            return;
        }

        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('monthly_limit');
            $table->timestamps();

            $table->unique(['user_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
