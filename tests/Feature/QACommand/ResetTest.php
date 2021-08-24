<?php

namespace Tests\Feature\QACommand;

use App\Enums\PracticeStatusEnum;
use App\Models\QuestionUser;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\QATestCase;

class ResetTest extends QATestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider resetDataProvider
     *
     * @param string $confirm
     * @param array $statuses
     * @param int $answered_expected
     */
    public function test_confirmed_reset_should_change_all_questions_status_to_not_answered(
        string $confirm,
        array $statuses,
        int $answered_expected
    ): void {
        foreach ($statuses as $status => $count) {
            QuestionUser::factory($count)->create(['user_id' => $this->user->id, 'status' => $status]);
        }

        $actual = $this->user->practices()->count();

        $this->assertEquals($statuses[PracticeStatusEnum::Correct] + $statuses[PracticeStatusEnum::Incorrect], $actual);

        $this->login()
            ->expectsChoice('Choose one option', QAStatesEnum::Reset, QAStatesEnum::mainMenu())
            ->expectsConfirmation('Are you sure? (You can not undo this action)', $confirm);

        $actual = $this->user->practices()->count();

        $this->assertEquals($answered_expected, $actual);
    }

    public function resetDataProvider(): array
    {
        return [
            [
                'confirm' => 'yes',
                'statuses' => [
                    PracticeStatusEnum::Correct => 5,
                    PracticeStatusEnum::Incorrect => 5,
                ],
                'answered_expected' => 0,
            ],
            [
                'confirm' => 'yes',
                'statuses' => [
                    PracticeStatusEnum::Correct => 0,
                    PracticeStatusEnum::Incorrect => 0,
                ],
                'answered_expected' => 0,
            ],
            [
                'confirm' => 'yes',
                'statuses' => [
                    PracticeStatusEnum::Correct => 0,
                    PracticeStatusEnum::Incorrect => 20,
                ],
                'answered_expected' => 0,
            ],
            [
                'confirm' => 'yes',
                'statuses' => [
                    PracticeStatusEnum::Correct => 20,
                    PracticeStatusEnum::Incorrect => 0,
                ],
                'not_answered_expected' => 0,
            ],
            // No
            [
                'confirm' => 'no',
                'statuses' => [
                    PracticeStatusEnum::Correct => 5,
                    PracticeStatusEnum::Incorrect => 5,
                ],
                'answered_expected' => 10,
            ],
            [
                'confirm' => 'no',
                'statuses' => [
                    PracticeStatusEnum::Correct => 0,
                    PracticeStatusEnum::Incorrect => 0,
                ],
                'answered_expected' => 0,
            ],
            [
                'confirm' => 'no',
                'statuses' => [
                    PracticeStatusEnum::Correct => 0,
                    PracticeStatusEnum::Incorrect => 20,
                ],
                'answered_expected' => 20,
            ],
            [
                'confirm' => 'no',
                'statuses' => [
                    PracticeStatusEnum::Correct => 20,
                    PracticeStatusEnum::Incorrect => 0,
                ],
                'answered_expected' => 20,
            ],
        ];
    }
}
