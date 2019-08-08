<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    public function transaction()
    {
        return $this->hasMany('App\Transaction');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
