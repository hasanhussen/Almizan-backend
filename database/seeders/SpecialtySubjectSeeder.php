<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Specialty;
use App\Models\Subject;

class SpecialtySubjectSeeder extends Seeder
{
    public function run()
    {
        $specialty = Specialty::first();
        $subjects = Subject::pluck('id');

        $specialty->subjects()->attach($subjects);
    }
}
