<?php

namespace App\StateMachines\States;

use App\StateMachines\Interfaces\StateInterface;
use Illuminate\Console\Command;

class ListQuestions implements StateInterface
{
    public function handle(Command $command): string
    {
        $questions = $command->user()->questions()->get(['id', 'body'])->toArray();

        $command->titledTable(
            ['ID', 'Question'],
            $questions,
            'Questions',
            '',
            'borderless',
        );

        return 'MainMenu';
    }

    public function getName(): string
    {
        return self::class;
    }
}
