<?php

namespace App\StateMachines\Machines\QA\States;

use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Console\Command;

class MainMenu implements StateInterface
{
    private Command $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    public function handle(): string
    {
        $this->command->info(sprintf('User: %s', $this->command->user()->email));
        $choice = $this->command->choice(
            'Choose one option',
            QAStatesEnum::mainMenu(),
            $defaultIndex = 0
        );

        $this->clearScreen();

        return $choice;
    }

    public function name(): string
    {
        return self::class;
    }

    public function action(): string
    {
        return QAStatesEnum::MainMenu;
    }

    private function clearScreen()
    {
        system('clear');
    }
}
