<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>TBNB</title>
        <link rel="stylesheet" href="{{env('APP_ENV') == 'production' ? secure_asset('css/app.css') : asset('css/app.css')}}">
    </head>
    <body>
        <div id="app">
            <home></home>
        </div>
        <script src="{{env('APP_ENV') == 'production' ? secure_asset('js/app.js') : asset('js/app.js')}}"></script>
    </body>
</html>
