<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{asset('images/logo.png')}}">

    @php
        $title = $title ?? '';
        $desc = $desc ?? "Bizlx.com™ is India's daily local hot deals, sales, and latest information website of shops and stores. Search hotels, shops,
                            garments, furniture, jewelry, automobiles, professionals, mobile phones, restaurants with reviews &amp;amp; discounts on shopping &amp;amp; product
                            videos everyday. Bizlx provides local, accurate &amp;amp; verified business information to customers with regular updates";
        $ogimage = $ogimage ?? '/images/business-register.jpg';
    @endphp
    <title>{{$title}} on Bizlx.com™</title>
{{--    <meta property="og:url" content="https://bizlx.com">--}}
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{$title}} Bizlx">
    <meta property="og:image" content="{{('https://bizlx.s3.ap-south-1.amazonaws.com').$ogimage}}">
    <meta property="og:description" content="{{$desc}} - {{$title}} Bizlx">

    <meta name="theme-color" content="#0165a3">
    <meta property="og:site_name" content="Bizlx">
    <meta property="og:type" content="website">
    <meta property="og:keywords" content="Bizlx,bizlx hot deals,Bizlx hotel deals,Bizlx daily deals website,India Daily Hot Deals Website,resort deals bizlx,Bizlx Founder Rajneesh Sharma,Products,Professional,Professionals,Local Hot Deals,promotion website,cottages listed on bizlx,Bizlx discounts,Leading Hotels in Bizlx">
    <script type="application/ld+json">
        {
            "@context":"http://schema.org",
            "@type":"WebSite",
            "name":"Bizlx",
            "url":"{{ url()->current() }}",
            "logo":"https://bizlx.s3.ap-south-1.amazonaws.com/images/logo.png",
            "sameAs":[
                "https://www.facebook.com/bizlx",
                "https://www.linkedin.com/company/bizlx/",
                "https://www.instagram.com/bizlx_/"]
        }
    </script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//cdn.bizlx.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <meta name="user" content="{{ Auth::user() }}">
</head>
<body>
    <div id="app">
        <v-app app v-cloak>
            <nav class="navbar navbar-expand navbar-light bg-white shadow-sm sticky-top flex-column">
                <div class="container py-0">
                    <a class="navbar-brand" href="{{ route('homepage') }}">
                        <img src="{{config('app.cdn_url').('images/logo.png')}}" class="img-fluid" width="50" alt="Bizlx">
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav mx-auto d-md-none">
                            <li>
                                {{-- <img src="{{asset('images/appload.jpeg')}}" alt="Download App" width="32px" height="32px"> --}}
                            </li>
                        </ul>
                        <div class="ms-auto d-md-block d-none">
                            <cat-search></cat-search>
                        </div>

                                <!-- Desktop version (visible on md and up) -->
                        <ul class="navbar-nav ms-auto d-none d-md-flex">
                            <li class="nav-item">
                                <a href="{{ route('front.businessreg') }}" class="nav-link">
                                    <v-btn color="red" dark small>List Business</v-btn>
                                </a>
                            </li>
                        </ul>

                        <!-- Mobile version (visible below md only) -->
                        <ul class="navbar-nav d-flex justify-content-center d-md-none w-100" style="position: relative; left: -20px;">
                            <li class="nav-item text-center">
                                <a href="{{ route('front.businessreg') }}" class="nav-link px-0">
                                    <v-btn color="red" dark small class="mx-auto">List Business</v-btn>
                                </a>
                            </li>
                        </ul>
                        

                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ms-auto">
                            <!-- Authentication Links -->
                            @guest
{{--                                <li class="nav-item me-4">--}}
{{--                                    <v-icon class="cblu">mdi-heart-outline</v-icon>--}}
{{--                                </li>--}}
                                <menu-login></menu-login>
                            @else
                                <li class="nav-item dropdown">
{{--                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>--}}
{{--                                        {{ Auth::user()->name }}--}}
{{--                                    </a>--}}
                                    <menu-login></menu-login>

                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <user-logout></user-logout>
                                        <form id="logout-form" action="{{ route('user.logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
                <div class="container py-0 d-md-none mx-auto">
                    <ul class="navbar-nav mx-auto w-100 d-md-none">
                        <cat-search></cat-search>
                    </ul>
                </div>
            </nav>

            <v-main>
                @yield('content')
            </v-main>
            @include('layouts.mainfooter')
        </v-app>
    </div>
    <!-- Scripts -->

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        Fancybox.bind("[data-fancybox]", {
            // Your custom options
        });
    </script>
     <!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-DNJN1PF3CS"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());

gtag('config', 'G-Y6ZQHP21P0');
</script> 
<script>
        function togglePassword(fieldId, iconElement) {
            const input = document.getElementById(fieldId);
            const icon = iconElement.querySelector('i');
   
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>
</html>
