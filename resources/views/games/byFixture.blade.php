@extends('layouts.base')
@section('content')
    <div class="overflow-x-auto">
        <div class="bg-white shadow-md rounded-lg">
            <div id="app">
                <all-by-fixtures  fixture-id="{{ json_encode($fixtures[0]) }}"></all-by-fixtures>
            </div>
        </div>
    </div>
@endsection
