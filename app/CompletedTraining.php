<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompletedTraining extends Model
{
    protected $table = 'completed_trainings';
    
    protected $fillable = [
        'training_id'
    ];
}
