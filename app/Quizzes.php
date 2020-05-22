<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quizzes extends Model
{

    protected $table = 'quizzes';

    public function training(){
        return $this->belongsTo('App\Training');
    }

    public function questions(){
        return $this->hasMany('App\Question');
    }

    public function quizzes(){
        return $this->belongsTo('App\Quizzes');
    }

    public function company(){
        return $this->belongsTo('App\Companys');
    }

    public function completedQuiz(){
        return $this->belongsToMany('App\User', 'completed_quizzes', 'quizz_id', 'user_id');
    }

    public function quizzType()
    {
        return $this->belongsTo('App\TypeTraining', 'type_id', 'id');
    }
}
