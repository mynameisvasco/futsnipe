<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    public function account()
    {
        return $this->belongsTo('App\User');
    }
}
