<?php

namespace Database\Seeders;

use App\Models\SuperAdminSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultLanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultLanguage = SuperAdminSetting::where('key','default_language')->exists();
        if(!$defaultLanguage){
            SuperAdminSetting::create(['key' => 'default_language', 'value' => 'en']);
        }
    }
}
