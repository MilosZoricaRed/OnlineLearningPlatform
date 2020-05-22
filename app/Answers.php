<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answers extends Model
{
    protected $hidden = [
        'exact'
    ];

    public function question(){
        return $this->belongsTo('App\Question');
    }

    /*public function useranswer(){
        return $this->hasOne('App\User', 'user_answers', 'answers_id', 'question_id', 'user_id');
    }

    public function useran(){
        return $this->hasOne('App\UserAnswers');
    }*/
    
    public function useranswers(){
        return $this->hasMany('App\UserAnswers', 'user_answers');
    }
}
