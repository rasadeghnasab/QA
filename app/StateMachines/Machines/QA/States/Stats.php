<?php

namespace App\StateMachines\Machines\QA\States;

use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Console\Command;

class Stats implements StateInterface
{
    public function handle(Command $command): string
    {
        $questions = $command->user()->questions()->get();

        $answered = $correct = 0;
        if ($all = $questions->count()) {
            $answered = $questions->whereIn('status', ['Correct', 'Incorrect'])->count();
            $answered = number_format($answered * 100 / $all);

            $correct = $questions->where('status', 'Correct')->count();
            $correct = number_format($correct * 100 / $all);
        }

        $command->titledTable(['Title', 'Value'], [
            ['Total', $all],
            ['Answered', sprintf('%%%s', $answered)],
            ['Correct', sprintf('%%%s', $correct)]
        ],
            'Stats'
        );
        $command->newLine();

        return QAStatesEnum::MainMenu;
    }

    public function name(): string
    {
        return self::class;
    }

    public function action(): string
    {
        return QAStatesEnum::Stats;
    }
}
