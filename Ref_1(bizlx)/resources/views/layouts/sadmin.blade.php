<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
{{--    <script src="{{ asset('js/app.js') }}" defer></script>--}}

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <v-app app>
            @guest
            @else
                @if(Auth::guard('sadmin')->check())
                    <v-app-bar light color="white" clipped-left app>
                        <v-app-bar-nav-icon></v-app-bar-nav-icon>
                        <v-spacer></v-spacer>
                        <a href="{{ route('sadmin.logout') }}"> <v-icon>mdi-logout</v-icon></a>
                    </v-app-bar>
                @endif
                    @if(Auth::guard('sadmin')->check())
                        <v-navigation-drawer permanent app>
                            <v-list>
                                <v-list-item class="px-2">
                                    <v-list-item-avatar>
                                        <v-img src="https://randomuser.me/api/portraits/men/85.jpg"></v-img>
                                    </v-list-item-avatar>
                                </v-list-item>
                                <v-list-item link>
                                    <v-list-item-content>
                                        <v-list-item-title class="text-h6">{{ Auth::user()->name }}</v-list-item-title>
                                        <v-list-item-subtitle>{{ Auth::user()->email }}</v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
                            <v-divider></v-divider>
                            <v-list dense shaped nav>
                                <v-list-group>
                                    <template v-slot:activator>
                                        <v-icon class="me-4">mdi-shape</v-icon>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                Categories
                                            </v-list-item-title>
                                        </v-list-item-content>
                                    </template>
                                    <v-list-item to="/admin/maincategories">
                                        <v-icon class="me-4">mdi-shape-plus</v-icon>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                Main Categories
                                            </v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-list-item to="/admin/subcategories">
                                        <v-icon class="me-4">mdi-shape</v-icon>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                Sub Categories
                                            </v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list-group>
                                <v-list-item href="/sadmin/category">
                                    <v-list-item-icon>
                                        <v-icon>mdi-shape</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-title>Categories</v-list-item-title>
                                </v-list-item>
                                <v-list-item href="/sadmin/location">
                                    <v-list-item-icon>
                                        <v-icon>mdi-folder</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-title>Locations</v-list-item-title>
                                </v-list-item>
                                <v-list-item href="{{ route('sadmin.logout') }}">
                                    <v-list-item-icon>
                                        <v-icon>mdi-logout</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-title>Logout</v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </v-navigation-drawer>
                    @endif
            @endguest
                <v-main app>
                    <v-container fluid>
                        @yield('content')
                    </v-container>
                </v-main>
        </v-app>
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
