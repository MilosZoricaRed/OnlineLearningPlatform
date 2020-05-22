<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompletedSlides extends Model
{
    protected $table = 'completed_slides';
    
    protected $fillable = [
        'slides_id'
    ];

    public function intro()
    {
        return $this->hasMany('App\Slides', 'id', 'intro_id');
    }
    public function strategy()
    {
        return $this->hasMany('App\Slides', 'id', 'strategy_id');
    }
    public function training()
    {
        return $this->hasMany('App\Slides', 'id', 'training_id');
    }
}
