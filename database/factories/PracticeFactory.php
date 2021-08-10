<?php

namespace Database\Factories;

use App\Models\Practice;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PracticeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Practice::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory()->create(),
            'question_id' => Question::factory()->create(),
            'status' => $this->faker->randomElement(['Not answered', 'Correct', 'Incorrect']),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function status(string $status)
    {
        return $this->state(function (array $attributes) use ($status) {
            return [
                'status' => $status,
            ];
        });
    }
}
