<?php

namespace App\Models\Interfaces;

use Carbon\Carbon;
use App\Models\Parkering;
use League\Period\Period;
use App\Models\Parkeringsregel;

interFace SpecialparkeringsregelInterface
{
    /**
     * Metod som räknar ut kostnadens paverkan på parkeringen
     * @param  Parkering $parkering [För att nå ->kostnad_data]
     * @return [int] [kostnadspaverkan i kr för parkeringen]
     */
    public function beraknaTaxa(Parkering $parkering, Parkeringsregel $regel, Period $aktivParkeringsPeriod, Carbon $aktivDagRegelStop);

    public function setParkeringAvslutadFranSpecial();

    public function setAktivParkeringsperiodAvslutadFranSpecial();

    public function setAktivParkeringsPeriod();
}
