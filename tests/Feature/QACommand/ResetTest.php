<?php

namespace Tests\Feature\QACommand;

use App\Models\Question;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\QATestCase;

class ResetTest extends QATestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider statsDataProvider
     *
     * @param string $confirm
     * @param array $statuses
     * @param int $not_answered_expected
     */
    public function test_confirmed_reset_should_change_all_questions_status_to_not_answered(string $confirm, array $statuses, int $not_answered_expected): void
    {
        foreach ($statuses as $status => $count) {
            Question::factory($count)->create(['user_id' => $this->user->id, 'status' => $status]);
        }

        $this->login()
            ->expectsChoice('Choose one option', QAStatesEnum::Reset, QAStatesEnum::mainMenu())
            ->expectsConfirmation('Are you sure? (You can not undo this action)', $confirm);

        $actual = $this->user->questions()->where('status', 'Not answered')->count();

        $this->assertEquals($not_answered_expected, $actual);
    }

    public function statsDataProvider(): array
    {
        return [
            [
                'confirm' => 'yes',
                'statuses' => [
                    'Not answered' => 10,
                    'Correct' => 5,
                    'Incorrect' => 5,
                ],
                'not_answered_expected' => 20,
            ],
            [
                'confirm' => 'yes',
                'statuses' => [
                    'Not answered' => 20,
                    'Correct' => 0,
                    'Incorrect' => 0,
                ],
                'not_answered_expected' => 20,
            ],
            [
                'confirm' => 'yes',
                'statuses' => [
                    'Not answered' => 0,
                    'Correct' => 0,
                    'Incorrect' => 20,
                ],
                'not_answered_expected' => 20,
            ],
            [
                'confirm' => 'yes',
                'statuses' => [
                    'Not answered' => 0,
                    'Correct' => 20,
                    'Incorrect' => 0,
                ],
                'not_answered_expected' => 20,
            ],
            // No
            [
                'confirm' => 'no',
                'statuses' => [
                    'Not answered' => 10,
                    'Correct' => 5,
                    'Incorrect' => 5,
                ],
                'not_answered_expected' => 10,
            ],
            [
                'confirm' => 'no',
                'statuses' => [
                    'Not answered' => 20,
                    'Correct' => 0,
                    'Incorrect' => 0,
                ],
                'not_answered_expected' => 20,
            ],
            [
                'confirm' => 'no',
                'statuses' => [
                    'Not answered' => 0,
                    'Correct' => 0,
                    'Incorrect' => 20,
                ],
                'not_answered_expected' => 0,
            ],
            [
                'confirm' => 'no',
                'statuses' => [
                    'Not answered' => 0,
                    'Correct' => 20,
                    'Incorrect' => 0,
                ],
                'not_answered_expected' => 0,
            ],
        ];
    }
}
