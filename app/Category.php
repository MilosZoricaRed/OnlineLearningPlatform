<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'category';
    protected $fillable = [
        'name'
    ];

    public function category()
    {
        return $this->hasMany('App\TypeTraining');
    }

    public function typeTraining()
    {
        return $this->hasMany('App\TypeTraining');
    }

    public function typeQuizz(){
        return $this->hasMany('App\TypeTraining');
    }

    public function training(){
        return $this->hasMany('App\Training', 'type_id', 'category_id');
    }
}
