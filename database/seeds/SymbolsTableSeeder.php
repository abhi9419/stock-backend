<?php

use Illuminate\Database\Seeder;

use \App\Symbol;

class SymbolsTableSeeder extends Seeder
{

    private function allowedSeries()
    {
        return array('EQ','SM');
    }


    public function run()
    {
        //reads from excel and add in database change seeder if any update is in schema
        //can also modify the source for seed if better option is available
        //csv made from https://www.nseindia.com/products/content/equities/indices/nifty_200.htm (URL is not accurate but somewhere in this site holds the csv).
        if (($handle = fopen ( public_path () . '/seeds/symbol.csv', 'r' )) !== FALSE) {

            $seq = 0;//skipping the header line

            while ( ($data = fgetcsv ( $handle, 0, ',' )) !== FALSE ) {
                $seq++;
                if($seq===1){
                    continue;
                }
                if(in_array($data[1],self::allowedSeries())) {
                    $symbol = new Symbol ();
                    $symbol->symbol = $data [0];
                    $symbol->series = $data [1];
                    $symbol->isin = $data [2];
                    $symbol->alphavantage_symbol = $data[3];
                    $symbol->save();
                }
            }
            fclose ( $handle );
        }
    }
}
