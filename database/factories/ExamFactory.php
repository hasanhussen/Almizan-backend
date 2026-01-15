<?php
// database/factories/ExamFactory.php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamFactory extends Factory
{
    protected $model = Exam::class;

public function definition()
{
    $types = ['quiz','midterm','final','assignment','project','participation','oral','practice','makeup'];
    $years = ['1st','2nd','3rd','4th'];
    $status = ['0','1','2'];

    $totalMarks = $this->faker->numberBetween(20, 100);

    return [
        'subject_id' => Subject::factory(),
        'exam_date' => $this->faker->dateTimeBetween('-1 years', '+1 years'),

        'total_marks' => $totalMarks,

        // ✅ 50% من العلامة الكلية
        'success_rate' => (int) round($totalMarks * 0.5),

        'exam_term' => '2025 Term ' . $this->faker->numberBetween(1, 2),
        'exam_type' => $this->faker->randomElement($types),
        'year' => $this->faker->randomElement($years),
        'start_time' => $this->faker->time(),
        'end_time' => $this->faker->time(),
        'actual_duration' => $this->faker->numberBetween(30, 180),
        'exam_status' => $this->faker->randomElement($status),
    ];
}

}
