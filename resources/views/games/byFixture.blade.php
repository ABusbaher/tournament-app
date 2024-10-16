@extends('layouts.base')
@section('content')
    <div class="overflow-x-auto">
        <div class="bg-white shadow-md rounded-lg">
            <all-by-fixtures  fixture-id="{{ json_decode($fixture) }}"></all-by-fixtures>
        </div>
    </div>
@endsection
