<?php

namespace App\StateMachines\Machines\QA\States;

use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Console\Command;

class Stats implements StateInterface
{
    private Command $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    public function handle(): string
    {
        $questions = $this->command->user()->questions()->get();

        $answered = $correct = 0;
        if ($all = $questions->count()) {
            $answered = $questions->whereIn('status', ['Correct', 'Incorrect'])->count();
            $answered = number_format($answered * 100 / $all);

            $correct = $questions->where('status', 'Correct')->count();
            $correct = number_format($correct * 100 / $all);
        }

        $this->command->titledTable(['Title', 'Value'], [
            ['Total', $all],
            ['Answered', sprintf('%%%s', $answered)],
            ['Correct', sprintf('%%%s', $correct)]
        ],
            'Stats'
        );
        $this->command->newLine();

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
