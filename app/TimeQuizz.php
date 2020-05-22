<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeQuizz extends Model
{
    protected $table = 'user_time_quizz';

    protected $primaryKey = 'id';

    public function quizzTime(){
        return $this->hasOne('App\Quizzes', 'quizz_id', 'id');
    }

}
