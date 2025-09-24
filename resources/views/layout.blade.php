<!DOCTYPE html>
<html lang="ar">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>سوق +</title>
        @vite('resources/scss/app.scss')
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/favicon/favicon.png') }}" id="favicon">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('storage/favicon/favicon.png') }}">
    </head>
    <body>
        <div id="app"></div>
    </body>
    @vite('resources/js/app.ts')
</html>
