<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    public function transaction()
    {
        return $this->hasMany('App\Transaction');
    }
}
