<?php

namespace Tests\Feature\QACommand;

use App\Models\Question;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\QATestCase;

class PracticeTest extends QATestCase
{
    use RefreshDatabase;

    public function test_practice_should_go_to_add_question_on_no_question_exists()
    {
        $this->assertEquals(0, $this->user->questions()->count());

        $this->login()
            ->expectsChoice('Choose one option', QAStatesEnum::Practice, QAStatesEnum::mainMenu())
            ->expectsOutput('No question to answer.')
            ->expectsConfirmation('Want to Add one?', 'yes')
            ->execute();
//            ->run();

            // add a question
//            ->expectsQuestion('Enter the question body please', 'Question body')
//            ->expectsQuestion('Enter the answer please', 'Question answer')
//            ->expectsOutput('The question has been added successfully.')
//            ->expectsConfirmation('Add another one?', 'no')

            // back to main menu
//            ->expectsChoice('Choose one option', QAStatesEnum::Exit, QAStatesEnum::mainMenu());
    }

    /**
     * @dataProvider practiceDataProvider
     *
     * @param array $statuses
     */
    public function test_practice_draw_table_as_we_expected(array $statuses): void
    {
        foreach ($statuses as $status => $count) {
            Question::factory($count)->create(['user_id' => $this->user->id, 'status' => $status]);
        }

        $this->login()
            ->expectsChoice('Choose one option', QAStatesEnum::Practice, QAStatesEnum::mainMenu())
            ->expectsTable(
                ['ID', 'Question', 'Status'],
                $this->user->questions()->where('Status', '!=', 'Correct')->get(['id', 'body', 'status'])->toArray()
            );
    }

    public function practiceDataProvider(): array
    {
        return [
            [
                'statuses' => [
                    'Not answered' => 10,
                    'Correct' => 5,
                    'Incorrect' => 5,
                ],
            ],
//            [
//                'statuses' => [
//                    'Not answered' => 20,
//                    'Correct' => 0,
//                    'Incorrect' => 0,
//                ],
//            ],
//            [
//                'statuses' => [
//                    'Not answered' => 0,
//                    'Correct' => 0,
//                    'Incorrect' => 20,
//                ],
//            ],
//            [
//                'statuses' => [
//                    'Not answered' => 0,
//                    'Correct' => 20,
//                    'Incorrect' => 0,
//                ],
//            ],
        ];
    }
}
