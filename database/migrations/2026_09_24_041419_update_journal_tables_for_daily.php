<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan tabel daily_moods ada
        if (!Schema::hasTable('daily_moods')) {
            Schema::create('daily_moods', function (Blueprint $table) {
                $table->id();
                $table->string('mood_emoji');
                $table->string('mood_label');
                $table->date('date');
                $table->timestamps();
            });
        }

        // Tambah kolom date ke tabel todos jika belum ada
        if (Schema::hasTable('todos') && !Schema::hasColumn('todos', 'date')) {
            Schema::table('todos', function (Blueprint $table) {
                $table->date('date')->nullable()->after('is_completed');
            });
        }
    }

    public function down(): void
    {
        //
    }
};