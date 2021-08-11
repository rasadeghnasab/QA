<?php

namespace App\StateMachines\Machines\QA;

use App\StateMachines\Transition;
use Illuminate\Console\Command;
use App\StateMachines\Interfaces\MachineInterface;
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
        $this->machine->setExitState($exit);

        // authentication
        $this->machine->addTransition(new Transition($authentication, $authentication));

        // main menu
        $this->machine->addTransition(new Transition($mainMenu, $addQuestion));
        $this->machine->addTransition(new Transition($mainMenu, $listQuestions));
        $this->machine->addTransition(new Transition($mainMenu, $practice));
        $this->machine->addTransition(new Transition($mainMenu, $stats));
        $this->machine->addTransition(new Transition($mainMenu, $reset));
        $this->machine->addTransition(new Transition($mainMenu, $exit));

        // recursive
        $this->machine->addTransition(new Transition($practice, $practice));
        $this->machine->addTransition(new Transition($addQuestion, $addQuestion));

        // automatically return to the main menu
        $this->machine->addTransition(new Transition($authentication, $mainMenu));
        $this->machine->addTransition(new Transition($addQuestion, $mainMenu));
        $this->machine->addTransition(new Transition($listQuestions, $mainMenu));
        $this->machine->addTransition(new Transition($practice, $mainMenu));
        $this->machine->addTransition(new Transition($stats, $mainMenu));
        $this->machine->addTransition(new Transition($reset, $mainMenu));
    }

    public function start(Command $command)
    {
        $this->make();
        $this->machine->start($command);
    }
}
