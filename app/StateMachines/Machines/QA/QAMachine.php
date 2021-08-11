<?php

namespace App\StateMachines\Machines\QA;

use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Interfaces\TransitionInterface;
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

class QAMachine implements MachineInterface
{
    /**
     * @var MachineInterface
     */
    private MachineInterface $machine;

    public function __construct(MachineInterface $machine)
    {
        $this->machine = $machine;
    }

    public function start(Command $command): int
    {
        $this->make();

        return $this->machine->start($command);
    }

    public function setInitialState(StateInterface $state): void
    {
        // we can set the initial state to mainMenu and bypass the authentication step
        $this->machine->setInitialState($state);
    }

    public function setExitState(StateInterface $state): void
    {
        $this->machine->setExitState($state);
    }

    public function addTransition(TransitionInterface $transition): void
    {
        $this->machine->addTransition($transition);
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
            $this->addTransition($transition);
        }
    }

    private function setSpecialStates(): void
    {
        $this->setInitialState((new Authenticate())->onlyEmail());
        $this->setExitState(new ExitApp);
    }

    private function transitions(): array
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
