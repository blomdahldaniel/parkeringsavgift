<?php

namespace App\Models;

use App\Models\Parkering;
use App\Models\Parkeringsomrade;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\SpecialparkeringPropertyGenvagar;
class Specialparkeringsregel extends Model
{
    use SpecialparkeringPropertyGenvagar;
    protected $table = 'specialparkeringsregler';
    public $fillable = [
        'specialregel_id',
        'specialregel_type',
    ];

    /**
     * Genväg till att beräkna kostnaden för aktiv subblass.
     * @return int id
     */
    public function kostnadsPaverkan(Parkering $parkering)
    {
        return $this->specialregel->kostnadsPaverkan($parkering);
    }

    /**
     * Här binds rellationen till parkeringsområdet.
     * En specialparkeringsregel tillhör ett parkeringsområde.
     * Detta sker genom pekande till pivot-tabellen `parkeringsomrade_specialparkeringsregel`
     */
    public function parkeringsomraden()
    {
        return $this->belongsToMany(Parkeringsomrade::class, 'parkeringsomrade_specialparkeringsregel')->withTimestamps();
    }

    /**
     * Här binds rellationen mellan huvudklassen Specialparkeringsregel och
     * de sub-classer som används för att hantera specialregler.
     * Pekningen sker genom tabellen `specialparkeringsregler`
     * med hjälp av `specialregel_id` och `specialregel_type`.
     */
    public function specialregel()
    {
        return $this->morphTo();
    }
}
