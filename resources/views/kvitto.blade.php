<!DOCTYPE html>
<html lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Title Page</title>
        <link href='https://fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>
        <style>
        body{
            font-family: 'Roboto', sans-serif;
        }
        </style>
    </head>
    <body>
    <a href="{{ url('/') }}">Tillbaka till startsidan</a>
    <h1>Parkering mellan {{ $parkering->startTidsobjekt() .' - '. $parkering->stopTidsobjekt() }} </h1>
    <h1>Kostnad: {{ $parkering->kostnad }} kr</h1>
    <hr>
    <h1>Beräkning</h1>
    @foreach ($parkering->kostnad_data['parkeringsregler'] as $datum => $dag)
        <!-- vanliga parkeringsregler -->
         <h2>{{ $datum }}</h2>
         <h4>Taxa dag: {{ $dag['dagensTaxa'] }} kr (max  {{ $parkering->parkeringsomrade->max_kostnad_per_dygn }}  kr)</h4>
         <h4>Total kostnad dag: {{ $dag['dagensKostnad'] }} kr</h4>
         <h3>Perioder</h3>
         ---------------------------------------------------------------------------------------
         @foreach ($dag['perioder'] as $period)
            <p>Från: {{ $period['start'] }}  </p>
            <p>Till: {{ $period['stop'] }}  </p>
            <p>Taxa: {{ $period['taxa_total'] }}  kr ( {{ $period['tid_timmar'] }} h * {{ $period['taxa'] }}  kr/tim)</p>
            @if(array_key_exists('beskrivning', $period))
                <p>beskrivning: {{ $period['beskrivning'] }} </p>
            @endif
            ---------------------------------------------------------------------------------------<br>
         @endforeach
     @endforeach
    </body>
</html>
