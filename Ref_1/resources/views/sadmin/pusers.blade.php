@extends('layouts.sadmin')

@section('content')
    <h2>Public Users</h2>
    <public-users :users="{{$pusers}}"></public-users>
@endsection
