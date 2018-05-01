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


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
