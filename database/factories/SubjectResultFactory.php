<?php
// database/factories/SubjectResultFactory.php

namespace Database\Factories;

use App\Models\SubjectResult;
use App\Models\User;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectResultFactory extends Factory
{
    protected $model = SubjectResult::class;

    public function definition()
    {
        $user = User::inRandomOrder()->first() ?? User::factory();
        $subject = Subject::inRandomOrder()->first() ?? Subject::factory();

        return [
            'user_id' => $user->id,
            'subject_id' => $subject->id,
            'total_degree' => $this->faker->numberBetween(0, 100),
            'is_passed' => $this->faker->boolean(80),
        ];
    }
}
