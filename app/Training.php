<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $table = 'training';

    public function training()
    {
        return $this->hasMany('App\Slides', 'id', 'trainings_id');
    }

    public function company()
    {
        return $this->belongsTo('App\Companys');
    }

    public function quizzes()
    {
        return $this->hasMany('App\Quizzes');
    }

    public function trainingType()
    {
        return $this->belongsTo('App\TypeTraining', 'type_id', 'id');
    }

    public function trainingLikes(){
        return $this->hasMany('App\Likes');
    }

    public function trainingBullets(){
        return $this->hasMany('App\Bullet');
    }

    public function trainingCompleted(){
        return $this->belongsToMany('App\User', 'completed_trainings');
    }

    public function trainingTime()
    {
        return $this->hasMany('App\TimeTraining', 'user_time_training_id', 'training_id', 'id');
    }
}
