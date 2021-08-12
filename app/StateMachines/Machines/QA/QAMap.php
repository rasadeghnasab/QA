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

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    public function states(): array
    {
        return [
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

    public function path(): array
    {
        $states = $this->states();

        return [
            [
                'source' => $states[QAStatesEnum::Authenticate],
                'destination' => $states[QAStatesEnum::Authenticate],
            ],
            [
                'source' => $states[QAStatesEnum::MainMenu],
                'destination' => $states[QAStatesEnum::AddQuestion],
            ],
            [
                'source' => $states[QAStatesEnum::MainMenu],
                'destination' => $states[QAStatesEnum::ListQuestions],
            ],
            [
                'source' => $states[QAStatesEnum::MainMenu],
                'destination' => $states[QAStatesEnum::Practice],
            ],
            [
                'source' => $states[QAStatesEnum::MainMenu],
                'destination' => $states[QAStatesEnum::Stats],
            ],
            [
                'source' => $states[QAStatesEnum::MainMenu],
                'destination' => $states[QAStatesEnum::Reset],
            ],
            [
                'source' => $states[QAStatesEnum::MainMenu],
                'destination' => $states[QAStatesEnum::Exit],
            ],
            [
                'source' => $states[QAStatesEnum::Practice],
                'destination' => $states[QAStatesEnum::Practice],
            ],
            [
                'source' => $states[QAStatesEnum::AddQuestion],
                'destination' => $states[QAStatesEnum::AddQuestion],
            ],
            [
                'source' => $states[QAStatesEnum::Authenticate],
                'destination' => $states[QAStatesEnum::MainMenu],
            ],
            [
                'source' => $states[QAStatesEnum::AddQuestion],
                'destination' => $states[QAStatesEnum::MainMenu],
            ],
            [
                'source' => $states[QAStatesEnum::ListQuestions],
                'destination' => $states[QAStatesEnum::MainMenu],
            ],
            [
                'source' => $states[QAStatesEnum::Practice],
                'destination' => $states[QAStatesEnum::MainMenu],
            ],
            [
                'source' => $states[QAStatesEnum::Stats],
                'destination' => $states[QAStatesEnum::MainMenu],
            ],
            [
                'source' => $states[QAStatesEnum::Reset],
                'destination' => $states[QAStatesEnum::MainMenu],
            ],
        ];
    }

    public function initialState(): StateInterface
    {
        return $this->states()[QAStatesEnum::Authenticate];
    }

    public function exitState(): StateInterface
    {
        return $this->states()[QAStatesEnum::Exit];
    }
}
