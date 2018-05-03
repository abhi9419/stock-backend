<?php

namespace App\Http\Controllers;

use App\Api\AlphaVantage;
use App\OhlcValue;
use App\Symbol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OhlcValueController extends Controller
{

    private static $source;

    function __construct()
    {
        self::$source = env('OHLC_SOURCE');
    }


    public function current($symbol){

        $ohlc = new OhlcValue();
        $currentValues = $ohlc->getCurrentValue($symbol);

        if(!empty($currentValues)){

            return response()->json(['status' => 'success', 'data' => $currentValues[0]]);

        }

        return response()->json(['status' => 'error', 'message' => 'No Data']);


    }

    public function recent2($symbol){

        $ohlc = new OhlcValue();
        $currentValues = $ohlc->getCurrentValue($symbol);
        $secondLastValue = $ohlc->getSecondLastValue($symbol);

        if(!empty($currentValues)){

            return response()->json(['status' => 'success', 'data' => array('0'=>$currentValues[0],'1'=>$secondLastValue[0])]);

        }

        return response()->json(['status' => 'error', 'message' => 'No Data']);


    }

    public function create($symbol)
    {
        $symbolColumnName = '';
        $apiClass = null;

        if (self::$source === "ALPHAVANTAGE") {
            $apiClass = new AlphaVantage();
            $symbolColumnName = "alphavantage_symbol";
        } else {
            return response()->json(['status' => 'error', 'message' => 'Source Invalid for fetching data']);
        }

        $symbolMeta = Symbol::select(["$symbolColumnName as api_symbol", "id"])->where("symbol", $symbol)->first();


        if (empty($symbolMeta)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid Column Mapping for Source']);
        }

        $symbolName = $symbolMeta['api_symbol'];
        $symbolId = $symbolMeta['id'];

        $symbolData = $apiClass->daily($symbolName, "csv", "compact");

        $symbolData = $this->processDailyApiData($symbolData);

        if ($symbolData === false) {
            return response()->json(['status' => 'error', 'message' => 'Cannot process API Data.']);
        }

        foreach ($symbolData as $insertData) {
            OhlcValue::updateOrCreate(
                [
                    'timestamp' => $insertData['timestamp'],
                    'symbol_id' => $symbolId
                ],
                [
                    'open' => $insertData['open'],
                    'high' => $insertData['high'],
                    'low' => $insertData['low'],
                    'close' => $insertData['close'],
                    'volume' => $insertData['volume']
                ]);
        }


        return response()->json(['status' => 'success', 'message' => 'Data fetched from API']);


    }

    function processDailyApiData($data)
    {

        try {
            if (self::$source === "ALPHAVANTAGE") {

                $data = explode("\r\n", $data);
                $dataHeader = explode(",", $data[0]);
                unset($data[0]);

                $newData = array();

                foreach ($data as $index => $ohlcData) {
                    $ohlcData = explode(",", $ohlcData);
                    foreach ($dataHeader as $key => $value) {
                        if (array_key_exists($key, $ohlcData) && !empty($ohlcData[$key])) {
                            $newData[$index - 1][$value] = $ohlcData[$key];
                        } else {
                            break;
                        }
                    }
                }

                return $newData;

            } else {
                return false;
            }
        }catch (\Exception $e){
            return false;
        }

    }

    function getOhlcDataForGraph($symbol){

        $ohlc = new OhlcValue();
        $currentValues = $ohlc->getAllValue($symbol);

        if(!empty($currentValues)){


            $open = array("key"=>"Open","values"=>array());
            $high = ["key"=>"High","values"=>[]];
            $low = ["key"=>"Low","values"=>[]];
            $close = ["key"=>"Close","values"=>[]];

            foreach ($currentValues as $cv){

                $cv->timestamp = strtotime($cv->timestamp);

                array_push($open['values'],array($cv->timestamp,$cv->open));
                array_push($high['values'],array($cv->timestamp,$cv->high));
                array_push($low['values'],array($cv->timestamp,$cv->low));
                array_push($close['values'],array($cv->timestamp,$cv->close));

            }


            return response()->json(['status' => 'success', 'data' => array($open,$high,$low,$close)]);

        }

        return response()->json(['status' => 'error', 'message' => 'No Data']);

    }



}
