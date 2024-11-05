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
        Schema::create('section_twos', function (Blueprint $table) {
            $table->id();
            $table->string('text_main', 30);
            $table->string('text_secondary', 160);
            $table->string('card_one_image');
            $table->string('card_one_text', 20);
            $table->string('card_one_text_secondary', 90);
            $table->string('card_two_image');
            $table->string('card_two_text', 20);
            $table->string('card_two_text_secondary', 90);
            $table->string('card_third_image');
            $table->string('card_third_text', 20);
            $table->string('card_third_text_secondary', 90);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_twos');
    }
};
