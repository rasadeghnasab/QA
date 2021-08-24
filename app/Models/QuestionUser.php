<?php

namespace App\Models;

use App\Enums\PracticeStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class QuestionUser extends Pivot
{
    use HasFactory;

    protected $fillable = ['question_id', 'user_id', 'status'];

    public function getStatusNameAttribute(): string
    {
        return PracticeStatusEnum::getDescription($this->attributes['status']);
    }
}
