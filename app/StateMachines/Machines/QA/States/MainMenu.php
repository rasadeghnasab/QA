<?php

namespace App\StateMachines\Machines\QA\States;

use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Console\Command;

class MainMenu extends Command implements StateInterface
{
    public function handle(): string
    {
        $this->info(sprintf('User: %s', $this->user()->email));
        $choice = $this->choice(
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

    private function clearScreen(): void
    {
        if (app()->environment('testing')) {
            return;
        }

        system('clear');
    }
}
