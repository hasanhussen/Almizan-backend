<?php

namespace Database\Factories;

use App\Models\Specialty;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpecialtyFactory extends Factory
{
    protected $model = Specialty::class;

    public function definition()
    {
        $specialties = [
            'Economics',
            'Business Administration',
            'Accounting',
            'Banking and Finance',
            'International Trade',
            'Public Economics',
            'Financial Economics',
            'Economic Statistics',
        ];

        return [
            'name' => $this->faker->unique()->randomElement($specialties),
            'student_count' => $this->faker->numberBetween(20, 200),
        ];
    }
}
