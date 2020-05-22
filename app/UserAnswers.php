<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAnswers extends Model
{
    //public $timestamps = false;
    protected $table = 'user_answers';

    protected $fillable = [
         'answers_id', 'question_id', 'user_response', 'user_id'
     ];

     public function answer(){
         return $this->belongsTo('App\Answers', 'id');
     }

     public function user(){
         return $this->belongsTo('App\User', 'user_id');
     }

     public function useransweredonquestion(){
         return $this->hasOne('App\Question');
     }
}
