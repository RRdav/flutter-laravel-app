<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['lesson_id', 'question'];

    public function lesson() {
        return $this->belongsTo(Lesson::class);
    }

    public function questions() {
        return $this->hasMany(Question::class);
    }
}
