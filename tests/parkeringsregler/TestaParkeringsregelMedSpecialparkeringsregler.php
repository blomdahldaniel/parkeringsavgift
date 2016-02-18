<?php

use App\Models\Parkering;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TestaParkeringsregelMedSpecialparkeringsregler extends TestCase
{
    use DatabaseTransactions;

    /**
     * Körs inför för samtliga test
     */
    public function setUp()
    {
        parent::setUp();
        $this->skapa_standardparkering_och_anvandare_med_specialregel();
    }

    /**
     * Här kommer test som testar interageringen mellan parkeringsregel och  specialparkeringsregel
     * när perioden är prick 3 timmar lång
     */
    public function test_parkering_3_timmar_gratis_tid()
    {
        $parkering = $this->skapaParkering( 1455750000+3600*20); // ursprunglig timestamp:  2016-02-18 00:00:00

        $parkering->avslutaParkering(1455750000+3600*23); // Lägg till antal timmar (3600*t+sek)

        $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(0, $parkering->kostnad);
    }

    public function test_parkering_3_timmar_tid_med_taxa_mitt_i_regelperiod()
    {        $parkering = $this->skapaParkering( 1455750000+3600*12 ); // ursprunglig timestamp:  2016-02-18 00:00:00

        $parkering->avslutaParkering(1455750000+3600*15); // Lägg till antal timmar (3600*t+sek)
        $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(20, $parkering->kostnad);
    }

    public function test_parkering_3_timmar_borjan_av_regel_period()
    {
        $parkering = $this->skapaParkering( 1455750000+3600*9 ); // ursprunglig timestamp:  2016-02-18 00:00:00

        $parkering->avslutaParkering(1455750000+3600*12); // Lägg till antal timmar (3600*t+sek)

        $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(20, $parkering->kostnad);
    }

    public function test_parkering_3_timmar_slutet_av_regel_period()
    {
       $parkering = $this->skapaParkering( 1455750000+3600*15 ); // ursprunglig timestamp:  2016-02-18 00:00:00

       $parkering->avslutaParkering(1455750000+3600*18); // Lägg till antal timmar (3600*t+sek)

       $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(20, $parkering->kostnad);
    }

    public function test_parkering_3_timmar_slutet_av_dygn()
    {
        $parkering = $this->skapaParkering( 1455750000+3600*21 ); // ursprunglig timestamp:  2016-02-18 00:00:00

        $parkering->avslutaParkering(1455750000+3600*24); // Lägg till antal timmar (3600*t+sek)

        $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(0, $parkering->kostnad);
    }


    /**
     * Här kommer test som testar interageringen mellan parkeringsregel och  specialparkeringsregel
     * när perioden är 2 timmar och 36 minuter
     */
    public function test_parkering_2_timmar_36_minuter_gratis_tid()
    {
        $parkering = $this->skapaParkering( 1455750000+3600*19 ); // ursprunglig timestamp:  2016-02-18 00:00:00

        $parkering->avslutaParkering(1455750000+3600*22.6); // Lägg till antal timmar (3600*t+sek)

        $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(0, $parkering->kostnad);
    }

    public function test_parkering_2_timmar_36_minuter_tid_med_taxa_mitt_i_regelperiod()
    {        $parkering = $this->skapaParkering( 1455750000+3600*10 ); // ursprunglig timestamp:  2016-02-18 00:00:00

        $parkering->avslutaParkering(1455750000+3600*12.6); // Lägg till antal timmar (3600*t+sek)
        $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(10*1+5*1.6, $parkering->kostnad);
    }

    public function test_parkering_2_timmar_36_minuter_borjan_av_regel_period()
    {
        $parkering = $this->skapaParkering( 1455750000+3600*9 ); // ursprunglig timestamp:  2016-02-18 00:00:00

        $parkering->avslutaParkering(1455750000+3600*11.6); // Lägg till antal timmar (3600*t+sek)

        $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(10*1+5*1.6, $parkering->kostnad);
    }

    public function test_parkering_2_timmar_36_minuter_slutet_av_regel_period()
    {
       $parkering = $this->skapaParkering( 1455750000+3600*15.4 ); // ursprunglig timestamp:  2016-02-18 00:00:00

       $parkering->avslutaParkering(1455750000+3600*18); // Lägg till antal timmar (3600*t+sek)

       $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(10*1+5*1.6, $parkering->kostnad);
    }

    public function test_parkering_2_timmar_36_minuter_slutet_av_dygn()
    {
        $parkering = $this->skapaParkering( 1455750000+3600*21.4 ); // ursprunglig timestamp:  2016-02-18 00:00:00

        $parkering->avslutaParkering(1455750000+3600*24); // Lägg till antal timmar (3600*t+sek)

        $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(0, $parkering->kostnad);
    }


    /**
     * Testar överlappning mellan perioder
     */

    public function test_parkering_paserar_byte_av_dygn()
    {
        $parkering = $this->skapaParkering( 1455750000+3600*23.4 ); // ursprunglig timestamp:  2016-02-18 00:00:00

        $parkering->avslutaParkering(1455750000+3600*25); // Lägg till antal timmar (3600*t+sek)

        $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(0, $parkering->kostnad);
    }

    public function test_parkering_1_timma_15_minuter_paserar_byte_av_period_fran_gratis_till_taxa()
    {
        $parkering = $this->skapaParkering( 1455750000+3600*8.25 ); // ursprunglig timestamp:  2016-02-18 00:00:00

        $parkering->avslutaParkering(1455750000+3600*10.5); // Lägg till antal timmar (3600*t+sek)

        $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(8.75, $parkering->kostnad);
    }

    public function test_parkering_1_timma_15_minuter_paserar_byte_av_period_fran_taxa_till_gratis()
    {
        $parkering = $this->skapaParkering( 1455750000+3600*17.25 ); // ursprunglig timestamp:  2016-02-18 00:00:00

        $parkering->avslutaParkering(1455750000+3600*19.5); // Lägg till antal timmar (3600*t+sek)

        $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(7.5, $parkering->kostnad);
    }
}
