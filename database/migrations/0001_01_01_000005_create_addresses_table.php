<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('address_line_3')->nullable();
            $table->string('postcode', 12)->nullable();
            $table->string('country_code', 2)->collation('utf8mb4_bin')->nullable();
            $table->string('country_name')->nullable();
            $table->geography('coords')->nullable();
            $table->boolean('active')->default(1);
            $table->boolean('is_default')->default(false);
            $table->morphs('addressable');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
