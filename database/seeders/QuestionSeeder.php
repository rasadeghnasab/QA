<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email', 'test@test.com')->first();

        for ($i = 0; $i < 10; $i++) {
            Question::factory(25)->create([
                'user_id' => $user->id,
                'body' => "q{$i}",
                'answer' => "a{$i}",
            ]);
        }

        Question::factory(30)->create();
    }
}
