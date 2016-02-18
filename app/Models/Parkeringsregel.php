<?php

namespace App\Models;

use App\Models\Parkeringsomrade;
use Illuminate\Database\Eloquent\Model;

class Parkeringsregel extends Model
{
    protected $table = 'parkeringsregler';
    public $fillable = [
        'namn',
        'start_tid',
        'stop_tid',
        'taxa',
        'beskrivning',
    ];

    public function parkeringsomraden()
    {
        return $this->belongsToMany(Parkeringsomrade::class, 'parkeringsomrade_parkeringsregel')->withTimestamps();
    }
}
