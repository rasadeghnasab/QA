<?php

namespace App\StateMachines\States;

use App\StateMachines\Interfaces\StateInterface;
use Illuminate\Console\Command;

class MainMenu implements StateInterface
{
    public function handle(Command $command): string|bool
    {
        $defaultIndex = 0;
        $choice = $command->choice(
            'Choose one option',
            [
                'Create a question',
                'List questions',
                'Practice',
                'Stats',
                'Reset',
                'Exit'
            ],
            $defaultIndex
        );

        $this->clearScreen();

        return $choice;
    }

    public function getName(): string
    {
        return self::class;
    }

    private function clearScreen()
    {
        system('clear');
    }
}
