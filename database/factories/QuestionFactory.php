<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition()
    {
        $questions = [
            'Define the concept of opportunity cost.',
            'Explain the difference between microeconomics and macroeconomics.',
            'What is GDP and how is it calculated?',
            'Explain the law of demand.',
            'What are the main objectives of fiscal policy?',
            'Define inflation and its causes.',
            'What is elasticity of demand?',
            'Explain comparative advantage with an example.',
        ];

        return [
            'exam_id' => Exam::factory(),
            'question_text' => $this->faker->randomElement($questions),
            'mark' => $this->faker->randomFloat(1, 2, 10),
        ];
    }
}
