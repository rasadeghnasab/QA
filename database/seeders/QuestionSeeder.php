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

        Question::factory(10)->sequence(fn($sequence) => [
            'user_id' => $user->id,
            'body' => "q{$sequence->index}",
            'answer' => "a{$sequence->index}",
        ])->create();

        Question::factory(20)->sequence(fn($sequence) => [
            'body' => "q1{$sequence->index}",
            'answer' => "a1{$sequence->index}",
        ])->create();
    }
}
