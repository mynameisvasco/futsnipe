<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    public function fifaCard()
    {
        return $this->hasOne('App\FifaCard', 'definition_id', 'definition_id');
    }

    public function transaction()
    {
        return $this->hasMany('App\Transaction', 'definition_id', 'definition_id');
    }
}
