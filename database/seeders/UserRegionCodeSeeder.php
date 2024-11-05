<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Propaganistas\LaravelPhone\PhoneNumber;

class UserRegionCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        foreach($users as $user){
            try {
                $phoneno = isset($user->phone) ? $user->phone : '';
                $phoneNumber = (new \Propaganistas\LaravelPhone\PhoneNumber($phoneno))->toLibPhoneObject();
                $phone = $phoneNumber->getNationalNumber();
                $country_code = $phoneNumber->getCountryCode();

                $user->update([
                    'phone' => $phone,
                    'region_code' => isset($country_code) ? '+'.$country_code : ''
                ]);
            } catch (\Propaganistas\LaravelPhone\Exceptions\NumberParseException $e) {
                continue;
            }
        }
    }
}
