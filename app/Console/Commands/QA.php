<?php

namespace App\Console\Commands;

use App\Models\User;
use App\StateMachines\Machines\QAMachine;
use App\StateMachines\StateMachine;
use Illuminate\Console\Command;
use App\Models\Question;
use Illuminate\Contracts\Support\Arrayable;
use Symfony\Component\Console\Helper\Table;

class QA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qanda:interactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private User $user;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->user = User::where('id', 1)->first() ?? new User(['id' => 1]);
    }

    public function user(): User
    {
        return $this->user;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $machine = new QAMachine(new StateMachine());

        $machine->start($this);
    }

    /**
     * Format input to textual table.
     *
     * @param array $headers
     * @param \Illuminate\Contracts\Support\Arrayable|array $rows
     * @param string $tableStyle
     * @param string $header
     * @param string $footer
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
