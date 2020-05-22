<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function validateForPassportPasswordGrant($password)
    {
        //check for password
        if (Hash::check($password, $this->getAuthPassword())) {
            //is user active?
            if ($this->active == 1) {
                return true;
            }
        }
    }

    public function completedTraining()
    {
        return $this->belongsToMany('App\Training', 'completed_trainings', 'user_id', 'training_id');
    }

    public function completedQuiz()
    {
        return $this->belongsToMany('App\Quizzes', 'completed_quizzes', 'user_id', 'quizz_id');
    }

    public function answered()
    {
        return $this->belongsToMany('App\Answers', 'user_answers', 'user_id', 'answers_id');
    }

    public function completedSlides()
    {
        return $this->belongsToMany('App\Slides', 'completed_slides', 'user_id', 'slides_id');
    }

    public function answers()
    {
        return $this->hasMany('App\UserAnswers', 'user_answers');
    }

    public function userMessages()
    {
        return $this->hasMany('App\Messages');
    }

    public function userTimeTraining()
    {
        return $this->hasMany('App\TimeTraining', 'user_time_training', 'user_id', 'user_time_training_id');
    }
}
