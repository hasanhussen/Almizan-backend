<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    public function run()
    {
        Subject::create([
            'name' => 'محاسبة مالية',
            'year' => '1st',
            'semester' => 'first',
            'success_rate' => 60,
        ]);

        Subject::create([
            'name' => 'إدارة موارد بشرية',
            'year' => '1st',
            'semester' => 'second',
            'success_rate' => 55,
        ]);
    }
}

