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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->collation('ascii_bin')->unique();
            $table->string('username')->unique();
            $table->string('first_name', 85);
            $table->string('middle_names', 85)->nullable();
            $table->string('last_name', 85)->nullable();
            $table->string('full_name')->nullable();
            $table->timestamp('dob')->nullable();
            $table->string('company_number')->nullable();
            $table->string('company_mobile_number')->nullable();
            $table->string('company_ext')->nullable();
            $table->string('personal_number')->nullable();
            $table->string('personal_mobile_number')->nullable();
            $table->string('employee_id')->nullable();
            $table->string('email')->nullable();
            $table->string('personal_email')->nullable();
            $table->string('password');
            $table->string('emergency_name')->nullable();
            $table->string('emergency_number')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->rememberToken();
            $table->boolean('active')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};
