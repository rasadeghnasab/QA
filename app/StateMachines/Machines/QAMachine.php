<?php

namespace App\StateMachines\Machines;

use App\StateMachines\Interfaces\MachineInterface;
use App\StateMachines\States\ExitApp;
use App\StateMachines\States\ListQuestions;
use App\StateMachines\States\MainMenu;
use App\StateMachines\States\Practice;
use App\StateMachines\States\Reset;
use App\StateMachines\States\Stats;
use App\StateMachines\Transition;
use Illuminate\Console\Command;

class QAMachine
{
    /**
     * @var MachineInterface
     */
    private MachineInterface $machine;

    public function __construct(MachineInterface $machine)
    {
        $this->machine = $machine;
    }

    private function make(): void
    {
        $mainMenu = new MainMenu();
        $exit = new ExitApp();
        $stats = new Stats();
        $listQuestions = new ListQuestions();
        $reset = new Reset();
        $practice = new Practice();

        $this->machine->setInitialState($mainMenu);
        $this->machine->addTransition(new Transition('Exit', $mainMenu, $exit));
        $this->machine->addTransition(new Transition('Stats', $mainMenu, $stats));
        $this->machine->addTransition(new Transition('List questions', $mainMenu, $listQuestions));
        $this->machine->addTransition(new Transition('Reset', $mainMenu, $reset));

        $this->machine->addTransition(new Transition('Practice', $mainMenu, $practice));
        $this->machine->addTransition(new Transition('Continue', $practice, $practice));

        // automatically return to the main menu
        $this->machine->addTransition(new Transition('MainMenu', $stats, $mainMenu));
        $this->machine->addTransition(new Transition('MainMenu', $listQuestions, $mainMenu));
        $this->machine->addTransition(new Transition('MainMenu', $reset, $mainMenu));
        $this->machine->addTransition(new Transition('MainMenu', $practice, $mainMenu));
    }

    public function start(Command $command)
    {
        $this->make();
        $this->machine->start($command);
    }
}
