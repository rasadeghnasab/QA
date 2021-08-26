<?php

namespace Tests\Feature\QACommand;

use App\Enums\PracticeStatusEnum;
use App\Models\Question;
use App\Models\QuestionUser;
use App\StateMachines\Machines\QA\QAStatesEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\QATestCase;

class StatsTest extends QATestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider statsDataProvider
     *
     * @param int $total
     * @param string $answered_percentage
     * @param string $correct_percentage
     * @param array $statuses
     */
    public function test_stats_table_should_be_as_we_expected(
        int $question_count,
        int $total_expected,
        string $answered_percentage,
        string $correct_percentage,
        array $statuses
    ): void {
        Question::factory($question_count)->create();

        foreach ($statuses as $status => $count) {
            QuestionUser::factory($count)->create(['status' => $status]);
        }

        $this->login()
            ->expectsChoice('Choose one option', QAStatesEnum::Stats, QAStatesEnum::mainMenu())
            ->expectsTable(['Title', 'Value'], [
                ['Total', $total_expected],
                ['Answered', $answered_percentage],
                ['Correct', $correct_percentage]
            ]);
    }

    public function statsDataProvider(): array
    {
        return [
            [
                'questions' => 0,
                'total_expected' => 0,
                'answered_percentage' => '%0',
                'correct_percentage' => '%0',
                'statuses' => [
                    PracticeStatusEnum::Correct => 0,
                    PracticeStatusEnum::Incorrect => 0,
                ]
            ],
            [
                'questions' => 10,
                'total_expected' => 10 + 5 + 5,
                'answered_percentage' => '%50',
                'correct_percentage' => '%25',
                'statuses' => [
                    PracticeStatusEnum::Correct => 5,
                    PracticeStatusEnum::Incorrect => 5,
                ]
            ],
            [
                'questions' => 20,
                'total_expected' => 20 + 0 + 0,
                'answered_percentage' => '%0',
                'correct_percentage' => '%0',
                'statuses' => [
                    PracticeStatusEnum::Correct => 0,
                    PracticeStatusEnum::Incorrect => 0,
                ]
            ],
            [
                'questions' => 0,
                'total_expected' => 0 + 0 + 20,
                'answered_percentage' => '%100',
                'correct_percentage' => '%0',
                'statuses' => [
                    PracticeStatusEnum::Correct => 0,
                    PracticeStatusEnum::Incorrect => 20,
                ]
            ],
            [
                'questions' => 0,
                'total_expected' => 0 + 20 + 0,
                'answered_percentage' => '%100',
                'correct_percentage' => '%100',
                'statuses' => [
                    PracticeStatusEnum::NotAnswered => 0,
                    PracticeStatusEnum::Correct => 20,
                    PracticeStatusEnum::Incorrect => 0,
                ]
            ],
        ];
    }
}
