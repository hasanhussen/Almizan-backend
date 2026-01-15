<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Specialty;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $specialties = [
            ['name' => 'محاسبة'],
            ['name' => 'إدارة أعمال'],
            ['name' => 'اقتصاد'],
        ];

        foreach ($specialties as $specialty) {
            Specialty::create($specialty);
        }
    }

}


