<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('time_capsules', function (Blueprint $table) {
            $table->id();
            $table->string('sender')->nullable();
            $table->text('content');
            $table->dateTime('unlock_at');
            $table->string('type')->default('time_capsule'); // 'letter' or 'time_capsule'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_capsules');
    }
};
