<?php

namespace App\StateMachines\Machines\QA\States;

use App\Models\User;
use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Authenticate extends Command implements StateInterface
{
    private bool $withPassword;

    public function __construct()
    {
        $this->withPassword = (bool)$this->option('with-password');
    }

    public function handle(): string
    {
        list($email, $password) = $this->getInputs();

        $user = User::where('email', $email)->first() ?? User::factory()->create(['email' => $email, 'name' => $email]);
        $authenticated = true;

        if ($this->withPassword) {
            $authenticated = Hash::check($password, $user->getAuthPassword());
        }

        if ($authenticated) {
            $this->info('You logged in successfully');
            $this->newLine();
            $this->setUser($user);

            return QAStatesEnum::MainMenu;
        }

        $this->error('Authentication failed. Please try again.');

        return QAStatesEnum::Authenticate;
    }

    public function name(): string
    {
        return self::class;
    }

    public function action(): string
    {
        return QAStatesEnum::Authenticate;
    }

    /**
     * @param array $data
     * @throws ValidationException
     */
    private function validate(array $data): void
    {
        $rules = [
            'email' => ['required', 'email'],
        ];

        if (array_key_exists('password', $data)) {
            $rules['password'] = ['required', 'min:8'];
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private function getInputs(): array
    {
        $question = "Enter your email address\n If the email doesn't exist it will be created";
//        $question = "Enter your email address";
        $email = $this->ask($question, 'test@test.com');
        $data = ['email' => $email];

        $password = '';
        if ($this->withPassword) {
            $password = $this->secret('Enter your password');
            $data['password'] = $password;
        }

        $this->validate($data);

        return [$email, $password];
    }
}
