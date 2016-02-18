<?php

namespace App\Models\Specialregler;

use Carbon\Carbon;
use App\Models\Parkering;
use League\Period\Period;
use App\Models\Parkeringsregel;
use Illuminate\Database\Eloquent\Model;
use App\Models\Interfaces\SpecialparkeringsregelInterface;


class AnnanTaxaDagIVecka extends Model implements SpecialparkeringsregelInterface
{
    protected $table = 'annan_taxa_dag_i_vecka';
    public $fillable = [
        'taxa',
        'gratis_timme',
        'beskrivning',
        'max_kostnad_per_dygn',
        'veckodagar',
    ];

    protected $casts = [
        'veckodagar' => 'json'
    ];

    public function beraknaTaxa(Parkering $parkering, Parkeringsregel $regel, Period $aktivParkeringsPeriod, Carbon $aktivDagRegelStop)
    {
        $kostnadspaverkan = 0;
        if($this->max_kostnad_per_dygn != null){
            $max_kostnad_per_dygn = $this->max_kostnad_per_dygn;
        }else{
            $max_kostnad_per_dygn = $parkering->parkeringsomrade->max_kostnad_per_dygn;
        }
        $forstaTimmen = 3600;
        foreach ($parkering->kostnad_data['parkeringsregler'] as $datum => $dag) {
            if( in_array(Carbon::parse($datum)->dayOfWeek, $this->veckodagar)){
                foreach ($dag['perioder'] as $period) {
                    // Drar bort den tidigare lagda timkostnaden
                    $kostnadspaverkan -= $period['taxa'] * $period['tid_sekunder'] / 3600;
                    // Adderar del av den nya taxan
                    if($period['taxa'] == 0 || $this->gratis_timme == false){
                        $kostnadspaverkan += $this->taxa * $period['tid_sekunder'] / 3600;
                    }
                    else{
                        $kostnadspaverkan += $this->taxa * $period['tid_sekunder'] / 3600;
                    }
                }
            }
        }
        // Om det inte finns angiven max-kostnad per dygn
        if($max_kostnad_per_dygn == null){
            return $kostnadspaverkan;
        }
        if($kostnadspaverkan < $max_kostnad_per_dygn){
            return $kostnadspaverkan;
        }else{
            return $max_kostnad_per_dygn;
        }
    }

    public function kostnadsDataArray()
    {
        return ['taxa' => $this->taxa];
    }

    public function specialregel()
    {
        return $this->morphTo();
    }


}
