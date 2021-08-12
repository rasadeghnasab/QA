<?php

namespace Tests\Feature\QACommand;

use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\QATestCase;

class ExitTest extends QATestCase
{
    use RefreshDatabase;

    public function test_manual_exit_returns_0(): void
    {
        $this->login()
            ->expectsChoice('Choose one option', QAStatesEnum::Exit, QAStatesEnum::mainMenu())
            ->expectsOutput(sprintf('Goodbye `%s`.', $this->user->name))
            ->assertExitCode(0);
    }
}
