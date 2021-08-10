<?php

namespace Database\Seeders;

use App\Models\Practice;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;

class PracticeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('id', 1)->first();

        $user->questions->each(function ($question) {
            Practice::factory()->create([
                'user_id' => $question->user_id,
                'question_id' => $question->id,
            ]);
        });

        // Add some not answered questions for user
        Question::factory(10)->create([
            'user_id' => $user->id,
        ]);

        Practice::factory(20)->create();
    }
}
