<?php

namespace App\StateMachines\Machines\QA;

use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Interfaces\TransitionInterface;
use App\StateMachines\Interfaces\TransitionsInterface;
use App\StateMachines\Transition;
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
use Exception;
use Illuminate\Console\Command;

class QATransitions implements TransitionsInterface
{
    private array $transitions = [];
    private StateInterface $exitState;
    private StateInterface $initialState;

    public function __construct(Command $command)
    {
        /**
         * if you remove the onlyEmail it will prompt for email and password
         * but now authentication system only ask for the password
         */
        $authentication = new Authenticate($command);
        $mainMenu = new MainMenu($command);
        $addQuestion = new AddQuestion($command);
        $listQuestions = new ListQuestions($command);
        $practice = new Practice($command);
        $stats = new Stats($command);
        $reset = new Reset($command);
        $exit = new ExitApp($command);

        $this->setInitialState($authentication);
        $this->setExitState($exit);

        $this->addTransitions([
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
        ]);
    }

    public function next(StateInterface $state, string $action): StateInterface
    {
        foreach ($this->transitions as $transition) {
            if ($destination = $transition->destination($state, $action)) {
                return $destination;
            }
        }

        throw new Exception(
            sprintf('No path defined to any state from `%s` with the action `%s`', $state->name(), $action)
        );
    }

    public function transitions(): array
    {
        return $this->transitions;
    }

    public function setInitialState(StateInterface $state): void
    {
        // we can set the initial state to mainMenu and bypass the authentication step
        $this->initialState = $state;
    }

    public function setExitState(StateInterface $state): void
    {
        $this->exitState = $state;
    }

    public function addTransition(TransitionInterface $transition): void
    {
        $this->transitions[] = $transition;
    }

    public function addTransitions($transitions)
    {
        foreach ($transitions as $transition) {
            $this->addTransition($transition);
        }
    }

    public function initialState(): StateInterface
    {
        return $this->initialState;
    }

    public function exitState(): StateInterface
    {
        return $this->exitState;
    }
}
