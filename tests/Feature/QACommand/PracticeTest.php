<?php

namespace Tests\Feature\QACommand;

use App\Enums\PracticeStatusEnum;
use App\Models\Question;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use Tests\Feature\QATestCase;

class PracticeTest extends QATestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        $this->markTestSkipped('must be revisited.');

        parent::setUp();
    }

    public function test_practice_should_go_to_add_question_on_no_question_exists()
    {
        $this->assertEquals(0, Question::count());

        $this->login()
            ->expectsChoice('Choose one option', QAStatesEnum::Practice, QAStatesEnum::mainMenu())
            ->expectsOutput('No question to ask.')
            ->expectsConfirmation('Want to Add one?', 'no')

            // add a question
            ->expectsQuestion('Enter the question body please', 'Question body')
            ->expectsQuestion('Enter the answer please', 'Question answer')
            ->expectsOutput('The question has been added successfully.')
            ->expectsConfirmation('Add another one?', 'no')

            // back to main menu
            ->expectsChoice('Choose one option', QAStatesEnum::Exit, QAStatesEnum::mainMenu());
    }

    /**
     * @dataProvider practiceDataProvider
     *
     * @param array $statuses
     */
    public function test_practice_correct_answer_should_print_and_mark_as_correct(array $statuses): void
    {
        foreach ($statuses as $status => $count) {
            Question::factory($count)->create(['user_id' => $this->user->id, 'status' => $status]);
        }

        $notCorrect = $this->user->questions()
            ->where('Status', '!=', PracticeStatusEnum::Correct)
            ->get(['id', 'body', 'status']);

        $firstElement = $this->user->questions()->find($notCorrect->first()->id);

        $this->login()
            ->expectsChoice('Choose one option', QAStatesEnum::Practice, QAStatesEnum::mainMenu())
            ->expectsTable(
                ['ID', 'Question', 'Status'],
                [...$notCorrect->toArray(), ...$this->practiceTableFooter($statuses)]
            )
            ->expectsChoice(
                'Choose one of the questions above',
                $firstElement->body,
                $notCorrect->pluck('body', 'id')->toArray()
            )
            ->expectsQuestion($firstElement->body, $firstElement->answer)
            ->expectsOutput(PracticeStatusEnum::getDescription(PracticeStatusEnum::Correct))
            ->expectsConfirmation('Continue?', 'no');

        $this->assertDatabaseHas('questions', [
            'id' => $firstElement->id,
            'status' => PracticeStatusEnum::Correct
        ]);
    }

    private function practiceTableFooter(array $statuses): array
    {
        $total = $statuses[PracticeStatusEnum::NotAnswered] + $statuses[PracticeStatusEnum::Correct] + $statuses[PracticeStatusEnum::Incorrect];
        $progress = $statuses[PracticeStatusEnum::Correct] * 100 / $total;

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

    /**
     * @dataProvider PracticeDataProvider
     *
     * @param array $statuses
     */
    public function test_practice_incorrect_answer_should_print_and_mark_as_incorrect(array $statuses): void
    {
        foreach ($statuses as $status => $count) {
            Question::factory($count)->create(['user_id' => $this->user->id, 'status' => $status]);
        }

        $notCorrect = $this->user->questions()
            ->where('Status', '!=', PracticeStatusEnum::Correct)
            ->get(['id', 'body', 'status']);

        $firstElement = $this->user->questions()->find($notCorrect->first()->id);

        $wrongAnswer = sprintf('wrong %s', $firstElement->answer);

        $this->login()
            ->expectsChoice('Choose one option', QAStatesEnum::Practice, QAStatesEnum::mainMenu())
            ->expectsTable(
                ['ID', 'Question', 'Status'],
                [...$notCorrect->toArray(), ...$this->practiceTableFooter($statuses)]
            )
            ->expectsChoice(
                'Choose one of the questions above',
                $firstElement->body,
                $notCorrect->pluck('body', 'id')->toArray()
            )
            ->expectsQuestion($firstElement->body, $wrongAnswer)
            ->expectsOutput(PracticeStatusEnum::getDescription(PracticeStatusEnum::Incorrect))
            ->expectsConfirmation('Continue?', 'no')
            ->expectsChoice('Choose one option', QAStatesEnum::Exit, QAStatesEnum::mainMenu());

        $this->assertDatabaseHas('questions', [
            'id' => $firstElement->id,
            'status' => PracticeStatusEnum::Incorrect
        ]);
    }

    /**
     * @dataProvider PracticeDataProvider
     *
     * @param array $statuses
     */
    public function test_answer_should_be_valid(array $statuses): void
    {
        foreach ($statuses as $status => $count) {
            Question::factory($count)->create(['user_id' => $this->user->id, 'status' => $status]);
        }

        $notCorrect = $this->user->questions()
            ->where('Status', '!=', PracticeStatusEnum::Correct)
            ->get(['id', 'body', 'status']);

        $firstElement = $this->user->questions()->find($notCorrect->first()->id);

        $emptyAnswer = '';
        $shortAnswer = 'a';

        $this->login()
            ->expectsChoice('Choose one option', QAStatesEnum::Practice, QAStatesEnum::mainMenu())
            ->expectsTable(
                ['ID', 'Question', 'Status'],
                [...$notCorrect->toArray(), ...$this->practiceTableFooter($statuses)]
            )
            ->expectsChoice(
                'Choose one of the questions above',
                $firstElement->body,
                $notCorrect->pluck('body', 'id')->toArray()
            )
            ->expectsQuestion($firstElement->body, $emptyAnswer)
            ->expectsOutput('The answer field is required.')
            ->expectsQuestion($firstElement->body, $shortAnswer)
            ->expectsOutput('The answer must be at least 2 characters.')
            ->expectsQuestion($firstElement->body, $firstElement->answer)
            ->expectsConfirmation('Continue?', 'no')
            ->expectsChoice('Choose one option', QAStatesEnum::Exit, QAStatesEnum::mainMenu());
    }

    public function practiceDataProvider(): array
    {
        return [
            [
                'statuses' => [
                    PracticeStatusEnum::NotAnswered => 10,
                    PracticeStatusEnum::Correct => 5,
                    PracticeStatusEnum::Incorrect => 5,
                ],
            ],
            [
                'statuses' => [
                    PracticeStatusEnum::NotAnswered => 20,
                    PracticeStatusEnum::Correct => 0,
                    PracticeStatusEnum::Incorrect => 0,
                ],
            ],
            [
                'statuses' => [
                    PracticeStatusEnum::NotAnswered => 0,
                    PracticeStatusEnum::Correct => 0,
                    PracticeStatusEnum::Incorrect => 20,
                ],
            ],
        ];
    }
}
