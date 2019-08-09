<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Consumable extends Model
{
    public function fifaCard()
    {
        return $this->hasOne('App\FifaCard', 'resourceId', 'definition_id');
    }
}
