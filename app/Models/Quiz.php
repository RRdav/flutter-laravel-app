<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = ['title', 'type'];

    public function questions()
    {
        return $this->belongsToMany(Question::class);
    }
}
