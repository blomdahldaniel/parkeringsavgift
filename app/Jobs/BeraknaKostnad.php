<?php

namespace App\Jobs;

use App\Jobs\Job;
use Carbon\Carbon;
use App\Models\Parkering;
use League\Period\Period;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class BeraknaKostnad extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $parkering;
    public $tempLoopDate;
    public $dagensTaxa;
    public $dagensKostnad;
    public $kostnadTotalt;
    public $kostnad_data;
    public $kostnad_data_perioder_array;
    public $parkeringAvslutadFranSpecial;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Parkering $parkering)
    {
        $this->parkering = $parkering;
        $this->tempLoopDate = $this->parkering->startTidsobjekt();
        $this->dagensTaxa = 0;
        $this->dagensKostnad = 0;
        $this->kostnadTotalt = 0;
        $this->kostnad_data = array();
        $this->kostnad_data_perioder_array = array();
        $this->parkeringAvslutadFranSpecial = false;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /**
         * Loopar igenom parkeringsregler dag för dag
         */
        while($this->tempLoopDate->lt( $this->parkering->stopTidsobjekt() )){
            foreach ($this->parkering->parkeringsregler() as $regel) {
                $aktivParkeringsperiodAvslutadFranSpecial = false;
                // bygg upp tidsobjekt från aktiv regel
                $aktivDagRegelStart = new Carbon($this->tempLoopDate->format('Y-m-d') .' '. $regel->start_tid);
                $aktivDagRegelStop = new Carbon($this->tempLoopDate->format('Y-m-d') .' '. $regel->stop_tid);

                // Vid sista perioden på dygnet, sätt övergång till nästa dag
                if( $regel->stop_tid == "00:00:00" ){
                    // se till så att tiden med 00:00 hamnar för nästa dygn
                    $this->tempLoopDate->addDay()->startOfDay();
                    // Skriv över tidigare instans
                    $aktivDagRegelStop = new Carbon($this->tempLoopDate->format('Y-m-d') .' '. $regel->stop_tid);
                }

                // Initiera periodsobjekt för aktiv regel
                $regelPeiod = new Period($aktivDagRegelStart, $aktivDagRegelStop);
                $parkeringsPeriod = new Period($this->parkering->startTidsobjekt(), $this->parkering->stopTidsobjekt());

                /**
                 * Endast de perioder då parkering skedde skall beräknas.
                 * Det gäller när när parkering- och regelperioder sammanfaller
                 * Då:
                 * Regelns sluttid är > parkeringens starttid && Regelns starttid är < parkeringens sluttid.
                 * Alltså när perioderna överlappar eller tangerar
                 *
                 * Regel:       |_____|              |_____|              |_____|
                 * Parkering:      |_________|     |_________|     |_________|
                 */
                if(
                    Carbon::parse($regelPeiod->getEndDate()->format('Y-m-d H:i:s'))->gt( $this->parkering->startTidsobjekt() )
                    &&
                    Carbon::parse($regelPeiod->getStartDate()->format('Y-m-d H:i:s'))->lt( $this->parkering->stopTidsobjekt() )
                    )
                {
                    // Här sker magin
                    // Initiera periodsobjekt för den tid som parkerats
                    $aktivParkeringsPeriod = $regelPeiod->intersect($parkeringsPeriod);

                    $aktivParkeringsPeriodSekunder = $aktivParkeringsPeriod->getTimestampInterval();
                    // Här implementeras specialregler
                    // hämtar beräkningar
                    foreach ($this->parkering->specialparkeringsregler() as $specialregel) {
                        $specialParkeringData = $specialregel->specialregel->beraknaTaxa($this->parkering, $regel, $aktivParkeringsPeriod, $aktivDagRegelStop);
                        // om specialregeln har varit aktiv
                        if($specialParkeringData != null){
                            // Sparar till data_perioder_array
                            $this->kostnad_data_perioder_array = array_merge($this->kostnad_data_perioder_array, $specialParkeringData['kostnad_data']);

                            // adderar till dagens taxa
                            $this->dagensTaxa += $specialParkeringData['kostnad'];
                            if( isset($specialParkeringData['aktivParkeringsPeriod']) ){
                                $aktivParkeringsPeriod = $specialParkeringData['aktivParkeringsPeriod'];
                            }
                        }
                    }
                    /* ------ Slut på loop för specialregler ------*/

                    // Om inte parkeringen har avslutats från special
                    if(!isset($specialParkeringData['parkeringAvslutadFranSpecial']) ){

                        if (!isset($specialParkeringData['aktivParkeringsperiodAvslutadFranSpecial'])){
                            // Spara data för dagens aktuella period
                            $this->kostnad_data_perioder_array[] = array(
                                'start' => $aktivParkeringsPeriod->getStartDate()->format('Y-m-d H:i:s'),
                                'stop' => $aktivParkeringsPeriod->getEndDate()->format('Y-m-d H:i:s'),
                                'tid_sekunder' => $aktivParkeringsPeriod->getTimestampInterval(),
                                'tid_timmar' => ($aktivParkeringsPeriod->getTimestampInterval() /3600),
                                'taxa' => $regel->taxa,
                                'taxa_total' => $regel->taxa * ($aktivParkeringsPeriod->getTimestampInterval()/3600),
                                'beskrivning' => $regel->beskrivning,
                            );
                        }
                        // om det inte är sista perioden på dygnet
                        // Alltså, när det ska fortsätta loopas till nästa regel så sparas den aktiva parkeringsperiodens data innan
                        if($aktivParkeringsPeriod->getEndDate()->format('H:i:s') != '00:00:00' && !isset($specialParkeringData['aktivParkeringsperiodAvslutadFranSpecial'])){
                            $this->dagensTaxa += $regel->taxa * ($aktivParkeringsPeriod->getTimestampInterval()/3600);
                            $this->setDagensKostnadData( $aktivParkeringsPeriod->getStartDate()->format('Y-m-d') );
                        }

                        // Om parkeringen inte är avslutad från special
                        // Och om den aktiva periodens sluttid är 00:00:00
                        // Och om
                        // Här sammanställs dagens data för sista pedioden på dygnet
                        if( $aktivParkeringsPeriod->getEndDate()->format('H:i:s') == '00:00:00'
                            &&
                            !isset($specialParkeringData['aktivParkeringsperiodAvslutadFranSpecial'])
                            &&
                            !Carbon::parse($aktivParkeringsPeriod->getEndDate()->format('Y-m-d H:i:s'))->eq($this->parkering->stopTidsobjekt() )
                            ){
                            $this->dagensKostnad = $this->bestamDagensKostnad();
                            $this->parkering->kostnad += $this->dagensKostnad;

                            $this->setDagensKostnadData( $aktivParkeringsPeriod->getStartDate()->format('Y-m-d') );

                            // om det INTE är sista perioden för parkeringen så nollställs dagsbunden data
                            if(!Carbon::parse($aktivParkeringsPeriod->getEndDate()->format('Y-m-d H:i:s'))->eq($this->parkering->stopTidsobjekt() )){
                                // Nollställ dagens taxa då detta var sista perioden för aktuell dag
                                $this->dagensKostnad = 0;
                                $this->dagensTaxa = 0;
                                $this->kostnad_data_perioder_array = array();
                            }
                        }
                    }
                    // om det är sista perioden för hela parkeringen
                    if(isset($specialParkeringData['parkeringAvslutadFranSpecial']) || Carbon::parse($aktivParkeringsPeriod->getEndDate()->format('Y-m-d H:i:s'))->eq($this->parkering->stopTidsobjekt() )){
                        $this->setDagensKostnadData( $aktivParkeringsPeriod->getStartDate()->format('Y-m-d') );
                        $this->lagraSlutligaVarden($aktivParkeringsPeriod);
                        $this->parkering->kostnad += $this->dagensKostnad;
                        $this->parkering->save();
                        return $this->parkering;
                    }
                }
            }
        }
    }

    public function lagraSlutligaVarden($aktivParkeringsPeriod)
    {
        $this->dagensKostnad = $this->bestamDagensKostnad();
        // Lagra sista dagens data till kostnaden.
        $this->setDagensKostnadData( $aktivParkeringsPeriod->getStartDate()->format('Y-m-d') );

        $this->parkering->kostnad_data = $this->kostnad_data;
        $this->parkering->save();
    }

    /**
     * Ser om det finns max-belopp för en dag.
     * Gör det det så anpassa dagens datum efter max-belopp annars returneras dagens taxa
     * @return [int] [summerad taxa för dagen]
     */
    public function bestamDagensKostnad()
    {
        // Om det inte finns angiven max-kostnad per dygn
        if($this->parkering->parkeringsomrade->max_kostnad_per_dygn == null){
            return $this->dagensTaxa;
        }

        if($this->dagensTaxa < $this->parkering->parkeringsomrade->max_kostnad_per_dygn){
            return $this->dagensTaxa;
        }else{
            return $this->parkering->parkeringsomrade->max_kostnad_per_dygn;
        }
    }

    /**
     * Bygg array med data om beräkning av kostnad
     * @param [string] $aktivtDatum [datum för ]
     */
    public function setDagensKostnadData($aktivtDatum)
    {
        if(!array_key_exists($aktivtDatum, $this->kostnad_data))
        {
            $this->kostnad_data['parkeringsregler'][$aktivtDatum] = [
            'dagensTaxa' => round($this->dagensTaxa, 2),
            'dagensKostnad' => round($this->dagensKostnad, 2),
            'perioder' => $this->kostnad_data_perioder_array,
            ];
        }
    }
}
