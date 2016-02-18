<?php

use App\Models\Parkering;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TestaParkeringsregel extends TestCase
{
    use DatabaseTransactions;

    /**
     * Körs inför för samtliga test
     */
    public function setUp()
    {
        parent::setUp();
        $this->skapa_standardparkering_och_anvandare();
    }

    /**
     * Här kommer test som testar interageringen för en specialregel
     * när perioden är prick 1 timma lång
     */
    public function test_parkering_1_timma_gratis_tid()
    {
        $parkering = $this->skapaParkering( 1455750000+3600*20); // ursprunglig timestamp:  2016-02-18 00:00:00

        $parkering->avslutaParkering(1455750000+3600*21); // Lägg till antal timmar (3600*t+sek)

        $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(0, $parkering->kostnad);
    }

    public function test_parkering_1_timma_tid_med_taxa_mitt_i_regelperiod()
    {        $parkering = $this->skapaParkering( 1455750000+3600*12 ); // ursprunglig timestamp:  2016-02-18 00:00:00

        $parkering->avslutaParkering(1455750000+3600*13); // Lägg till antal timmar (3600*t+sek)
        $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(5 , $parkering->kostnad);
    }

    public function test_parkering_1_timma_borjan_av_regel_period()
    {
        $parkering = $this->skapaParkering( 1455750000+3600*9 ); // ursprunglig timestamp:  2016-02-18 00:00:00

        $parkering->avslutaParkering(1455750000+3600*10); // Lägg till antal timmar (3600*t+sek)

        $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(5 , $parkering->kostnad);
    }

    public function test_parkering_1_timma_slutet_av_regel_period()
    {
       $parkering = $this->skapaParkering( 1455750000+3600*17 ); // ursprunglig timestamp:  2016-02-18 00:00:00

       $parkering->avslutaParkering(1455750000+3600*18); // Lägg till antal timmar (3600*t+sek)

       $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(5 , $parkering->kostnad);
    }

    public function test_parkering_1_timma_slutet_av_dygn()
    {
        $parkering = $this->skapaParkering( 1455750000+3600*23 ); // ursprunglig timestamp:  2016-02-18 00:00:00

        $parkering->avslutaParkering(1455750000+3600*24); // Lägg till antal timmar (3600*t+sek)

        $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(0, $parkering->kostnad);
    }

    // Testar överlappning mellan perioder
    public function test_parkering_1_timma_paserar_byte_av_dygn()
    {
        $parkering = $this->skapaParkering( 1455750000+3600*23.5 ); // ursprunglig timestamp:  2016-02-18 00:00:00

        $parkering->avslutaParkering(1455750000+3600*24.5); // Lägg till antal timmar (3600*t+sek)

        $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(0, $parkering->kostnad);
    }

    public function test_parkering_1_timma_paserar_byte_av_period_fran_gratis_till_taxa()
    {
        $parkering = $this->skapaParkering( 1455750000+3600*8.5 ); // ursprunglig timestamp:  2016-02-18 00:00:00

        $parkering->avslutaParkering(1455750000+3600*9.5); // Lägg till antal timmar (3600*t+sek)

        $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(5*0.5 , $parkering->kostnad);
    }

    public function test_parkering_1_timma_paserar_byte_av_period_fran_taxa_till_gratis()
    {
        $parkering = $this->skapaParkering( 1455750000+3600*17.5 ); // ursprunglig timestamp:  2016-02-18 00:00:00

        $parkering->avslutaParkering(1455750000+3600*18.5); // Lägg till antal timmar (3600*t+sek)

        $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(5*0.5, $parkering->kostnad);
    }



    /**
     * Här kommer test som testar interageringen för en specialregel
     * när perioden är kortare än en timme
     */
    public function test_parkering_36_minuter_gratis_tid()
    {
        $parkering = $this->skapaParkering( 1455750000+3600*20 ); // ursprunglig timestamp:  2016-02-18 00:00:00

        $parkering->avslutaParkering(1455750000+3600*20.6); // Lägg till antal timmar (3600*t+sek)

        $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(0, $parkering->kostnad);
    }

    public function test_parkering_36_minuter_tid_med_taxa_mitt_i_regelperiod()
    {        $parkering = $this->skapaParkering( 1455750000+3600*12 ); // ursprunglig timestamp:  2016-02-18 00:00:00

        $parkering->avslutaParkering(1455750000+3600*12.6); // Lägg till antal timmar (3600*t+sek)
        $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(5 *0.6, $parkering->kostnad);
    }

    public function test_parkering_36_minuter_borjan_av_regel_period()
    {
        $parkering = $this->skapaParkering( 1455750000+3600*9 ); // ursprunglig timestamp:  2016-02-18 00:00:00

        $parkering->avslutaParkering(1455750000+3600*9.6); // Lägg till antal timmar (3600*t+sek)

        $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(5  * 0.6, $parkering->kostnad);
    }

    public function test_parkering_36_minuter_slutet_av_regel_period()
    {
       $parkering = $this->skapaParkering( 1455750000+3600*17.4 ); // ursprunglig timestamp:  2016-02-18 00:00:00

       $parkering->avslutaParkering(1455750000+3600*18); // Lägg till antal timmar (3600*t+sek)

       $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(5  * 0.6, $parkering->kostnad);
    }

    public function test_parkering_36_minuter_slutet_av_dygn()
    {
        $parkering = $this->skapaParkering( 1455750000+3600*23.4 ); // ursprunglig timestamp:  2016-02-18 00:00:00

        $parkering->avslutaParkering(1455750000+3600*24); // Lägg till antal timmar (3600*t+sek)

        $parkering = Parkering::findOrFail($parkering->id);

        $this->assertEquals(0, $parkering->kostnad);
    }

}
