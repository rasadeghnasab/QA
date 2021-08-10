<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Question::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'body' => $this->faker->text(100),
            'answer' => $this->faker->text(20),
            'user_id' => User::factory()->create(),
            'status' => $this->faker->randomElement(['Not answered', 'Correct', 'Incorrect']),
        ];
    }
}
