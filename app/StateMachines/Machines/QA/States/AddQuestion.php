<?php

namespace App\StateMachines\Machines\QA\States;

use App\Models\Question;
use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Machines\QA\QAStatesEnum;
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

        return $command->confirm('Add another one?', true) ? QAStatesEnum::AddQuestion : QAStatesEnum::MainMenu;
    }

    public function name(): string
    {
        return self::class;
    }

    public function action(): string
    {
        return QAStatesEnum::AddQuestion;
    }
}
