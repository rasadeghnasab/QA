<?php

namespace App\Console\Commands;

use App\Models\User;
use App\StateMachines\Machines\QA\QAMap;
use App\StateMachines\Machines\QA\QATransitions;
use Illuminate\Console\Command;
use Illuminate\Contracts\Support\Arrayable;
use Symfony\Component\Console\Helper\Table;

class QACommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qanda:interactive {--with-password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Q&A app made with Laravel + Artisan';

    private User $user;

    public function __construct()
    {
        parent::__construct();
    }

    public function user(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $this->clearScreen();
        $machine = app()->make('QAStateMachine');

        return $machine->start($this, new QATransitions(new QAMap($this)));
    }

    public function clearScreen(): void
    {
        if (app()->environment('testing')) {
            return;
        }

        if ($this->isLinux()) {
            system('clear');
            return;
        }

        system('cls');
    }

    private function isLinux(): bool
    {
        return PHP_OS === 'Linux';
    }
}
