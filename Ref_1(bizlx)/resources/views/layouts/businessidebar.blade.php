<v-list dense nav shaped>
    <v-list-item-group>
        <v-list-item href="{{route('homepage')}}">
            <img src="{{asset('images/logo.png')}}" class="img-fluid" width="50" alt="Bizlx">
        </v-list-item>
    </v-list-item-group>
</v-list>
<v-list dense nav shaped>
    <v-list-item-group>
        <v-list-item href="{{route('bussiness.dashboard')}}">
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
        <v-list-item href="{{route('business.coverimage')}}">
            <v-list-item-icon>
                <v-icon size="20">mdi-image</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
                <v-list-item-title>
                  Main Cover Picture
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
    </v-list-item-group>
</v-list>
<v-list dense nav shaped>
    <v-list-item-group>
        <v-list-item href="{{route('business.galleryimage')}}">
            <v-list-item-icon>
                <v-icon size="20">mdi-folder-multiple-image</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
                <v-list-item-title>
                  Gallery Images
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
    </v-list-item-group>
</v-list>
<v-list dense nav shaped>
    <v-list-item-group>
        <v-list-item href="{{route('business.deals')}}">
            <v-list-item-icon>
                <v-icon size="20">mdi-handshake</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
                <v-list-item-title>
                  Post Deals
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
    </v-list-item-group>
</v-list>
<v-list dense nav shaped>
    <v-list-item-group>
    <v-list-item href="{{ route('business.wishlist') }}">
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
        <v-list-item href="{{route('business.reviews')}}">
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
        <v-list-item href="{{route('business.reviewbybusiness')}}">
            <v-list-item-icon>
                <v-icon size="20">mdi-file-star</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Reviews By Business
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
    </v-list-item-group>
</v-list>
<v-list dense nav shaped>
    <v-list-item-group>
    <v-list-item href="{{ route('business.billing') }}">
            <v-list-item-icon>
                <v-icon size="20">mdi-book</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Billing & Plans
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
    </v-list-item-group>
</v-list>
<v-list dense nav shaped>
    <v-list-item-group>
    <v-list-item href="{{ route('bussinesspage',Auth::user()->username) }}">
            <v-list-item-icon>
                <v-icon size="20">mdi-book</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Preview Website
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
