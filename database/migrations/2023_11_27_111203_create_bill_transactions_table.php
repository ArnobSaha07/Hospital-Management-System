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
        Schema::create('bill_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id');
            $table->integer('payment_type')->comment('1 = Stripe, 2 = Manual');
            $table->float('amount');
            $table->unsignedInteger('bill_id');
            $table->string('status');
            $table->text('meta')->nullable();
            $table->string('tenant_id')->nullable();
            $table->integer('is_manual_payment')->nullable()->default(0);
            $table->timestamps();

            $table->foreign('bill_id')
                ->references('id')
                ->on('bills')
                ->onUpdate('cascade')
                ->onDelete('cascade');

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
        Schema::dropIfExists('bill_transactions');
    }
};
