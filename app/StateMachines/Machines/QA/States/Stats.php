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
        /**
         * Assigment requested this
         * general stats
         */
        list($header, $data) = $this->simpleStats();

        /**
         * Extra
         * users score board.
         */
//        list($header, $data) = $this->scoreBoard();

        $this->command->table($header, $data);

        $this->command->newLine();

        return QAStatesEnum::MainMenu;
    }

    /**
     * Returns scoreboard data and header
     * Shows a list of users who have answered questions and the number of
     *
     * @return array
     */
    private function usersScoreBoardStats(): array
    {
        $scoreBoard = QuestionUser::scoreBoard()->get()->toArray();
        $header = [
            'name',
            'email',
            'total answered',
            'incorrect',
            'correct',
        ];

        return [$header, $scoreBoard];
    }

    private function simpleStats(): array
    {
        $all = Question::count();

        $answered = $correct = 0;
        if ($all > 0) {
            $answered = QuestionUser::count();
            $correct = QuestionUser::where('status', PracticeStatusEnum::Correct)->count();

            $answered = number_format($answered * 100 / $all);
            $correct = number_format($correct * 100 / $all);
        }

        $header = ['Title', 'Value'];
        $data = [
            ['Total', $all],
            ['Answered', sprintf('%%%s', $answered)],
            ['Correct', sprintf('%%%s', $correct)]
        ];

        return [$header, $data];
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
