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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('filename')->collation('ascii_bin')->unique();
            $table->string('type', 20)->collation('ascii_bin');
            $table->string('hash', 64)->collation('ascii_bin');
            $table->string('extension', 5);
            $table->string('mime_type', 50);
            $table->text('comments')->nullable();
            $table->unsignedInteger('size');
            $table->unsignedSmallInteger('width');
            $table->unsignedSmallInteger('height');
            $table->boolean('is_default')->default(false);
            $table->morphs('imageable');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
