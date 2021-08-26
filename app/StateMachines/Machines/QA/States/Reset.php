<?php

namespace App\StateMachines\Machines\QA\States;

use App\Models\QuestionUser;
use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Console\Command;

class Reset implements StateInterface
{
    private Command $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    public function handle(): string
    {
        if (!$this->command->confirm('Are you sure? (You can not undo this action)')) {
            return QAStatesEnum::MainMenu;
        }

        /**
         * Remove all the practices for all users
         */
        QuestionUser::query()->delete();

        /**
         * It wasn't clear if we want to reset the current user practices or all users
         * So if we want to only remove user practices we can do this
         */
        // $this->command->user()->practices()->delete();

        $this->command->warn('The practice reset successfully.');

        return QAStatesEnum::MainMenu;
    }

    public function name(): string
    {
        return self::class;
    }

    public function action(): string
    {
        return QAStatesEnum::Reset;
    }
}
