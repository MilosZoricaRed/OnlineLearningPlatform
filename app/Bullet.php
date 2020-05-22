<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bullet extends Model
{
    protected $table = 'bullets';
    

    protected $fillable = [
        'tekst_bullet'
    ];

    public function bulletsTraining(){
        return $this->belongsTo('App\Training');
    }
}
