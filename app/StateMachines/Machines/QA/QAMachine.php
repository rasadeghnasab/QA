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

    public function start(Command $command)
    {
        $this->make();
        $this->machine->start($command);
    }

    /**
     * NOTE:
     * We can make this method dynamic and read the values from a database
     * But for now I let it be like that.
     * In this way it's easier to read and understand
     */
    private function make(): void
    {
        $this->setSpecialStates();

        foreach ($this->transitions() as $transition) {
            $this->machine->addTransition($transition);
        }
    }

    private function setSpecialStates()
    {
        $authentication = (new Authenticate())->onlyEmail();
        $exit = new ExitApp();

        // we can set the initial state to mainMenu and bypass the authentication step
        $this->machine->setInitialState($authentication);
        $this->machine->setExitState($exit);
    }

    private function transitions()
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

        return [
            // authentication
            new Transition($authentication, $authentication),

            // main menu
            new Transition($mainMenu, $addQuestion),
            new Transition($mainMenu, $listQuestions),
            new Transition($mainMenu, $practice),
            new Transition($mainMenu, $stats),
            new Transition($mainMenu, $reset),
            new Transition($mainMenu, $exit),

            // recursive
            new Transition($practice, $practice),
            new Transition($addQuestion, $addQuestion),

            // automatically return to the main menu
            new Transition($authentication, $mainMenu),
            new Transition($addQuestion, $mainMenu),
            new Transition($listQuestions, $mainMenu),
            new Transition($practice, $mainMenu),
            new Transition($stats, $mainMenu),
            new Transition($reset, $mainMenu),
        ];
    }
}
