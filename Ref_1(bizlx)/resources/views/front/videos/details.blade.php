@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="text-center">
            <span class="h5 font-weight-medium cred mb-2">Video Detail</span>
        </div>
        <div class="text-center text-md-start">
            <v-btn color="red" dark href="{{url()->previous()}}"><v-icon>mdi-chevron-left</v-icon>Back</v-btn>
            <a href="#accordionContact">
                <v-btn color="success" class="me-1"><v-icon small>mdi-map-marker</v-icon>Location</v-btn>
            </a>
        </div>
        @if(isset($videtail))
            <videodetail-page :vidata="{{$videtail}}"></videodetail-page>
        @endif
    </div>
@endsection
