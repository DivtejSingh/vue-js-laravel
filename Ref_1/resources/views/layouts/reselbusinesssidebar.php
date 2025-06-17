<v-list dense nav shaped>
    <v-list-item-group>
        <v-list-item href="{{route('homepage')}}">
            <img src="{{asset('images/logo.png')}}" class="img-fluid" width="50" alt="Bizlx">
        </v-list-item>
    </v-list-item-group>
</v-list>
<v-list dense nav shaped>
    <v-list-item-group>
        <v-list-item href="{{route('relbusiness.dashboard')}}">
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

