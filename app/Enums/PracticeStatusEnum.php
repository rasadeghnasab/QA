<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class PracticeStatusEnum extends Enum implements LocalizedEnum
{
    const NotAnswered = 0;
    const Correct = 1;
    const Incorrect = 2;
}
