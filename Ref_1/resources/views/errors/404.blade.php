@extends('layouts.app') <!-- Use your existing layout or create a new one -->

@section('content')
    <div class="container text-center" style="padding: 50px;">
        <h1 style="font-size: 2.5rem; color: #333;">Data Not Found</h1>
        <p style="color: #555; margin: 20px 0;">
            The page you are trying to access does not exist. Please check the URL and try again.
        </p>
        <a href="{{ route('homepage') }}" 
           class="btn btn-primary" 
           style="background-color: #007bff; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px;">
           Go to Homepage
        </a>
    </div>
@endsection
