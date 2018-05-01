<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OhlcValue extends Model
{
    protected $table = 'ohlc_values';
    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo('App\Symbol', 'id');
    }

    
}
