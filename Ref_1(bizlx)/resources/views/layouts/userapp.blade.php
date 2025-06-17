<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{asset('images/logo.png')}}">
    @php $title = $title ?? ''; @endphp
    <title>{{$title}} Bizlx.com™</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
{{--    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">--}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@latest/css/materialdesignicons.min.css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <meta name="user" content="{{ Auth::user() }}">
</head>
<body>
    <div id="app">
        <v-app app>
            <menu-bar :auser="{{Auth::user()}}"></menu-bar>
{{--            <v-app-bar app>--}}
{{--                <v-app-bar-nav-icon drawer="true"></v-app-bar-nav-icon>--}}
{{--                <v-spacer></v-spacer>--}}
{{--                @php--}}
{{--                $user = Auth::user()--}}
{{--                @endphp--}}
{{--                @if($user->profile != null)--}}
{{--                    <v-img src="{{'https://bizlx.s3.ap-south-1.amazonaws.com'.$user->profile->user_avatar}}" max-width="50px" width="50" height="50"></v-img>--}}
{{--                @else--}}
{{--                    <v-img src="{{'https://bizlx.s3.ap-south-1.amazonaws.com/images/logo.png'}}" max-width="50px" width="50" height="50"></v-img>--}}
{{--                @endif--}}
{{--            </v-app-bar>--}}
{{--            <v-navigation-drawer drawer fixed app>--}}
{{--                @if(Auth::user()->user_role == 0)--}}
{{--                    @include('layouts.usersidebar')--}}
{{--                @elseif(Auth::user()->user_role == 1)--}}
{{--                    @include('layouts.businessidebar')--}}
{{--                @elseif(Auth::user()->user_role == 2)--}}
{{--                    @include('layouts.resellersidebar')--}}
{{--                @elseif(Auth::user()->user_role == 3)--}}
{{--                    @include('layouts.sidebar')--}}
{{--                @elseif(Auth::user()->user_role == 4)--}}
{{--                    @include('layouts.sidebar')--}}
{{--                @endif--}}
{{--            </v-navigation-drawer>--}}
            <v-main app>
                @yield('content')
            </v-main>
        </v-app>
    </div>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
<script>
    const current = window.location.href;
    document.querySelectorAll('a').forEach(function(elem){if(elem.href.includes(current)){elem.classList.add("active");}});
</script>
</body>
</html>
