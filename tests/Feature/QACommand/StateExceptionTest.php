<?php

namespace Tests\Feature\QACommand;

use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\QATestCase;

class StateExceptionTest extends QATestCase
{
    use RefreshDatabase;

    public function test_should_not_be_able_to_go_to_undefined_path(): void
    {
        $errorMessage = "No path defined to any state from `App\StateMachines\Machines\QA\States\MainMenu` with the action `Authenticate`";

        $this->login()
            ->expectsChoice('Choose one option', QAStatesEnum::Authenticate, QAStatesEnum::mainMenu())
            ->expectsOutput('Exiting...')
            ->expectsOutput(sprintf('Error message: %s', $errorMessage))
            ->assertExitCode(255);
    }
}
