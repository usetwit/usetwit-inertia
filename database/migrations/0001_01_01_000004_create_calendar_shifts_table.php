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
        Schema::create('calendar_shifts', function (Blueprint $table) {
            $table->unsignedBigInteger('calendar_id');
            $table->date('shift_date');
            $table->unsignedSmallInteger('total_duration');
            $table->boolean('nwd');
            $table->time('shift1_start');
            $table->time('shift1_end');
            $table->unsignedSmallInteger('shift1_duration');
            $table->time('shift2_start')->nullable();
            $table->time('shift2_end')->nullable();
            $table->unsignedSmallInteger('shift2_duration');
            $table->time('shift3_start')->nullable();
            $table->time('shift3_end')->nullable();
            $table->unsignedSmallInteger('shift3_duration');
            $table->time('shift4_start')->nullable();
            $table->time('shift4_end')->nullable();
            $table->unsignedSmallInteger('shift4_duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_shifts');
    }
};
