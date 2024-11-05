<?php

namespace Database\Seeders;

use App\Models\Bed;
use App\Models\BedAssign;
use App\Models\IpdPatientDepartment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultIpdBedAssignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ipdBedIds = IpdPatientDepartment::where('is_discharge', 0)->pluck('bed_id')->toArray();
        $assignedBedIds = BedAssign::where('status', 0)->pluck('bed_id')->toArray();
        $BedIds = array_merge($ipdBedIds, $assignedBedIds);
        $allBedIds = array_unique($BedIds);

        if(isset($allBedIds)){
            $bedsToUpdate = Bed::whereNotIn('id', $allBedIds)->get();
            if(isset($bedsToUpdate)){
                foreach($bedsToUpdate as $bed) {
                    $bed->update(['is_available' => 1]);
                }
            }
        }
    }
}
