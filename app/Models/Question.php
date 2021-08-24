<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['body', 'answer'];

    public function scopeCorrect(Builder $query)
    {
        return $query->where('Status', '!=', 'Correct');
    }

    public function scopeIncorrect(Builder $query)
    {
        return $query->where('Status', '!=', 'Incorrect');
    }

    public function scopeNotAnswered(Builder $query)
    {
        return $query->where('Status', '!=', 'Not Answered');
    }
}
