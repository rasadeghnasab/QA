<?php

namespace App\StateMachines\Machines\QA\States;

use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Console\Command;

class ListQuestions implements StateInterface
{
    public function handle(Command $command): string
    {
        $questions = $command->user()->questions()->get(['id', 'body'])->toArray();

        if (empty($questions)) {
            $command->warn(' You do not have any question!');

            return QAStatesEnum::MainMenu;
        }

        $command->titledTable(
            ['ID', 'Question'],
            $questions,
            'Questions',
            '',
            'box-double',
        );

        return QAStatesEnum::MainMenu;
    }

    public function name(): string
    {
        return self::class;
    }

    public function action(): string
    {
        return QAStatesEnum::ListQuestions;
    }
}
