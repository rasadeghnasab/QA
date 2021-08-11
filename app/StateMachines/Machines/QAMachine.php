<?php

namespace App\StateMachines\Machines;

use App\StateMachines\Interfaces\MachineInterface;
use App\StateMachines\States\AddQuestion;
use App\StateMachines\States\Authenticate;
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

    /**
     * NOTE:
     * We can make this method dynamic and read the values from a database
     * But for now I let it be like that.
     * In this way it's easier to read and understand
     */
    private function make(): void
    {
        /**
         * if you remove the onlyEmail it will prompt for email and password
         * but now authentication system only ask for the password
         */
        $authentication = (new Authenticate())->onlyEmail();
        $mainMenu = new MainMenu();
        $addQuestion = new AddQuestion();
        $listQuestions = new ListQuestions();
        $practice = new Practice();
        $stats = new Stats();
        $reset = new Reset();
        $exit = new ExitApp();

        // we can set the initial state to mainMenu and bypass the authentication step
        $this->machine->setInitialState($authentication);

        // authentication
        $this->machine->addTransition(new Transition('Authenticate', $authentication, $authentication));

        // main menu
        $this->machine->addTransition(new Transition('Create a question', $mainMenu, $addQuestion));
        $this->machine->addTransition(new Transition('List questions', $mainMenu, $listQuestions));
        $this->machine->addTransition(new Transition('Practice', $mainMenu, $practice));
        $this->machine->addTransition(new Transition('Stats', $mainMenu, $stats));
        $this->machine->addTransition(new Transition('Reset', $mainMenu, $reset));
        $this->machine->addTransition(new Transition('Exit', $mainMenu, $exit));

        // recursive
        $this->machine->addTransition(new Transition('Practice', $practice, $practice));
        $this->machine->addTransition(new Transition('Create a question', $addQuestion, $addQuestion));

        // automatically return to the main menu
        $this->machine->addTransition(new Transition('MainMenu', $authentication, $mainMenu));
        $this->machine->addTransition(new Transition('MainMenu', $addQuestion, $mainMenu));
        $this->machine->addTransition(new Transition('MainMenu', $listQuestions, $mainMenu));
        $this->machine->addTransition(new Transition('MainMenu', $practice, $mainMenu));
        $this->machine->addTransition(new Transition('MainMenu', $stats, $mainMenu));
        $this->machine->addTransition(new Transition('MainMenu', $reset, $mainMenu));
    }

    public function start(Command $command)
    {
        $this->make();
        $this->machine->start($command);
    }
}
