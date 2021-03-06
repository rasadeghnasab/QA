<?php

namespace Tests\Feature\QACommand;

use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\QATestCase;

class AuthenticateTest extends QATestCase
{
    use RefreshDatabase;

    private string $email;

    public function test_user_will_be_created_if_it_not_exists(): void
    {
        $email = 'not_existed@user.com';

        $this->artisan('qanda:interactive')
            ->expectsQuestion("Enter your email address\n If the email doesn't exist it will be created", $email)
            ->expectsOutput("User: {$email}");


        $this->assertDatabaseHas('users', [
            'email' => $email
        ]);
    }

    public function test_authentication_with_no_password_required(): void
    {
        $this->artisan('qanda:interactive')
            ->expectsQuestion(
                "Enter your email address\n If the email doesn't exist it will be created",
                $this->user->email
            )
            ->expectsOutput('You logged in successfully')
            ->expectsOutput("User: {$this->user->email}");
    }

    public function test_authentication_wrong_email_provided()
    {
        $this->artisan('qanda:interactive')
            ->expectsQuestion(
                "Enter your email address\n If the email doesn't exist it will be created",
                'not_an_email'
            )
            ->expectsOutput('The email must be a valid email address.')
            ->expectsQuestion(
                "Enter your email address\n If the email doesn't exist it will be created",
                'test@test.com'
            );
    }

    public function test_authentication_no_email_provided(): void
    {
        $this->artisan('qanda:interactive')
            ->expectsQuestion("Enter your email address\n If the email doesn't exist it will be created", '')
            ->expectsOutput('The email field is required.')
            ->expectsQuestion(
                "Enter your email address\n If the email doesn't exist it will be created",
                'test@test.com'
            );
    }

    public function test_full_authentication_short_password_provided(): void
    {
        $this->artisan('qanda:interactive --with-password')
            ->expectsQuestion(
                "Enter your email address\n If the email doesn't exist it will be created",
                $this->user->email
            )
            ->expectsQuestion("Enter your password", 'short')
            ->expectsOutput('The password must be at least 8 characters.');
    }

    public function test_authentication_no_password_provided(): void
    {
        $this->artisan('qanda:interactive --with-password')
            ->expectsQuestion(
                "Enter your email address\n If the email doesn't exist it will be created",
                $this->user->email
            )
            ->expectsQuestion("Enter your password", '')
            ->expectsOutput('The password field is required.');
    }

    public function test_authentication_show_main_menu_after_successful_login(): void
    {
        $this->artisan('qanda:interactive --with-password')
            ->expectsQuestion(
                "Enter your email address\n If the email doesn't exist it will be created",
                $this->user->email
            )
            ->expectsQuestion("Enter your password", 'password')
            ->expectsOutput('You logged in successfully')
            ->expectsOutput("User: {$this->user->email}")
            ->expectsChoice('Choose one option', 5, QAStatesEnum::mainMenu());
    }
}
