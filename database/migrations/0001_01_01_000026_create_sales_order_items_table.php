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
        Schema::create('sales_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained('sales_orders');
            $table->foreignId('bom_id')->nullable()->constrained('boms');
            $table->double('qty');
            $table->double('price');
            $table->double('total');
            $table->double('discount');
            $table->double('tax');
            $table->string('status')->collation('ascii_bin');
            $table->timestamp('due_at');
            $table->timestamp('delivery_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_order_items');
    }
};
