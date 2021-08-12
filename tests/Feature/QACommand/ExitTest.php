<?php

namespace Tests\Feature\QACommand;

use App\Models\Question;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\QATestCase;

class ExitTest extends QATestCase
{
    public function test_manual_exit_returns_0(): void
    {
        $this->login()
            ->expectsChoice('Choose one option', QAStatesEnum::Exit, QAStatesEnum::mainMenu())
            ->assertExitCode(0);
    }
}
