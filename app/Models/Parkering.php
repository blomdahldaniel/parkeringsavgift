<?php
namespace App\Models;

use Carbon\Carbon;
use App\Models\Anvandare;
use League\Period\Period;
use App\Jobs\BeraknaKostnad;
use App\Models\Parkeringsomrade;
use App\Events\ParkeringAvslutades;
use Illuminate\Support\Facades\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesJobs;

class Parkering extends Model {

    use DispatchesJobs, SerializesModels;

    protected $table = 'parkeringar';
    public $fillable = [
        'anvandare_id',
        'parkeringsomrade_id',
        'kostnad',
        'kostnad_data',
        'start_tid',
        'stop_tid',
    ];

    protected $casts = [
        'kostnad_data' => 'json'
    ];

    public function avslutaParkering($stop_tid)
    {
        if(!isset($stop_tid)){
            $stop_tid = time();
        }
        $this->stop_tid = $stop_tid;
        $this->save();
        $this->dispatch(new BeraknaKostnad($this));
        return $this;
    }

    public function parkeringsregler()
    {
        return $this->parkeringsomrade->parkeringsregler;
    }

    public function specialparkeringsregler()
    {
        return $this->parkeringsomrade->specialparkeringsregler;
    }

    public function startTidsobjekt()
    {
        return Carbon::createFromTimestamp($this->start_tid);
    }

    public function stopTidsobjekt()
    {
        return Carbon::createFromTimestamp($this->stop_tid);
    }

    public function anvandare()
    {
        return $this->belongsTo(Anvandare::class);
    }

    public function parkeringsomrade()
    {
        return $this->belongsTo(Parkeringsomrade::class);
    }
}
