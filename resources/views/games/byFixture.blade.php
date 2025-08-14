@extends('layouts.base')
@section('content')
    <all-by-fixtures  fixture-id="{{ json_decode($fixture) }}"></all-by-fixtures>
@endsection
