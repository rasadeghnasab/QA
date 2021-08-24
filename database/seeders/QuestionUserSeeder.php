<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\QuestionUser;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;

class QuestionUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::where('email', '!=', 'test@test.com')->get();
        $questions = Question::all()->shuffle();

        QuestionUser::factory(10)->sequence(
            fn($sequence) => [
                'user_id' => $users->random()->id,
                'question_id' => $questions[$sequence->index]
            ]
        )->create();

        $user = User::where('email', 'test@test.com')->first();
        $randomQuestions = 
        QuestionUser::factory(10)->sequence(
            fn($sequence) => [
                'question_id' => $questions[$sequence->index],
                'user_id' => $user->id,
            ]
        )->create();
    }
}
