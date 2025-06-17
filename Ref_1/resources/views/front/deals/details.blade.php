@extends('layouts.app')

@section('content')
    <div class="container">
        @if(isset($dealdetail))
            <dealdetail-page :data1="{{$dealdetail}}" :id="{{$dealdetail->id}}"></dealdetail-page>
        @endif
    </div>
   @endsection
