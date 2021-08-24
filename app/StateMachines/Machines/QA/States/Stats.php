<?php

namespace App\StateMachines\Machines\QA\States;

use App\Enums\PracticeStatusEnum;
use App\Models\Question;
use App\Models\QuestionUser;
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
        $all = Question::count();
        $answered = QuestionUser::count();
        $correct = QuestionUser::where('status', PracticeStatusEnum::Correct)->count();

        $this->command->table(['Title', 'Value'], [
            ['Total', $all],
            ['Answered', sprintf('%%%s', number_format($answered * 100 / $all))],
            ['Correct', sprintf('%%%s', number_format($correct * 100 / $all))]
        ]);
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
