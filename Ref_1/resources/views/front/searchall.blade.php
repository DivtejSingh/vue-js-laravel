@extends('layouts.app')

@section('content')
    @if(isset($dealtype) && !empty($dealtype))
        <search-page :reqparams="{{ $dealtype }}"></search-page>
    @else
        <search-page></search-page>
    @endif
@endsection