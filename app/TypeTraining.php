<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypeTraining extends Model
{
    protected $table = "type_training";

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function training()
    {
        return $this->hasMany('App\Training', 'training', 'training_id', 'type_id');
    }
}
