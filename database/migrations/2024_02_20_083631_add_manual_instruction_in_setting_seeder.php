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
        Schema::table('setting_seeder', function (Blueprint $table) {
            Artisan::call('db:seed', ['--class' => 'ManualInstructionSettingSeeder', '--force' => true]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setting_seeder', function (Blueprint $table) {
            //
        });
    }
};
