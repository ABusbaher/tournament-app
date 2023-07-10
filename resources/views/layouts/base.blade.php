<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Tournament maker</title>

    @vite('resources/js/app.js')
</head>

<body>
    <div class="container mx-auto">
        @yield('content')
    </div>
</body>

</html>
