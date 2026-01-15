<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition()
    {
        $subjects = [
            'Microeconomics',
            'Macroeconomics',
            'Principles of Economics',
            'Econometrics',
            'Public Finance',
            'International Economics',
            'Development Economics',
            'Monetary Economics',
            'Financial Accounting',
            'Managerial Economics',
            'Statistics for Economics',
            'Mathematics for Economists',
        ];

        $years = ['1st', '2nd', '3rd', '4th'];
        $semesters = ['first', 'second'];

        return [
            'name' => $this->faker->randomElement($subjects),
            'year' => $this->faker->randomElement($years),
            'semester' => $this->faker->randomElement($semesters),
            'success_rate' => $this->faker->numberBetween(60, 95),
            'mark' => $this->faker->randomFloat(2, 50, 100),
        ];
    }
}
