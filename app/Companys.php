<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Companys extends Model
{
    protected $table = 'company';

    public function trainingCompany(){
        return $this->hasMany('App\Training', 'company_id', 'quizz_id');
    }

    public function quiz(){
        return $this->hasMany('App\Quizzes');
    }

    public function sectors(){
        return $this->hasMany('App\Sector', 'company_id');
    }

    public function categories(){
        return $this->hasMany('App\Category', 'company_id');
    }
    
}
