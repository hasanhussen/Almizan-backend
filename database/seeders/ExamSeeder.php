<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\Subject;


class ExamSeeder extends Seeder
{
    public function run()
    {
        $subject = Subject::first();

        Exam::create([
            'subject_id' => $subject->id,
            'exam_date' => now()->addDays(5),
            'start_time' => now()->addDays(5)->setTime(10, 0),
            'end_time' => now()->addDays(5)->setTime(11, 0),
            'total_marks' => 100,
            'exam_term' => 'نصفي',
            'exam_type' => 'written',
            'year' => '1st',
        ]);
    }
}

