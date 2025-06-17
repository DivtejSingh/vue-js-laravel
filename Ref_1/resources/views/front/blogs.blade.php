@extends('layouts.app')

@section('content')
<div class="blog-top-section" style="background: linear-gradient(0deg, rgb(0 0 0 / 30%), rgb(0 0 0 / 30%)), url(images/hotel-taxi.jpg);background-size: cover;background-position: center;background-repeat: no-repeat;">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="blogs">
                    <h1><b>Blogs</b></h1>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. <br>Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-md-4">
            <a href="#"><img src="{{ asset('images/1.png') }}" class="img-fluid" style="border-radius: 10px 10px 0px 0px;" alt="Affordable Rides" /></a>
            <div class="blog-section">
                <p class="date">January 2024</p>
                <h4><a href="#">Affordable Rides Anytime, Anywhere</a></h4>
                <p>Reliable and affordable taxi service in Manali offering clean vehicles, experienced drivers, and 24/7 availability for your convenience.</p>
                <a href="#" type="button" class="btn btn-primary reset-button">Read More</a>
            </div>
        </div>
        <div class="col-md-4">
            <a href="#"><img src="{{ asset('images/2.png') }}" class="img-fluid" style="border-radius: 10px 10px 0px 0px;" alt="Hotel Management" /></a>
            <div class="blog-section">
                <p class="date">January 2024</p>
                <h4><a href="#">Top Hotel Management Jobs Manali</a></h4>
                <p>Join leading hotels for rewarding careers in hospitality, offering growth and scenic work environments.</p>
                <a href="#" type="button" class="btn btn-primary reset-button">Read More</a>
            </div>
        </div>
        <div class="col-md-4">
            <a href="#"><img src="{{ asset('images/3.png') }}" class="img-fluid" style="border-radius: 10px 10px 0px 0px;" alt="Luxurious Hotel" /></a>
            <div class="blog-section">
                <p class="date">January 2024</p>
                <h4><a href="#">Luxurious Retreats in Snowy Bliss</a></h4>
                <p> Luxurious rooms, breathtaking mountain views, and world-class service make it Manali's ultimate retreat.</p>
                <a href="#" type="button" class="btn btn-primary reset-button">Read More</a>
            </div>
        </div>
    </div>
</div>
@endsection
<style>
    .blog-section {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 0px 0px 10px 10px;
    }

    p.date {
        margin-bottom: 5px;
    }

    .blog-section h4 a {
        text-decoration: none;
        color: #000;
    }

    .blogs p {
        font-size: 18px;
    }

    .blog-top-section {
        background: #fafafa;
        padding: 100px 0px 100px 0px;
        color: #fff;
        text-align: center;
    }


    a.btn.btn-primary.reset-button {
        color: #fff !important;
    }
</style>