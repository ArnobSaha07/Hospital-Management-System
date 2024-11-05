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
        Schema::table('blood_issues', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->nullable(false)->default(0.00)->change();
        });
    }
};
