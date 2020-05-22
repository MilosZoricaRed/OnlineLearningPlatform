<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeTraining extends Model
{
    protected $table = 'user_time_training';

    protected $primaryKey = 'id';

    public function trainingTime()
    {
        return $this->belongsTo('App\Training', 'training_id', 'id');
    }
}
