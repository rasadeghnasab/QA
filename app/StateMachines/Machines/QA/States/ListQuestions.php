<?php

namespace App\StateMachines\Machines\QA\States;

use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;

class ListQuestions extends Command implements StateInterface
{
    public function handle(): string
    {
        $questions = $this->user()->questions()->get(['id', 'body'])->toArray();

        if (empty($questions)) {
            $this->warn('You do not have any question!');

            return QAStatesEnum::MainMenu;
        }

        $this->table(
            ['ID', 'Question'],
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
