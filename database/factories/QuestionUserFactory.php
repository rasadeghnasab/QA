<?php

namespace Database\Factories;

use App\Enums\PracticeStatusEnum;
use App\Models\Question;
use App\Models\QuestionUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = QuestionUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'question_id' => Question::factory()->create(),
            'user_id' => User::factory()->create(),
            'status' => $this->faker->randomElement([PracticeStatusEnum::Correct, PracticeStatusEnum::Incorrect]),
        ];
    }
}
