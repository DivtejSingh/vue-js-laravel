@extends('layouts.app')

@section('content')
    <div class=" container jcats">
        <jobcat-page :data1="{{$jobcategory}}"></jobcat-page>
    </div>
@endsection
