<?php

namespace App\StateMachines\States;

use App\Models\Question;
use App\StateMachines\Interfaces\StateInterface;
use Illuminate\Console\Command;

class AddQuestion implements StateInterface
{
    public function handle(Command $command): string
    {
        $body = $command->ask('Enter your question body please');

        $answer = $command->ask('Enter the answer for your question');

        $command->user()->questions()->save(
            new Question([
                'body' => $body,
                'answer' => $answer,
            ])
        );

        $command->info('The question has been added successfully.');

        return $command->confirm('Add another one?', true) ? 'Create a question' : 'MainMenu';
    }

    public function name(): string
    {
        return self::class;
    }
}
