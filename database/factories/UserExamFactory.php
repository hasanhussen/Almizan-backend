<?php
// database/factories/UserExamFactory.php

namespace Database\Factories;

use App\Models\UserExam;
use App\Models\User;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserExamFactory extends Factory
{
    protected $model = UserExam::class;

    public function definition()
    {
        $status=['0','1','2'];
        return [
            'user_id' => User::factory(),
            'exam_id' => Exam::factory(),
            //'code' => strtoupper($this->faker->bothify('???###')),
            'is_submitted' => $this->faker->randomElement($status),
        ];
    }
}
