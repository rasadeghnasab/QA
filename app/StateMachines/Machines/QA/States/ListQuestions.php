<?php

namespace App\StateMachines\Machines\QA\States;

use App\Models\Question;
use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Console\Command;

class ListQuestions implements StateInterface
{
    private Command $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    public function handle(): string
    {
        $questions = Question::get(['id', 'body', 'answer'])->toArray();

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
