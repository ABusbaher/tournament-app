@extends('layouts.base')
@section('content')
    <div class="overflow-x-auto">
        <div class="bg-white shadow-md rounded-lg">
            <h1 class="mb-4 text-4xl font-extrabold text-center leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl dark:text-white">
                Games in Fixture {{ $fixtures[0] }}
            </h1>
            <div id="app">
                <all-tournaments></all-tournaments>
            </div>
        </div>
    </div>
@endsection
