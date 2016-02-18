<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TestRelationships extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function user_belongs_to_parking_instance()
    {
        $anvandare = Anvandare::create(['namn' => 'Daniel Blomdahl']);

        $parkering = Parkering::create([
            'start_tid' => 1454925600, // 2016-02-08T10:00:00
            'stop_tid' => 1454925600+3600*2, // 2016-02-08T12:00:00
            'anvandare_id' => $anvandare->id,
            'parkeringsomrade_id' => 1,
        ]);

        $parkering
        $this->assertTrue($parkering->taxa, 15);
    }

    public function FunctionName($value='')
    {
        $anvandare = Anvandare::create(['namn' => 'Daniel Blomdahl']);

    $pRegel1 = Parkeringsregel::create([
            'start_tid' => '09:00:00',
            'stop_tid' => '15:00:00',
            'taxa' => 5,
            'beskrivning' => '5 kr/timme mellan 09:00 och 15:00',
    ]);
    $pRegel2 = Parkeringsregel::create([
            'start_tid' => '15:00:00',
            'stop_tid' => '18:00:00',
            'taxa' => 10,
            'beskrivning' => '10 kr/timme mellan 15:00 och 18:00',
    ]);
    $pRegel3 = Parkeringsregel::create([
            'start_tid' => '18:00:00',
            'stop_tid' => '00:00:00',
            'taxa' => 0,
            'beskrivning' => '0 kr/timme mellan 18:00 och 08:00',
    ]);
    $pRegel4 = Parkeringsregel::create([
            'start_tid' => '00:00:00',
            'stop_tid' => '09:00:00',
            'taxa' => 0,
            'beskrivning' => '0 kr/timme mellan 18:00 och 08:00',
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

    $parkeringsomrade->parkeringsregler()->sync([$pRegel1->id, $pRegel2->id, $pRegel3->id, $pRegel4->id]);

    $parkeringsomrade->specialparkeringsregler()->sync([$specialparkeringsregel1->id]);

    $parkering = Parkering::create([
        'start_tid' => 1454925600+3600*0+165, // 2016-02-08T10:00:00
        'stop_tid' => 1454925600+3600*44.6+142, // 2016-02-08T12:00:00
        'anvandare_id' => $anvandare->id,
        'parkeringsomrade_id' => $parkeringsomrade->id,
    ]);
    }
}
