<?php

namespace App\Models;

use App\Enums\PracticeStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class QuestionUser extends Pivot
{
    use HasFactory;

    protected $fillable = ['question_id', 'user_id', 'status'];

    protected $casts = [
        'status' => 'integer',
    ];

    public function getStatusNameAttribute(): string
    {
        return PracticeStatusEnum::getDescription($this->attributes['status']);
    }
}
