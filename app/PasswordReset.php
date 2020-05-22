<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $table='password_resets';

    // protected $primaryKey = 'email';
    public $timestamps = false;
    protected $fillable = [
        'email', 'token'
    ];
}
