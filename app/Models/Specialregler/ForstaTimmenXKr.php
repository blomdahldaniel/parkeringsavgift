<?php

namespace App\Models\Specialregler;

use Carbon\Carbon;
use App\Models\Parkering;
use League\Period\Period;
use App\Models\Parkeringsregel;
use Illuminate\Database\Eloquent\Model;
use App\Models\Abstracts\SpecialparkeringsregelAbstract;
use App\Models\Interfaces\SpecialparkeringsregelInterface;


class ForstaTimmenXKr extends Model implements SpecialparkeringsregelInterface
{
    protected $table = 'forsta_timmen_x_kr';
    public $fillable = [
        'taxa',
        'gratis_timme',
        'beskrivning',
    ];

    public function beraknaTaxa(Parkering $parkering, Parkeringsregel $regel, Period $aktivParkeringsPeriod, Carbon $aktivDagRegelStop)
    {
        $aktivParkeringsPeriodSekunder = $aktivParkeringsPeriod->getTimestampInterval();
        $forstaTimmen = 3600;
        $kostnad_data = array();
        $returnData = array();
        $kostnad = 0;
        $parkeringsPeriod = new Period($parkering->startTidsobjekt(), $parkering->stopTidsobjekt());
        $hittillsParkeradTid = $aktivParkeringsPeriod->getStartDate()->getTimestamp() - $parkeringsPeriod->getStartDate()->getTimestamp();
        $aterstaendeTidForRegel = 3600 - $hittillsParkeradTid;
        if($hittillsParkeradTid < 3600){
            $specialAktivParkeringsPeriod = $aktivParkeringsPeriod->intersect(new Period($aktivParkeringsPeriod->getStartDate(), Carbon::parse($aktivParkeringsPeriod->getStartDate()->format('Y-m-d H:i:s'))->addSeconds($aterstaendeTidForRegel) ) );
            if($specialAktivParkeringsPeriod->getTimestampInterval() < 3600){
                $tidAttBerakna = $specialAktivParkeringsPeriod->getTimestampInterval();
            }
            else{
                $tidAttBerakna = 3600 - $hittillsParkeradTid;
            }
            $kostnad_data[] = $this->setKostnadDataArray($parkering, $specialAktivParkeringsPeriod, $regel->taxa, $tidAttBerakna);
            if($regel->taxa != 0 || $this->gratis_timme == false){
                $kostnad = $this->taxa * $tidAttBerakna / 3600;
            }else{
                $kostnad = 0;
            }

            //  // om det är sista perioden för hela parkeringen
            if(Carbon::parse($specialAktivParkeringsPeriod->getEndDate()->format('Y-m-d H:i:s'))->eq($parkering->stopTidsobjekt() ) ){
                $returnData['parkeringAvslutadFranSpecial'] = true;
            }

            //  // om det är sista perioden för hela parkeringen
            if(Carbon::parse($specialAktivParkeringsPeriod->getEndDate()->format('Y-m-d H:i:s'))->eq($parkering->stopTidsobjekt() ) ){
                $returnData['parkeringAvslutadFranSpecial'] = true;
            }
            // om specialregeln avslutat en aktiv parkeringsregel
            elseif( Carbon::parse($specialAktivParkeringsPeriod->getEndDate()->format('Y-m-d H:i:s'))->eq( Carbon::parse($aktivParkeringsPeriod->getEndDate()->format('Y-m-d H:i:s')) ) ){
                $returnData['aktivParkeringsperiodAvslutadFranSpecial'] = true;
            }
            // Bygg om aktivParkeringsPeriod så att loopen kan fortästta på samma regel
            else{
                $tempRegelPeriod = new Period(Carbon::parse($specialAktivParkeringsPeriod->getEndDate()->format('Y-m-d H:i:s')), $aktivDagRegelStop);
                $returnData['aktivParkeringsPeriod'] = $tempRegelPeriod->intersect($parkeringsPeriod);
            }


            $returnData = array_merge($returnData, [
                'kostnad' => $kostnad,
                'kostnad_data' => $kostnad_data,
                'ny_stop_tid' => $specialAktivParkeringsPeriod->getEndDate()->format('Y-m-d H:i:s'),
            ]);

            return $returnData;
        }
        else{
            // parkeringen har hållt på längre än 1 timme
            return null;
        }
    }



    private function setKostnadDataArray(Parkering $parkering, Period $aktivParkeringsPeriod, $aktivRegelTaxa, $tidKvarSekunder)
    {
        return [
            'start' => $aktivParkeringsPeriod->getStartDate()->format('Y-m-d H:i:s'),
            'stop' => $aktivParkeringsPeriod->getEndDate()->format('Y-m-d H:i:s'),
            'tid_sekunder' => $tidKvarSekunder,
            'tid_timmar' => $tidKvarSekunder / 3600,
            'taxa' => $this->taxa,
            'taxa_total' => ($aktivRegelTaxa != 0 || $this->gratis_timme == false) ? $this->taxa * $tidKvarSekunder / 3600 : 0,
            'beskrivning' => $this->beskrivning,
        ];
    }

    /**
     * Rellation med parent-class
     */
    public function specialregel()
    {
        return $this->morphTo();
    }
}
