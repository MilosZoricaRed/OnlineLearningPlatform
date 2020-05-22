<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'question';

    public function quizz(){
        return $this->belongsTo('App\Quizzes');
    }

    public function answers(){
        return $this->hasMany('App\Answers');
    }

    public function useransweredonquestion(){
        return $this->hasMany('App\UserAnswers');
    }

    public function questionAnswers(){
        return $this->hasMany('App\Answers');
    }
}
