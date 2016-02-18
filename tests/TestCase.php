<?php
use App\Models\Anvandare;
use App\Models\Parkering;
use App\Models\Parkeringsregel;
use App\Models\Parkeringsomrade;
use App\Models\Specialparkeringsregel;
use App\Models\Specialregler\ForstaTimmenXKr;

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

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

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        setlocale(LC_TIME, "sv_SE");
        date_default_timezone_set('CET');

        return $app;
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
        $this->skapa_standardparkering_och_anvandare();

        $this->forstaTimmen10Kr = ForstaTimmenXKr::create(['taxa' => 10, 'beskrivning' => 'Första timmen (första timmen som inte är 0 kr/tim): 10 kr/tim, därefter gäller vanliga regler']);

        $this->specialparkeringsregel1 = Specialparkeringsregel::create([
            'specialregel_id' => $this->forstaTimmen10Kr->id,
            'specialregel_type' => 'App\Models\Specialregler\ForstaTimmenXKr',
        ]);

        $this->parkeringsomrade->specialparkeringsregler()->sync([$this->specialparkeringsregel1->id]);

    }

    public function skapa_standardparkering_och_anvandare()
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
    }
}
