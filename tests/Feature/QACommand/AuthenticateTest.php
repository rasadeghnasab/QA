<?php

namespace Tests\Feature\QACommand;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticateTest extends TestCase
{
    use RefreshDatabase;

    private string $email;

    public function setUp(): void
    {
        parent::setUp();

        $this->email = 'test@test.com';

        User::factory()->create(['email' => $this->email]);
    }

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
            ->expectsQuestion("Enter your email address\n If the email doesn't exist it will be created", $this->email)
            ->expectsOutput('You logged in successfully')
            ->expectsOutput("User: {$this->email}");
    }

    public function test_authentication_wrong_email_provided()
    {
        $this->artisan('qanda:interactive')
            ->expectsQuestion("Enter your email address\n If the email doesn't exist it will be created", 'not_an_email')
            ->expectsOutput('The email must be a valid email address.')
            ->expectsQuestion("Enter your email address\n If the email doesn't exist it will be created", 'test@test.com');
    }

    public function test_authentication_no_email_provided(): void
    {
        $this->artisan('qanda:interactive')
            ->expectsQuestion("Enter your email address\n If the email doesn't exist it will be created", '')
            ->expectsOutput('The email field is required.')
            ->expectsQuestion("Enter your email address\n If the email doesn't exist it will be created", 'test@test.com');
    }

    public function test_full_authentication_short_password_provided(): void
    {
        $this->artisan('qanda:interactive --with-password')
            ->expectsQuestion("Enter your email address\n If the email doesn't exist it will be created", $this->email)
            ->expectsQuestion("Enter your password", 'short')
            ->expectsOutput('The password must be at least 8 characters.');
    }

    public function test_authentication_no_password_provided(): void
    {
        $this->artisan('qanda:interactive --with-password')
            ->expectsQuestion("Enter your email address\n If the email doesn't exist it will be created", $this->email)
            ->expectsQuestion("Enter your password", '')
            ->expectsOutput('The password field is required.');
    }

    public function test_authentication_successful_login(): void
    {
        $this->artisan('qanda:interactive --with-password')
            ->expectsQuestion("Enter your email address\n If the email doesn't exist it will be created", $this->email)
            ->expectsQuestion("Enter your password", 'password')
            ->expectsOutput('You logged in successfully')
            ->expectsChoice()
            ->expectsOutput("User: {$this->email}");
    }
}
