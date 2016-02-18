<?php

namespace App\Models;

use App\Models\Parkering;
use App\Models\Parkeringsregel;
use App\Models\Specialparkeringsregel;
use Illuminate\Database\Eloquent\Model;

class Parkeringsomrade extends Model
{
    protected $table = 'parkeringsomraden';
    public $fillable = [
        'namn',
        'kod_omrade',
        'max_kostnad_per_dygn',
    ];

    /**
     * Rellationer
     */

    public function parkeringar()
    {
        return $this->hasMany(Parkering::class);
    }

    public function parkeringsregler()
    {
        return $this->belongsToMany(Parkeringsregel::class, 'parkeringsomrade_parkeringsregel')->withTimestamps()->orderBy('start_tid');
    }

    public function specialparkeringsregler()
    {
        return $this->belongsToMany(Specialparkeringsregel::class, 'parkeringsomrade_specialparkeringsregel')->withTimestamps();
    }
}
