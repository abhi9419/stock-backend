<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OhlcValue extends Model
{
    protected $table = 'ohlc_values';
    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo('App\Symbol', 'id');
    }


    public function getCurrentValue($symbol)
    {


        $queryResult = DB::select(DB::raw("
                    SELECT ohlc.open,ohlc.high,ohlc.low,ohlc.close,ohlc.volume,ohlc.timestamp 
                    from ohlc_values ohlc join symbols s on s.id = ohlc.symbol_id where s.symbol = '$symbol'
                    order by ohlc.timestamp desc limit 1
        "));


        return $queryResult;


    }
    public function getSecondLastValue($symbol)
    {


        $queryResult = DB::select(DB::raw("
                    SELECT ohlc.open,ohlc.high,ohlc.low,ohlc.close,ohlc.volume,ohlc.timestamp 
                    from ohlc_values ohlc join symbols s on s.id = ohlc.symbol_id where s.symbol = '$symbol'
                    order by ohlc.timestamp desc limit 1,1
        "));


        return $queryResult;


    }

    public function getAllValue($symbol)
    {


        $queryResult = DB::select(DB::raw("
                    SELECT ohlc.open,ohlc.high,ohlc.low,ohlc.close,ohlc.volume,ohlc.timestamp 
                    from ohlc_values ohlc join symbols s on s.id = ohlc.symbol_id where s.symbol = '$symbol'
                    order by ohlc.timestamp asc
        "));


        return $queryResult;


    }



}
