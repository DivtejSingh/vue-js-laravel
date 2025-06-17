@extends('layouts.app')

@section('content')
    <div class="privacy">
        <v-container>
            {!! $content !!}
            <em>Last Updated: {{ $lastUpdated }}</em>
        </v-container>
    </div>
@endsection
<style>
    .ql-align-center {
        text-align: center;
    }
    .ql-align-right {
        text-align: right;
    }
    .ql-align-justify {
        text-align: justify;
    }
</style>
