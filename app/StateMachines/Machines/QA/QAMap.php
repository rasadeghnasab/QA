<?php

namespace App\StateMachines\Machines\QA;

use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Interfaces\StateMachineMapInterface;
use App\StateMachines\Machines\QA\States\{
    AddQuestion,
    Authenticate,
    ExitApp,
    ListQuestions,
    MainMenu,
    Practice,
    Reset,
    Stats,
};
use Illuminate\Console\Command;

class QAMap implements StateMachineMapInterface
{
    private Command $command;
    private array $states;

    public function __construct(Command $command)
    {
        $this->command = $command;

        $this->makeStates();
    }

    private function makeStates(): void
    {
        $this->states = [
            /**
             * if you remove the onlyEmail it will prompt for email and password
             * but now authentication system only ask for the password
             */
            QAStatesEnum::Authenticate => new Authenticate($this->command),
            QAStatesEnum::MainMenu => new MainMenu($this->command),
            QAStatesEnum::AddQuestion => new AddQuestion($this->command),
            QAStatesEnum::ListQuestions => new ListQuestions($this->command),
            QAStatesEnum::Practice => new Practice($this->command),
            QAStatesEnum::Stats => new Stats($this->command),
            QAStatesEnum::Reset => new Reset($this->command),
            QAStatesEnum::Exit => new ExitApp($this->command),
        ];
    }

    public function states(): array
    {
        return $this->states;
    }

    public function path(): array
    {
        return [
            [
                'source' => $this->states[QAStatesEnum::Authenticate],
                'destination' => $this->states[QAStatesEnum::Authenticate],
            ],
            [
                'source' => $this->states[QAStatesEnum::MainMenu],
                'destination' => $this->states[QAStatesEnum::AddQuestion],
            ],
            [
                'source' => $this->states[QAStatesEnum::MainMenu],
                'destination' => $this->states[QAStatesEnum::ListQuestions],
            ],
            [
                'source' => $this->states[QAStatesEnum::MainMenu],
                'destination' => $this->states[QAStatesEnum::Practice],
            ],
            [
                'source' => $this->states[QAStatesEnum::MainMenu],
                'destination' => $this->states[QAStatesEnum::Stats],
            ],
            [
                'source' => $this->states[QAStatesEnum::MainMenu],
                'destination' => $this->states[QAStatesEnum::Reset],
            ],
            [
                'source' => $this->states[QAStatesEnum::MainMenu],
                'destination' => $this->states[QAStatesEnum::Exit],
            ],
            [
                'source' => $this->states[QAStatesEnum::Practice],
                'destination' => $this->states[QAStatesEnum::Practice],
            ],
            [
                'source' => $this->states[QAStatesEnum::AddQuestion],
                'destination' => $this->states[QAStatesEnum::AddQuestion],
            ],
            [
                'source' => $this->states[QAStatesEnum::Authenticate],
                'destination' => $this->states[QAStatesEnum::MainMenu],
            ],
            [
                'source' => $this->states[QAStatesEnum::AddQuestion],
                'destination' => $this->states[QAStatesEnum::MainMenu],
            ],
            [
                'source' => $this->states[QAStatesEnum::ListQuestions],
                'destination' => $this->states[QAStatesEnum::MainMenu],
            ],
            [
                'source' => $this->states[QAStatesEnum::Practice],
                'destination' => $this->states[QAStatesEnum::MainMenu],
            ],
            [
                'source' => $this->states[QAStatesEnum::Stats],
                'destination' => $this->states[QAStatesEnum::MainMenu],
            ],
            [
                'source' => $this->states[QAStatesEnum::Reset],
                'destination' => $this->states[QAStatesEnum::MainMenu],
            ],
            [
                'source' => $this->states[QAStatesEnum::Practice],
                'destination' => $this->states[QAStatesEnum::AddQuestion],
            ],
        ];
    }

    public function initialState(): StateInterface
    {
        return $this->states[QAStatesEnum::Authenticate];
    }

    public function exitState(): StateInterface
    {
        return $this->states[QAStatesEnum::Exit];
    }
}
