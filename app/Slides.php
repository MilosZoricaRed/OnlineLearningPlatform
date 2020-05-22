<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Slides extends Model
{
    protected $table = 'slides';

    protected $fillable = array('title', 'tekst', 'photo_src', 'video_src', 'audio_src', 'training_id');

    public function training()
    {
        return $this->belongsTo('App\Training', 'training_id', 'id');
    }

    public function intro()
    {
        return $this->belongsTo('App\Intro', 'intro_id', 'id');
    }

    public function strategy()
    {
        return $this->belongsTo('App\Strategy', 'strategy_id', 'id');
    }
}
