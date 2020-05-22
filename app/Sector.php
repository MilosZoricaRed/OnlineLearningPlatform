<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    protected $table = 'sector';

    public function company(){
        return $this->belongsTo('App\Companys');
    }
}
