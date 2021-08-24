<?php

namespace App\StateMachines\Machines\QA\States;

use App\Enums\PracticeStatusEnum;
use App\Models\Question;
use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Stats implements StateInterface
{
    private Command $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    public function handle(): string
    {
        $questions = Question::select(
            'questions.id',
            'questions.body',
            'questions.answer',
            'question_user.status',
            DB::raw(sprintf("IFNULL(question_user.status, '%s') as `status`", PracticeStatusEnum::NotAnswered))
        )->leftJoin(
            'question_user',
            'questions.id',
            'question_user.question_id'
        )
            ->get();


        $answered = $correct = 0;
        if ($all = $questions->count()) {
            $answered = $questions->whereIn('status', [PracticeStatusEnum::Correct, PracticeStatusEnum::Incorrect]
            )->count();
            $answered = number_format($answered * 100 / $all);

            $correct = $questions->where('status', PracticeStatusEnum::Correct)->count();
            $correct = number_format($correct * 100 / $all);
        }

        $this->command->table(['Title', 'Value'], [
            ['Total', $all],
            ['Answered', sprintf('%%%s', $answered)],
            ['Correct', sprintf('%%%s', $correct)]
        ]);
        $this->command->newLine();

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
}
