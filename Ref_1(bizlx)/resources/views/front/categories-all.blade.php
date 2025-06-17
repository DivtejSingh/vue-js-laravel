@extends('layouts.app')

@section('content')
    <all-categories></all-categories>
{{--    <div class="allcats">--}}
{{--        <div class="container">--}}
{{--            <div class="card-columns mt-6 desktop">--}}
{{--                @foreach($categories as $cat)--}}
{{--                    <div class="card-body mb-3 col-md-12 col-12">--}}
{{--                        <ul class="list-group mb-3">--}}
{{--                            <li class="list-group-item bg-light font-weight-bold" aria-current="true">--}}
{{--                                <img src="{{'https://bizlx.itechvision.in/'.$cat->cat_img_url}}"  alt="{{$cat->cat_name}}" width="32">--}}
{{--                                {{$cat->cat_name}}--}}
{{--                            </li>--}}
{{--                            @foreach($cat->subcats as $subcat)--}}
{{--                                <li class="list-group-item">--}}
{{--                                    <img src="{{'https://bizlx.itechvision.in/'.$subcat->subcat_img_url}}"  alt="{{$subcat->subcat_name}}" width="24">--}}
{{--                                    {{$subcat->subcat_name}}--}}
{{--                                </li>--}}
{{--                            @endforeach--}}
{{--                        </ul>--}}
{{--                    </div>--}}
{{--                @endforeach--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
@endsection

