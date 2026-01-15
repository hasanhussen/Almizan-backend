<?php
// database/factories/UserAnswerFactory.php

namespace Database\Factories;

use App\Models\UserAnswer;
use App\Models\User;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserAnswerFactory extends Factory
{
    protected $model = UserAnswer::class;

    public function definition()
    {
        $question = Question::inRandomOrder()->first();
        $answers = $question ? $question->answers : [];
        $answer = $answers->count() ? $answers->random() : null;

        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'exam_id' => $question ? $question->exam_id : Exam::factory(),
            'question_id' => $question ? $question->id : Question::factory(),
            'answer_id' => $answer ? $answer->id : Answer::factory(),
        ];
    }
}
