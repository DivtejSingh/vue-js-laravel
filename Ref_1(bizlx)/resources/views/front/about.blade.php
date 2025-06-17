@extends('layouts.app')

@section('content')
    <div class="about">
        {{-- <h1 class="text-center mt-3 h3">
            <span>{{ $title }}</span>
        </h1> --}}
   
          
  
     
        <v-container>
          {!! $content !!}

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
