<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public function account()
    {
        return $this->belongsTo('App\Account');
    }
}
