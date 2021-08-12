<?php

namespace App\StateMachines\Machines\QA\States;

use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Console\Command;

class Stats extends Command implements StateInterface
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qanda:stats {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Q&A app made with Laravel + Artisan';

    public function handle(): string
    {
        $questions = $this->user()->questions()->get();

        $answered = $correct = 0;
        if ($all = $questions->count()) {
            $answered = $questions->whereIn('status', ['Correct', 'Incorrect'])->count();
            $answered = number_format($answered * 100 / $all);

            $correct = $questions->where('status', 'Correct')->count();
            $correct = number_format($correct * 100 / $all);
        }

        $this->table(['Title', 'Value'], [
            ['Total', $all],
            ['Answered', sprintf('%%%s', $answered)],
            ['Correct', sprintf('%%%s', $correct)]
        ]);
        $this->newLine();

        return QAStatesEnum::MainMenu;
    }

    public function name(): string
    {
        return self::class;
    }

    public function action(): string
    {
        return QAStatesEnum::Stats;
    }

    public function signature(): string
    {
        return $this->signature;
    }
}
