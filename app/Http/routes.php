<?php

use Carbon\Carbon;
use App\Models\Anvandare;
use App\Models\Parkering;
use League\Period\Period;
use App\Models\Parkeringsregel;
use App\Models\Parkeringsomrade;
use App\Models\Specialparkeringsregel;
use App\Models\Specialregler\ForstaTimmenXKr;

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    DB::table('anvandare')->truncate();
    DB::table('parkeringar')->truncate();
    DB::table('parkeringsregler')->truncate();
    DB::table('parkeringsomraden')->truncate();
    DB::table('forsta_timmen_x_kr')->truncate();
    DB::table('annan_taxa_dag_i_vecka')->truncate();
    DB::table('parkeringsomrade_parkeringsregel')->truncate();
    DB::table('specialparkeringsregler')->truncate();
    DB::table('parkeringsomrade_specialparkeringsregel')->truncate();

    $anvandare = Anvandare::create(['namn' => 'Daniel Blomdahl']);

    $pRegel1 = Parkeringsregel::create([
            'start_tid' => '09:00:00',
            'stop_tid' => '18:00:00',
            'taxa' => 5,
            'beskrivning' => '5 kr/timme mellan 09:00 och 15:00',
    ]);
    $pRegel2 = Parkeringsregel::create([
            'start_tid' => '18:00:00',
            'stop_tid' => '00:00:00',
            'taxa' => 0,
            'beskrivning' => '0 kr/timme mellan 18:00 och 00:00',
    ]);
    $pRegel3 = Parkeringsregel::create([
            'start_tid' => '00:00:00',
            'stop_tid' => '09:00:00',
            'taxa' => 0,
            'beskrivning' => '0 kr/timme mellan 00:00 och 09:00',
    ]);

    $parkeringsomrade = Parkeringsomrade::create([
        'namn' => 'Hasses parkering och biltvätt',
        'kod_omrade' => 'HAPB42',
        'max_kostnad_per_dygn' => 25,
    ]);


    $forstaTimmen10Kr = ForstaTimmenXKr::create(['taxa' => 10, 'beskrivning' => 'Första timmen (första timmen som inte är 0 kr/tim): 10 kr/tim, därefter gäller vanliga regler']);

    $specialparkeringsregel1 = Specialparkeringsregel::create([
        'specialregel_id' => $forstaTimmen10Kr->id,
        'specialregel_type' => 'App\Models\Specialregler\ForstaTimmenXKr',
    ]);

    $parkeringsomrade->parkeringsregler()->sync([$pRegel1->id, $pRegel2->id, $pRegel3->id]);

    $parkeringsomrade->specialparkeringsregler()->sync([$specialparkeringsregel1->id]);

    $parkering = Parkering::create([
        'start_tid' => 1455750000+3600*23.25, // ursprunglig timestamp:  2016-02-18 00:00:00
        'anvandare_id' => $anvandare->id,
        'parkeringsomrade_id' => $parkeringsomrade->id,
    ]);

    // Senare i applikationen så avslutas en parkering
    return $parkering->avslutaParkering(1455750000+3600*24);

    $parkering = Parkering::with([
            'parkeringsomrade',
            'parkeringsomrade.parkeringsregler',
            'parkeringsomrade.specialparkeringsregler',
            'parkeringsomrade.specialparkeringsregler.specialregel',
            ])->find(1);
    return $parkering;
    echo "<h1>Klappat och klart</h2>";
    echo '<a href="'.url('/kvitto').'" target="_blank">Gå till kvitto</a>';
    return '';
    });

Route::get('/kvitto', function () {

    // Ännu senare i  applikationen när kostnaden behöver hämtas
    $parkering = Parkering::with([
        'parkeringsomrade.specialparkeringsregler',
        ])->find(1);

    echo "<h1>Parkering mellan ". $parkering->startTidsobjekt() .' - ' .$parkering->stopTidsobjekt() ."</h1>";
    echo "<h1>Kostnad: ".$parkering->kostnad."kr</h1>";
    echo "<hr>";

    echo "<h2>parkeringsregler</h2>";
    foreach ($parkering->kostnad_data['parkeringsregler'] as $datum => $dag) {
        // vanliga parkeringsregler
         echo "<h2>$datum</h2>";
         echo "<h4>Taxa dag: ".$dag['dagensTaxa']. "kr (max ". $parkering->parkeringsomrade->max_kostnad_per_dygn ."kr)</h4>";
         echo "<h4>Total kostnad dag: ".$dag['dagensKostnad']."kr</h4>";
         echo "<h3>Perioder</h3>";
         echo "-----------------------------";
         foreach ($dag['perioder'] as $period) {
            echo "<p>Från: " . $period['start'] . "</p>";
            echo "<p>Till: " . $period['stop'] . "</p>";
            echo "<p>Taxa: ".$period['taxa_total'] ."kr (". $period['tid_timmar'] .'h * '. $period['taxa'] . "kr/tim)</p>";
            if(array_key_exists('beskrivning', $period)){
            echo "<p>beskrivning: ".$period['beskrivning']."</p>";
            }
            echo "-----------------------------<br>";
         }
     }
    return 'blaha';
});
