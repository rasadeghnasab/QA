<?php

namespace App\StateMachines\Machines\QA;

use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Interfaces\TransitionInterface;
use App\StateMachines\Interfaces\TransitionsInterface;
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
    /**
     * @var Command
     */
    private Command $command;
    /**
     * @var TransitionsInterface
     */
    private TransitionsInterface $transitions;

    public function __construct(MachineInterface $machine)
    {
        $this->machine = $machine;
    }

    public function start(Command $command, TransitionsInterface $transitions): int
    {
        $this->command = $command;
        $this->transitions = $transitions;

        $this->drawLogo($command);

        return $this->machine->start($command, $transitions);
    }

    private function drawLogo(Command $command): void
    {
        $asciLogo = <<<EOT
<fg=bright-blue>
   ____                    _
  / __ \                  | |     /\
 | |  | |   __ _ _ __   __| |    /  \
 | |  | |  / _` | '_ \ / _` |   / /\ \
 | |__| | | (_| | | | | (_| |  / ____ \
  \___\_\  \__,_|_| |_|\__,_| /_/    \_\
</>
EOT;

        $command->line("\n{$asciLogo}\n");
    }
}
