<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Anvandare;
use App\Models\Parkering;
use Illuminate\Http\Request;
use App\Models\Parkeringsregel;
use App\Models\Parkeringsomrade;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Specialparkeringsregel;
use App\Models\Specialregler\ForstaTimmenXKr;

class ParkeringsregelController extends Controller
{
    /*
     * Här är de parametrar som hör till det parkeringsområde
     * som byggs upp vid funktionen: skapa_standardparkering_och_anvandare()
     */
    protected $anvandare;
    protected $parkeringsregel1;
    protected $parkeringsregel2;
    protected $parkeringsregel3;
    protected $specialparkeringsregel1;
    protected $parkeringsomrade;
    protected $forstaTimmen10Kr;
    protected $exempelTider;


    public function __construct()
    {
        /**
         * Här kan du lägga till en ny tid om du vill experimentera
         * Den första tidsstämpeln utgår från 2016-02-18 00:00:00
         */
        $this->exempelTider = [
        ['start' => 1455750000+3600*10,'stop' => 1455750000+3600*12],
        ['start' => 1455750000+3600*9 ,'stop' => 1455750000+3600*9.6],
        ['start' => 1455750000+3600*8.75,'stop' => 1455750000+3600*24*402+10*3600],
        ];
    }


    public function index()
    {
        return view('index')->with(['exempelTider' => $this->exempelTider]);

    }

    public function exempel($exempelNr)
    {
        if(array_key_exists($exempelNr, $this->exempelTider)){
            $this->truncateDatabasen();
            $this->skapa_standardparkering_och_anvandare_med_specialregel();
            $parkering = $this->skapaParkering( $this->exempelTider[$exempelNr]['start'] ); // ursprunglig timestamp:  2016-02-18 00:00:00
            $parkering->avslutaParkering($this->exempelTider[$exempelNr]['stop']); // Lägg till antal timmar (3600*t+sek)

            // Ännu senare i  applikationen när kostnaden behöver hämtas
            $parkering = Parkering::find(1);
            return view('kvitto', compact('parkering'));
        }
        else{
            return redirect('/');
        }

    }

    public function skapaParkering($start_tid = 1455750000+3600*0)
    {
        return Parkering::create([
            'start_tid' => $start_tid, // ursprunglig timestamp:  2016-02-18 00:00:00
            'anvandare_id' => $this->anvandare->id,
            'parkeringsomrade_id' => $this->parkeringsomrade->id,
        ]);

    }

    public function skapa_standardparkering_och_anvandare_med_specialregel()
    {
        $this->anvandare = Anvandare::create(['namn' => 'Daniel Blomdahl']);

        $this->parkeringsregel1 = Parkeringsregel::create([
            'start_tid' => '09:00:00',
            'stop_tid' => '18:00:00',
            'taxa' => 5,
            'beskrivning' => '5 kr/timme mellan 09:00 och 15:00',
        ]);
        $this->parkeringsregel2 = Parkeringsregel::create([
                'start_tid' => '18:00:00',
                'stop_tid' => '00:00:00',
                'taxa' => 0,
                'beskrivning' => '0 kr/timme mellan 18:00 och 00:00',
        ]);
        $this->parkeringsregel3 = Parkeringsregel::create([
                'start_tid' => '00:00:00',
                'stop_tid' => '09:00:00',
                'taxa' => 0,
                'beskrivning' => '0 kr/timme mellan 00:00 och 09:00',
        ]);
        $this->parkeringsomrade = Parkeringsomrade::create([
            'namn' => 'Hasses parkering och biltvätt',
            'kod_omrade' => 'HAPB42',
            'max_kostnad_per_dygn' => 25,
        ]);

        $this->parkeringsomrade->parkeringsregler()->sync([
            $this->parkeringsregel1->id,
            $this->parkeringsregel2->id,
            $this->parkeringsregel3->id,
        ]);

        $this->forstaTimmen10Kr = ForstaTimmenXKr::create([
            'taxa' => 10,
            'beskrivning' => 'Första timmen (första timmen som inte är 0 kr/tim): 10 kr/tim, därefter gäller vanliga regler'
        ]);

        $this->specialparkeringsregel1 = Specialparkeringsregel::create([
            'specialregel_id' => $this->forstaTimmen10Kr->id,
            'specialregel_type' => 'App\Models\Specialregler\ForstaTimmenXKr',
        ]);

        $this->parkeringsomrade->specialparkeringsregler()->sync([$this->specialparkeringsregel1->id]);

    }

    public function truncateDatabasen()
    {
        DB::table('anvandare')->truncate();
        DB::table('parkeringar')->truncate();
        DB::table('parkeringsregler')->truncate();
        DB::table('parkeringsomraden')->truncate();
        DB::table('forsta_timmen_x_kr')->truncate();
        DB::table('annan_taxa_dag_i_vecka')->truncate();
        DB::table('parkeringsomrade_parkeringsregel')->truncate();
        DB::table('specialparkeringsregler')->truncate();
        DB::table('parkeringsomrade_specialparkeringsregel')->truncate();
    }
}
