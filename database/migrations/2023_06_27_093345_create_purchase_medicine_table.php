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
        Schema::create('purchase_medicines', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_no');
            $table->float('tax');
            $table->float('total');
            $table->float('net_amount');
            $table->integer('payment_type');
            $table->float('discount');
            $table->string('note')->nullable();
            $table->string('payment_note')->nullable();
            $table->string('tenant_id')->nullable;
            $table->timestamps();

            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_medicines');
    }
};
