<?php

namespace App\StateMachines\States;

use App\StateMachines\Interfaces\StateInterface;
use Illuminate\Console\Command;

class Stats implements StateInterface
{
    public function handle(Command $command): string|bool
    {
        $questions = $command->user()->questions()->get();

        $all = $questions->count();
        $answered = $questions->where('status', 'Incorrect')->count();
        $correct = $questions->where('status', 'Correct')->count();

        $command->titledTable(['Header', 'Value'], [
            ['Total', $questions->count()],
            ['Answered', sprintf('%%%s', number_format($answered * 100 / $all))],
            ['Correct', sprintf('%%%s', number_format($correct * 100 / $all))]
        ],
            'Stats'
        );
        $command->newLine();

        return 'MainMenu';
    }

    public function getName(): string
    {
        return self::class;
    }
}
