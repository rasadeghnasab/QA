<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class QATestCase extends TestCase
{
    protected User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    protected function login()
    {
        return $this->artisan('qanda:interactive')
            ->expectsQuestion("Enter your email address\n If the email doesn't exist it will be created", $this->user->email)
            ->expectsOutput('You logged in successfully');
    }
}
