<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Likes extends Model
{
    protected $table = 'likes';
    
    protected $fillable = [
        'training_id', 'likes'
    ];

    public function likesTraining(){
        return $this->belongsTo('App\Training');
    }

}
