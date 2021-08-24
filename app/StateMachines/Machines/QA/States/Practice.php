<?php

namespace App\StateMachines\Machines\QA\States;

use App\Enums\PracticeStatusEnum;
use App\Models\Question;
use App\Models\QuestionUser;
use App\StateMachines\Interfaces\StateInterface;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;

class Practice implements StateInterface
{
    private Command $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    public function handle(): string
    {
        $practices = Question::select(
            'questions.id',
            'questions.body',
            'questions.answer',
            DB::raw(sprintf("IFNULL(question_user.status, '%s') as `status`", PracticeStatusEnum::NotAnswered))
        )->leftJoin(
            'question_user',
            'questions.id',
            'question_user.question_id'
        )
            ->where('question_user.user_id', '=', $this->command->user()->id)
            ->orWhereNull('question_user.user_id')
            ->get();

        if ($practices->isEmpty() || $practices->where('status', '!=', 'Correct')->isEmpty()) {
            $this->command->warn('No question to ask.');
            $result = $this->command->confirm('Want to Add one?', true);

            return $result ? QAStatesEnum::AddQuestion : QAStatesEnum::MainMenu;
        }

        $this->drawProgressTable($practices);

        $this->askQuestion($practices);

        return $this->command->confirm('Continue?', true) ? QAStatesEnum::Practice : QAStatesEnum::MainMenu;
    }

    public function name(): string
    {
        return self::class;
    }

    public function action(): string
    {
        return QAStatesEnum::Practice;
    }

    /**
     * @param $practices
     */
    private function drawProgressTable($practices): void
    {
        $correct = $practices->where('status', 'Correct');
        $completed = 0;

        if ($total = $practices->count()) {
            $completed = number_format($correct->count() * 100 / $total);
        }
        $notCorrectQuestions = $practices->map(function ($question) {
            return $question->only(['id', 'body', 'status']);
        })->toArray();

        $progressFooter = $this->practiceTableFooter($completed);

        $this->command->table(
            ['ID', 'Question', 'Status'],
            [...$notCorrectQuestions, ...$progressFooter]
        );
    }

    /**
     * @param $practices
     * @return void
     */
    private function askQuestion($practices): void
    {
        $notCorrectPractices = $practices->where('status', '!=', 'Correct');
        $firstNotCorrect = $notCorrectPractices->first();

        $selected = $this->command->choice(
            'Choose one of the questions above',
            $notCorrectPractices->pluck('body', 'id')->toArray(),
            $firstNotCorrect->id ?? null,
        );

        $practice = $notCorrectPractices->where('body', $selected)->first();

        $userAnswer = $this->getInputs($practice->body);

        $status = $practice->answer === $userAnswer ? PracticeStatusEnum::Correct : PracticeStatusEnum::Incorrect;
        $this->command->warn($status);

        if ($practice->status === PracticeStatusEnum::NotAnswered) {
            QuestionUser::create([
                                     'user_id' => $this->command->user()->id,
                                     'question_id' => $practice->id,
                                     'status' => $status,
                                 ]);

            return;
        }

        QuestionUser::where('user_id', '=', $this->command->user()->id)
            ->where('question_id', '=', $practice->id)
            ->update(['status' => $status]);
    }

    private function getInputs(string $questionBody): string
    {
        $answer = '';

        do {
            try {
                $valid = true;
                $answer = $this->command->ask($questionBody);

                $this->validate(['answer' => $answer]);
            } catch (ValidationException $validationException) {
                foreach (collect($validationException->errors())->flatten() as $error) {
                    $this->command->warn($error);
                }
                $valid = false;
            }
        } while (!$valid);

        return $answer;
    }

    /**
     * @param array $data
     * @throws ValidationException
     */
    private function validate(array $data): void
    {
        $rules = [
            'answer' => ['required', 'min:2', 'max:300'],
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private function practiceTableFooter(int $progress): array
    {
        return [
            [
                new TableCell(
                    '',
                    [
                        'colspan' => 3,
                        'style' => new TableCellStyle([
                                                          'align' => 'center',
                                                          'fg' => 'white',
                                                          'bg' => 'cyan',
                                                      ])
                    ]
                ),
            ],
            [
                new TableCell(
                    sprintf('Correct answers: %%%s', $progress),
                    [
                        'colspan' => 3,
                        'style' => new TableCellStyle([
                                                          'align' => 'center',
                                                          'fg' => 'white',
                                                          'bg' => 'cyan',
                                                      ])
                    ]
                ),
            ],
            [
                new TableCell(
                    '',
                    [
                        'colspan' => 3,
                        'style' => new TableCellStyle([
                                                          'align' => 'center',
                                                          'fg' => 'white',
                                                          'bg' => 'cyan',
                                                      ])
                    ]
                ),
            ],
        ];
    }
}
