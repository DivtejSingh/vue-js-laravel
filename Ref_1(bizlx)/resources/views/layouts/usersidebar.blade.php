<v-list dense nav shaped>
    <v-list-item-group>
        <v-list-item href="{{route('homepage')}}">
            <img src="{{asset('images/logo.png')}}" class="img-fluid" width="50" alt="Bizlx">
        </v-list-item>
    </v-list-item-group>
</v-list>
<v-list dense nav shaped>
    <v-list-item-group>
        <v-list-item href="{{route('home')}}">
            <v-list-item-icon>
                <v-icon size="20">mdi-badge-account</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Profile Info
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
    </v-list-item-group>
</v-list>
<v-list dense nav shaped>
    <v-list-item-group>
        <v-list-item href="{{route('user.wishlist')}}">
            <v-list-item-icon>
                <v-icon size="20">mdi-cards-heart</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Wishlist
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
    </v-list-item-group>
</v-list>
<v-list dense nav shaped>
    <v-list-item-group>
        <v-list-item href="{{route('user.reviews')}}">
            <v-list-item-icon>
                <v-icon size="20">mdi-file-star</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Reviews
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
    </v-list-item-group>
</v-list>
<v-list dense nav shaped>
    <v-list-item-group>
        <v-list-item href="https://play.google.com/store/apps/details?id=com.bizlxapp" target="_blank">
            <v-list-item-icon>
                <v-icon size="20">mdi-progress-download</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Download App
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
    </v-list-item-group>
</v-list>
<v-list dense nav shaped>
    <user-logout></user-logout>
    <form id="logout-form" action="{{ route('user.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</v-list>
