@extends('layouts.app')

@section('content')
    <business-view :bdata="{{$bprofile}}" :id="{{$bprofile->id}}"></business-view>
    
@endsection
