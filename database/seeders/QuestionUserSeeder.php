<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\QuestionUser;
use App\Models\User;
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
        QuestionUser::truncate();

        $users = User::where('email', '!=', 'test@test.com')->get();
        $questions = Question::all()->shuffle();

        QuestionUser::factory(5)->sequence(
            fn($sequence) => [
                'user_id' => $users->random()->id,
                'question_id' => $questions[$sequence->index]
            ]
        )->create();

        $user = User::where('email', 'test@test.com')->first();
        QuestionUser::factory(5)->sequence(
            fn($sequence) => [
                'question_id' => $questions[$sequence->index],
                'user_id' => $user->id,
            ]
        )->create();
    }
}
