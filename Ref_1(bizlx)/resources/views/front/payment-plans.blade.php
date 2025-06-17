@extends('layouts.app')

@section('content')
    <h1 class="text-center mt-3 h3"><span>Payment and Plans</span></h1>
    <div class="text-center mt-3"><a href="#" class="cred text-decoration-underline">Custom Payment</a></div>
    <div class="container">
        <div class="cmsp py-3">
            <div class="row cmsplan g-5 text-center justify-content-center">
                @foreach($plans as $plan)
                <div class="col-12 col-md-4">
                    <div class="base shadow rounded-3 px-1 p2" id="{{$plan->id}}">
                        <div class="mtitle">{{$plan->plan_description}}</div>
                        <ul class="list-group list-group-flush">
                                @if($plan->plan_price == 0)
                                    <li class="list-group-item fw-bold mprice">Free 0</li>
                                @else
                                    <li class="list-group-item fw-bold mprice">INR : {{$plan->plan_price}}</li>
                                @endif

                                @if($plan->images > 0)
                                    <li class="list-group-item">
                                        <v-icon color="green">mdi-checkbox-marked-circle-outline</v-icon>
                                        Images : <b>{{ $plan->images }}</b></li>
                                @else
                                    <li class="list-group-item">
                                        <v-icon color="red">mdi-close-circle-outline</v-icon>
                                        Images : <b>No</b></li>
                                @endif

                                @if($plan->hot_deals > 0)
                                    <li class="list-group-item">
                                        <v-icon color="green">mdi-checkbox-marked-circle-outline</v-icon>
                                        Hot Deals : <b>{{ $plan->hot_deals }}</b></li>
                                @else
                                    <li class="list-group-item">
                                        <v-icon color="red">mdi-close-circle-outline</v-icon>
                                        Hot Deals : <b>No</b></li>
                                @endif

                                @if($plan->sale > 0)
                                    <li class="list-group-item">
                                        <v-icon color="green">mdi-checkbox-marked-circle-outline</v-icon>
                                        Sales : <b>{{ $plan->sale }}</b></li>
                                @else
                                    <li class="list-group-item">
                                        <v-icon color="red">mdi-close-circle-outline</v-icon>
                                        Sales : <b>No</b></li>
                                @endif

                                @if($plan->jobs > 0)
                                    <li class="list-group-item">
                                        <v-icon color="green">mdi-checkbox-marked-circle-outline</v-icon>
                                        Jobs : <b>{{ $plan->jobs }}</b></li>
                                @else
                                    <li class="list-group-item">
                                        <v-icon color="red">mdi-close-circle-outline</v-icon>
                                        Jobs : <b>No</b></li>
                                @endif

                                @if($plan->video > 0)
                                    <li class="list-group-item">
                                        <v-icon color="green">mdi-checkbox-marked-circle-outline</v-icon>
                                        Vidoes : <b>{{ $plan->video }}</b></li>
                                @else
                                    <li class="list-group-item">
                                        <v-icon color="red">mdi-close-circle-outline</v-icon>
                                        Vidoes : <b>No</b></li>
                                @endif

                                @if($plan->about > 0)
                                    <li class="list-group-item">
                                        <v-icon color="green">mdi-checkbox-marked-circle-outline</v-icon>
                                        About : <b>{{ $plan->about }}</b></li>
                                @else
                                    <li class="list-group-item">
                                        <v-icon color="red">mdi-close-circle-outline</v-icon>
                                        About : <b>No</b></li>
                                @endif

                                @if($plan->home_page_banner > 0)
                                    <li class="list-group-item">
                                        <v-icon color="green">mdi-checkbox-marked-circle-outline</v-icon>
                                        Home Page Banner : <b>{{ $plan->home_page_banner }}</b></li>
                                @else
                                    <li class="list-group-item">
                                        <v-icon color="red">mdi-close-circle-outline</v-icon>
                                        Home Page Banner : <b>No</b></li>
                                @endif

                                @if($plan->services > 0)
                                    <li class="list-group-item">
                                        <v-icon color="green">mdi-checkbox-marked-circle-outline</v-icon>
                                        Services : <b>{{ $plan->services }}</b></li>
                                @else
                                    <li class="list-group-item">
                                        <v-icon color="red">mdi-close-circle-outline</v-icon>
                                        Services : <b>No</b></li>
                                @endif

                                @if($plan->contact > 0)
                                    <li class="list-group-item">
                                        <v-icon color="green">mdi-checkbox-marked-circle-outline</v-icon>
                                        Contact : <b>Yes</b></li>
                                @else
                                    <li class="list-group-item">
                                        <v-icon color="red">mdi-close-circle-outline</v-icon>
                                        Contact : <b>No</b></li>
                                @endif

                                @if($plan->reviews > 0)
                                    <li class="list-group-item">
                                        <v-icon color="green">mdi-checkbox-marked-circle-outline</v-icon>
                                        Reviews : <b>Yes</b></li>
                                @else
                                    <li class="list-group-item">
                                        <v-icon color="red">mdi-close-circle-outline</v-icon>
                                        Reviews : <b>No</b></li>
                                @endif

                                @if($plan->featured_city > 0)
                                    <li class="list-group-item">
                                        <v-icon color="green">mdi-checkbox-marked-circle-outline</v-icon>
                                        Featured City : <b>Yes</b></li>
                                @else
                                    <li class="list-group-item">
                                        <v-icon color="red">mdi-close-circle-outline</v-icon>
                                        Featured City : <b>No</b></li>
                                @endif

                                @if($plan->featured_category > 0)
                                    <li class="list-group-item">
                                        <v-icon color="green">mdi-checkbox-marked-circle-outline</v-icon>
                                        Featured Category : <b>Yes</b></li>
                                @else
                                    <li class="list-group-item">
                                        <v-icon color="red">mdi-close-circle-outline</v-icon>
                                        Featured Category : <b>No</b></li>
                                @endif
                        </ul>
                        <div class="my-3 btn btn-outline-light">BUY NOW</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
