<?php
namespace App\Models;

use App\Models\Parkering;
use Illuminate\Database\Eloquent\Model;

class Anvandare extends Model {

    protected $table = 'anvandare';
    public $fillable =[
        'namn',
    ];

    public function parkeringar()
    {
        return $this->hasMany(Parking::class, 'user_id');
    }

}
