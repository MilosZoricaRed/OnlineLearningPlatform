<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Intro extends Model
{
    protected $table = 'intro';

    public function intro()
    {
        return $this->hasMany('App\Slides', 'id', 'intro_id');
    }
}
