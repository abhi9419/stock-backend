<?php


namespace App\Api;

use GuzzleHttp\Client;

class AlphaVantage
{


    private static $key;

    function __construct()
    {
        self::$key = env('ALPHAVANTAGE_KEY');
    }

    public function daily($symbol, $dataType = 'csv', $outputSize = 'compact')
    {
        try {
            $client = new Client();
            $uri = 'https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol=' . $symbol . '&outputsize=' . $outputSize . '&datatype=' . $dataType . '&apikey=' . self::getKey();
            $result = $client->get($uri);

            if ($dataType === "json") {
                return $result->getBody()->getContents();
            } elseif ($dataType === "csv") {
                return $result->getBody()->getContents();
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    private static function getKey()
    {
        return self::$key;
    }


}


?>