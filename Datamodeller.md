# Datamodeller

## Anvandare::class
* `id`
* `namn`
* `parkeringar -> hasMany(Parking::class)`

## Parkering::class
* `id`
* `stop_tid //timestamp`
* `start_tid //timestamp`
* `anvandare_id -> belongsTo(Anvandare::class)`
* `(parkeringsomrade_id) -> belongsTo(Parkeringsomrade::class)`
* `kostnad`
* `kostnad_data (json)`

**Har metoder som aggerar genvägar för de rellations-parametrar och metoder som finns**

---

## Parkeringsomrade::class
* `namn`
* `kod_omrade`
* `max_kostnad_per_dygn`
* `parkeringar -> hasMany(Parkering:class)`
* `parkeringsregler -> hasMany(Parkeringsregel::class)`
* `specialparkeringsregler -> hasMany(Specialparkeringsregel::class)`

----

## Parkeringsregel::class
* `id`
* `start_tid //ex. 09:00`
* `stop_tid //ex. 18:00`
* `taxa`
* `beskrivning`
* `parkeringsomraden -> belongsToMany(ParkeringsOmrade::class)`


----

## Specialparkeringsregel::class

* `id`
* `specialregel_id`
* `specialregel_type`
* `specialregler -> morphTo()`
* `belongsToMany(Parkeringsomrade::class)`


----
## Klasser som är specialregler
Alla klasser här styrs av inteface `SpecialparkeringsregelInterface` som kräver metoden `beraknaTaxa` samt andra värden som måste retrneras till huvudlogiken.
### ForstaTimmenXkr::class
* `id`
* `taxa`
* `gratis_timme // om den gäller under gratis timme eller inte`
* `beskrivning`
* `specialregel ->morphTo()`

