<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FifaCard extends Model
{
    public function item()
    {
        return $this->belongsTo('App\Item', 'definition_id', 'definition_id');
    }
}
