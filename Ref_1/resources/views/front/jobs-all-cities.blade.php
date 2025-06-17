@extends('layouts.app')

@section('content')
<allcityjobs-page :data1="{{$citieswithjobs}}"></allcityjobs-page>
    {{-- <div class="alljobs">
        <div class="container">
            <v-banner>
                <h2 class="h5">
                    <span class="cblu bbred">All City Jobs</span>
                </h2>
            </v-banner>
            <div class="row">
                @foreach($citieswithjobs as $cjob)
                    <div class="col-6 col-md-2">
                        <div class="card">
                            <v-card-text class="text-center h6 mb-0">
                                <span class="me-1">{{$cjob->city}}</span>
                                <span class="text--white badge bg-dark">{{$cjob->jobcount}}</span>
                            </v-card-text>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div> --}}
@endsection
