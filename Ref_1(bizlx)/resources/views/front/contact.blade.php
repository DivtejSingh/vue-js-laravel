@extends('layouts.app')

@section('content')
    <div class="contact bg-light">
        <h1 class="text-center pt-7 h2"><span>Contact Us</span></h1>
        <div class="container">
            <contact-view :profile="{{$profile}}"></contact-view>
        </div>
    </div>
@endsection
