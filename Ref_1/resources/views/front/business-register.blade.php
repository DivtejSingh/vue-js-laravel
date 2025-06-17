@extends('layouts.app')

@section('content')
    <v-container>
        <v-row>
            {{-- <v-col cols="12" lg="7">
                <v-img src="{{config('app.cdn_url').('images/registration_images/64b4cf550fd84.jpg')}}"></v-img>
            </v-col> --}}
            {{-- <v-col cols="12" lg="5"> --}}
                <business-register></business-register>
            {{-- </v-col> --}}
        </v-row>

    </v-container>
@endsection
