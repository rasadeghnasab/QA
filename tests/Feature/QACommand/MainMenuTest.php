<?php

namespace Tests\Feature\QACommand;

use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\QATestCase;

class MainMenuTest extends QATestCase
{
    use RefreshDatabase;

    public function test_main_menu(): void
    {
        $this->login()
            ->expectsOutput("User: {$this->user->email}")
            ->expectsChoice('Choose one option', QAStatesEnum::Exit, QAStatesEnum::mainMenu());
    }
}
