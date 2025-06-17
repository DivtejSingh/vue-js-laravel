@extends('layouts.sadmin')

@section('content')
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2>Locations</h2>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-sm btn-primary"
                    title="Add State" data-bs-toggle="modal" data-bs-target="#addstate">
                Add State
            </button>
            <button type="button" class="btn btn-sm btn-info"
                    title="Add City" data-bs-toggle="modal" data-bs-target="#addcity">
                Add City
            </button>
        </div>
    </div>
    <div class="table-group-divider"></div>
    <table class="table">
        <thead>
        <tr>
            <th>Country</th>
            <th>State</th>
            <th>City</th>
            <th>Created At</th>
            <th width="125px">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($locate as $loc)
            <tr>
                <td>{{$loc->country}}</td>
                <td>{{$loc->state}}
                <td>{{$loc->city}}</td>
                <td>{{$loc->created_at}}</td>
                <td>
                    <a href="#" class="btn btn-sm btn-primary">Edit</a>
                    <a href="#" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="table-group-divider"></div>
    <table class="table">
        <thead>
        <tr>
            <th>Country</th>
            <th>State</th>
            <th>City</th>
            <th>Created At</th>
            <th width="125px">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($locations as $location)
        <tr>
            <td>{{$location->country}}</td>
            <td>
            @foreach($location->states as $state)
                {{$state->state}}

            <td>
                <ul>


                @foreach($state->cities as $city)
                        <li>{{$city->city}}</li>
                @endforeach
                </ul>
            </td>
            @endforeach
            <td>date</td>
            <td>
                <a href="#" class="btn btn-sm btn-primary">Edit</a>
                <a href="#" class="btn btn-sm btn-danger">Delete</a>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
@endsection
