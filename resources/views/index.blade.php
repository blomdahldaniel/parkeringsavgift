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
            font-size:16px;
            line-height:1.6;
        }
        h2 small{
            font-weight: 100;
        }
        pre{
            display: inline;
            margin: 0;
            font-family:Menlo,Monaco,Consolas,"Courier New",monospace;
            display:inline;
            padding:5.5px;
            margin:0 0 10px;
            font-size:13px;
            line-height:1.42857143;
            color:#333;
            word-break:break-all;
            word-wrap:break-word;
            background-color:#f5f5f5;
            border:1px solid #ccc;
            border-radius:4px;
        }
        </style>
    </head>
    <body>
    <h1>Parkeringsavgifter</h1>
        Exempelparkeringar med kvitto.
        <br>
        Om du vill testa att lägga till en egen tid,
        <br>
        se då instruktionerna i <pre>__construct()</pre> i filen <pre>app\Http\Controllers\ParkeringsregelController.php</pre>
    @foreach($exempelTider as $nr => $tid)
        <h2>
            Parkering
            <small>
            från
            {{ Carbon\Carbon::createFromTimestamp($tid['start'])->format('Y:m:d H:i:s') }}
            till
            {{ Carbon\Carbon::createFromTimestamp($tid['stop'])->format('Y:m:d H:i:s') }}
                <a href="{{ action('ParkeringsregelController@exempel', $nr) }}">visa kvitto</a>
            </small>
        </h2>
    @endforeach

    </body>
</html>
