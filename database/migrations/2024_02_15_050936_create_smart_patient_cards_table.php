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
        Schema::create('smart_patient_cards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('template_name');
            $table->string('header_color');
            $table->boolean('show_email');
            $table->boolean('show_phone');
            $table->boolean('show_dob');
            $table->boolean('show_blood_group');
            $table->boolean('show_address');
            $table->boolean('show_patient_unique_id');
            $table->string('tenant_id')->nullable();
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
        Schema::dropIfExists('smart_patient_cards');
    }
};
