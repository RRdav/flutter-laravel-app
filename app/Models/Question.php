<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['lesson_id', 'question'];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class);
    }
}
