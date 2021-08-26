<?php

use App\Enums\PracticeStatusEnum;

return [

    /*
    |--------------------------------------------------------------------------
    | Enums descriptions
    |--------------------------------------------------------------------------
    |
    */

    PracticeStatusEnum::class => [
        PracticeStatusEnum::NotAnswered => 'Not answered',
        PracticeStatusEnum::Correct => 'Correct',
        PracticeStatusEnum::Incorrect => 'Incorrect',
    ],

];
