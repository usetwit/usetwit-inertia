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
        Schema::create('bom_operation_successors', function (Blueprint $table) {
            $table->foreignId('bom_operation_id')->constrained('bom_operations')->onDelete('cascade');
            $table->foreignId('successor_id')->constrained('bom_operations')->onDelete('cascade');

            $table->index(['bom_operation_id', 'successor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bom_operation_successors');
    }
};
