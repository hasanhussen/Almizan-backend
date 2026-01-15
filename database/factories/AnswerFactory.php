<?php
// database/factories/AnswerFactory.php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnswerFactory extends Factory
{
    protected $model = Answer::class;

    public function definition()
    {
        // AnswerFactory فقط كـ fallback
        return [
            'question_id' => Question::factory(),
            'answer_text' => 'Temporary answer',
            'is_correct' => false,
        ];

    }
}
