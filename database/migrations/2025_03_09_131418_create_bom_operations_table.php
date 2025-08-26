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
        Schema::create('bom_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bom_version_id')->constrained();
            $table->foreignId('operation_id')->nullable()->constrained();
            $table->foreignId('calendar_id')->constrained();
            $table->double('cost_ph');
            $table->enum('type', ['process', 'buffer'])->collation('ascii_bin');
            $table->enum('buffer_duration_type', ['minutes', 'calendar_day', 'working_day'])
                ->collation('ascii_bin')
                ->nullable();
            $table->integer('buffer_duration')->nullable();
            $table->integer('x');
            $table->integer('y');
            $table->string('color', 6)->collation('ascii_bin')->nullable();
            $table->text('instructions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bom_operations');
    }
};
