<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Tournament maker</title>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>

    @vite('resources/js/app.js')
</head>

<body>
    @routes
    <div id="app">
        @if(auth()->user() && auth()->user()->isAdmin())
            <admin-navigation></admin-navigation>
        @else
            <navigation></navigation>
        @endif
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
