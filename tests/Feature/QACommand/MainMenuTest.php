<?php

namespace Tests\Feature\QACommand;

use App\Models\User;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\PendingCommand;
use Tests\TestCase;

class MainMenuTest extends TestCase
{
    use RefreshDatabase;

    private PendingCommand $artisan;

    public function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    private function login()
    {
        $this->artisan('qanda:interactive')
            ->expectsQuestion("Enter your email address\n If the email doesn't exist it will be created", 'test@test.com');
    }

    public function test_main_menu(): void
    {
//        $this->artisan('qanda:interactive')
//            ->expectsQuestion("Enter your email address\n If the email doesn't exist it will be created", 'test@test.com')
//            ->expectsOutput('You logged in successfully');
//            $this->artisan->expectsChoice('Choose one option', QAStatesEnum::Exit, QAStatesEnum::mainMenu())
//            ->expectsOutput(sprintf('Goodbye `%s`.', 'test@test.com'));
    }
}
