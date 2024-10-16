<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Tournament maker</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.7.0/flowbite.min.css" rel="stylesheet" />
    @vite('resources/js/app.js')
</head>

<body>
    @routes
    <div id="app">
        <navigation></navigation>
        <div class="container mx-auto">
            @yield('content')
        </div>
    </div>
</body>
<script>
    window.Laravel = {
        user: @json(auth()->user())
    };
</script>
</html>
