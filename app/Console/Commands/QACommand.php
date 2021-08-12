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
        $machine = app()->make('QAStateMachine');

        return $machine->start($this, new QATransitions(new QAMap($this)));
    }

    /**
     * Format input to textual table.
     *
     * @param array $headers
     * @param Arrayable|array $rows
     * @param string $header
     * @param string $footer
     * @param string $tableStyle
     * @return void
     */
    public function titledTable($headers, $rows, string $header = '', string $footer = '', $tableStyle = 'default')
    {
        $table = new Table($this->output);

        if ($rows instanceof Arrayable) {
            $rows = $rows->toArray();
        }

        $table->setHeaders((array)$headers)->setRows($rows)->setStyle($tableStyle);

        if (!empty($header)) {
            $table->setHeaderTitle($header);
        }

        if (!empty($footer)) {
            $table->setFooterTitle($footer);
        }

        $table->render();
    }
}
