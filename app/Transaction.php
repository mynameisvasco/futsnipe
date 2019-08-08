<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public function account()
    {
        return $this->belongsTo('App\Account');
    }

    public function item()
    {
        return $this->belongsTo('App\Item', 'definition_id', 'definition_id');
    }

    public function fifaCard()
    {
        return $this->belongsTo('App\FifaCard', 'definition_id', 'definition_id');
    }
}
