<?php
// database/factories/ResultFactory.php

namespace Database\Factories;

use App\Models\Result;
use App\Models\User;
use App\Models\Subject;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResultFactory extends Factory
{
    protected $model = Result::class;

    public function definition()
    {
        $user = User::inRandomOrder()->first() ?? User::factory();
        $subject = Subject::inRandomOrder()->first() ?? Subject::factory();
        $exam = Exam::where('subject_id', $subject->id)->inRandomOrder()->first() ?? Exam::factory();

        $exam_degree = $this->faker->numberBetween(0, $exam->total_marks ?? 100);
        $is_passed = $exam_degree >= (($exam->success_rate ?? 50)/100 * ($exam->total_marks ?? 50));

        return [
            'user_id' => $user->id,
            'subject_id' => $subject->id,
            'exam_id' => $exam->id,
            'exam_degree' => $exam_degree,
            'is_passed' => $is_passed,
        ];
    }
}
