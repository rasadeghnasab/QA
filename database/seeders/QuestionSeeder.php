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
        Question::truncate();

        $user = User::where('email', 'test@test.com')->first();

        Question::factory(5)->sequence(fn($sequence) => [
            'user_id' => $user->id,
            'body' => "q{$sequence->index}",
            'answer' => "a{$sequence->index}",
        ])->create();

        Question::factory(5)->sequence(fn($sequence) => [
            'body' => "qf{$sequence->index}",
            'answer' => "af{$sequence->index}",
        ])->create();
    }
}
