<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();

        $email = 'test@test.com';
        $password = 'password';

        User::factory()->create([
                                    'email' => 'test@test.com',
                                    'password' => Hash::make('password'),
                                ]);

        $userInfo = <<<EOT
<bg=cyan;fg=bright-white>\t User: {$email} </>
<bg=cyan;fg=bright-white>\t Pass: {$password}      </>
EOT;

        $this->command->line("\n{$userInfo}\n");
    }
}
