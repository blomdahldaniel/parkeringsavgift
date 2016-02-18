<?php

namespace App\Models\Traits;

use App\Models\Parkering;
use Illuminate\Database\Eloquent\Model;

trait SpecialparkeringPropertyGenvagar
{
    /**
     * Bygger upp property-genvägar till specialregelns subklasser
     */

    /**
     * Genväg till subklassens property `id`
     * @return int id
     */
    public function id()
    {
        return $this->specialregel->id;
    }

    /**
     * Genväg till subklassens property `beskrivning`
     * @return string beskrivning
     */
    public function beskrivning()
    {
        return $this->specialregel->beskrivning;
    }

    /**
     * Genväg till subklassens property `created_at`
     * @return date created_at
     */
    public function created_at()
    {
        return $this->specialregel->created_at;
    }

    /**
     * Genväg till subklassens property `created_at`
     * @return date created_at
     */
    public function updated_at()
    {
        return $this->specialregel->updated_at;
    }
}
