<?php

namespace App\StateMachines\Machines\QA\States;

use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;

class ListQuestions implements StateInterface
{
    private Command $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    public function handle(): string
    {
        $questions = $this->command->user()->questions()->get(['id', 'body', 'answer'])->toArray();

        if (empty($questions)) {
            $this->command->warn('You do not have any question!');

            return QAStatesEnum::MainMenu;
        }

        $this->command->table(
            ['ID', 'Question', 'Answer'],
            $questions,
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
