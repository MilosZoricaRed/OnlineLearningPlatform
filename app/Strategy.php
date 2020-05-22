<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Strategy extends Model
{
    protected $table = 'strategy';

    public function strategy()
    {
        return $this->hasMany('App\Slides', 'strategy_id', 'id');
    }
}