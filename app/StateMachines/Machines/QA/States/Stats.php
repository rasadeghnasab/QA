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

        $all = $questions->count();
        $answered = $questions->whereIn('status', ['Correct', 'Incorrect'])->count();
        $correct = $questions->where('status', 'Correct')->count();

        $command->titledTable(['Title', 'Value'], [
            ['Total', $questions->count()],
            ['Answered', sprintf('%%%s', number_format($answered * 100 / $all))],
            ['Correct', sprintf('%%%s', number_format($correct * 100 / $all))]
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
