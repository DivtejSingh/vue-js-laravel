<v-list dense nav shaped>
    <v-list-item-group>
        <v-list-item href="{{route('homepage')}}">
            <img src="{{asset('images/logo.png')}}" class="img-fluid" width="50" alt="Bizlx">
        </v-list-item>
    </v-list-item-group>
</v-list>
<v-list dense nav shaped>
    <v-list-item-group>
        <v-list-item href="{{route('admin.dashboard')}}">
            <v-list-item-icon>
                <v-icon size="20">mdi-view-dashboard</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Dashboard
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
    </v-list-item-group>
</v-list>
<v-list dense nav shaped>
    <v-list-group>
        <template v-slot:activator>
            <v-icon class="me-4">mdi-account-group</v-icon>
            <v-list-item-content>
                <v-list-item-title>
                    All Users
                </v-list-item-title>
            </v-list-item-content>
        </template>
        <v-list-item href="{{route('admin.publicusers')}}">
            <v-icon class="me-4">mdi-account-group</v-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Public Users
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-list-item href="{{route('admin.subadmins')}}">
            <v-icon class="me-4">mdi-account-key</v-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Sub Admins
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-list-item href="{{route('admin.resellers')}}">
            <v-icon class="me-4">mdi mdi-handshake</v-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Resellers
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-list-item href="{{route('admin.businesses')}}">
            <v-icon class="me-4">mdi-list-box</v-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Business List
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
    </v-list-group>
</v-list>
<v-list dense nav shaped>
    <v-list-group>
        <template v-slot:activator>
            <v-icon class="me-4">mdi-shape</v-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Categories
                </v-list-item-title>
            </v-list-item-content>
        </template>
        <v-list-item href="{{route('admin.maincategories')}}">
            <v-icon class="me-4">mdi-shape-plus</v-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Main Categories
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-list-item href="{{route('admin.subcategories')}}">
            <v-icon class="me-4">mdi-shape</v-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Sub Categories
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
    </v-list-group>
</v-list>
<v-list dense nav shaped>
    <v-list-group>
        <template v-slot:activator>
            <v-icon class="me-4">mdi-briefcase</v-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Jobs
                </v-list-item-title>
            </v-list-item-content>
        </template>
        <v-list-item href="{{route('admin.jobcategories')}}">
            <v-icon class="me-4">mdi-shape</v-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Job Categories
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-list-item href="{{route('admin.jobtypes')}}">
            <v-icon class="me-4">mdi-call-merge</v-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Job Types
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-list-item href="{{route('admin.jobqualifications')}}">
            <v-icon class="me-4">mdi-equalizer</v-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Job Qualification
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
    </v-list-group>
</v-list>
<v-list dense nav shaped>
    <v-list-group>
        <template v-slot:activator>
            <v-icon class="me-4">mdi-map</v-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Locations
                </v-list-item-title>
            </v-list-item-content>
        </template>
        <v-list-item href="{{route('admin.country')}}">
            <v-icon class="me-4">mdi-city-variant</v-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Countries
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-list-item href="{{route('admin.state')}}">
            <v-icon class="me-4">mdi-city-variant</v-icon>
            <v-list-item-content>
                <v-list-item-title>
                    State
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-list-item href="{{route('admin.city')}}">
            <v-icon class="me-4">mdi-city</v-icon>
            <v-list-item-content>
                <v-list-item-title>
                    City
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
    </v-list-group>
</v-list>
<v-list dense nav shaped>
    <v-list-item-group>
        <v-list-item href="{{route('admin.reviews')}}">
            <v-icon class="me-4">mdi-file-star</v-icon>
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
        <v-list-item href="{{route('admin.reports')}}">
            <v-icon class="me-4">mdi-format-list-text</v-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Deal Reports
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
    </v-list-item-group>
</v-list>
<v-list dense nav shaped>
    <v-list-group>
        <template v-slot:activator>
            <v-icon class="me-4">mdi-cog</v-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Setting
                </v-list-item-title>
            </v-list-item-content>
        </template>
        <v-list-item href="{{route('admin.profile')}}">
            <v-list-item-content>
                <v-list-item-title>
                    <v-icon class="me-4">mdi-account-edit</v-icon>
                    Profile
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <!--          <router-link to="/admin/branding">-->
        <!--            <v-list-item>-->
        <!--              <v-list-item-content>-->
        <!--                <v-list-item-title>-->
        <!--                  <v-icon class="me-4">mdi-palette-swatch</v-icon>-->
        <!--                  Branding-->
        <!--                </v-list-item-title>-->
        <!--              </v-list-item-content>-->
        <!--            </v-list-item>-->
        <!--          </router-link>-->
        <v-list-item href="{{route('admin.plans')}}">
            <v-list-item-content>
                <v-list-item-title>
                    <v-icon class="me-4">mdi-floor-plan</v-icon>
                    Plans
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-list-item href="{{route('admin.registrationimage')}}">
            <v-icon class="me-4">mdi-image-check</v-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Registration Image
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-list-item href="{{route('admin.contactphone')}}">
            <v-icon class="me-4">mdi-card-account-phone</v-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Contact phone / Address
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <!--          <router-link to="/admin/payment/commission">-->
        <!--            <v-list-item>-->
        <!--              <v-list-item-content>-->
        <!--                <v-list-item-title>-->
        <!--                  <v-icon class="me-4">mdi-cash-100</v-icon>-->
        <!--                  Payment Commission-->
        <!--                </v-list-item-title>-->
        <!--              </v-list-item-content>-->
        <!--            </v-list-item>-->
        <!--          </router-link>-->
    </v-list-group>
</v-list>
<v-list dense nav shaped>
    <v-list-group>
        <template v-slot:activator>
            <v-icon class="me-4">mdi-image-size-select-actual</v-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Home Sliders
                </v-list-item-title>
            </v-list-item-content>
        </template>
        <v-list-item href="{{route('admin.ads')}}">
            <v-list-item-content>
                <v-list-item-title>
                    <v-icon class="me-4">mdi-advertisements</v-icon>
                    Top Ads
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-list-item href="{{route('admin.citydeals')}}">
            <v-list-item-content>
                <v-list-item-title>
                    <v-icon class="me-4">mdi-city</v-icon>
                    City Deals
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-list-item href="{{route('admin.catslider')}}">
            <v-list-item-content>
                <v-list-item-title>
                    <v-icon class="me-4">mdi-image-size-select-actual</v-icon>
                    Category Slider
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <!--            <v-list-item to="/admin/category/banner">-->
        <!--              <v-list-item-content>-->
        <!--                <v-list-item-title>-->
        <!--                  <v-icon class="me-4">mdi-image-size-select-actual</v-icon>-->
        <!--                  Category banner-->
        <!--                </v-list-item-title>-->
        <!--              </v-list-item-content>-->
        <!--            </v-list-item>-->
    </v-list-group>
</v-list>
<v-list dense nav shaped>
    <v-list-item-group>
        <v-list-item href="{{route('admin.pages')}}">
            <v-icon class="me-4">mdi-page-layout-sidebar-left</v-icon>
            <v-list-item-content>
                <v-list-item-title>
                    Pages
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
    </v-list-item-group>
</v-list>
{{--<v-list dense nav shaped>--}}
{{--    <v-list-item-group>--}}
{{--        <v-list-item to="/admin/pagecontent">--}}
{{--            <v-icon class="me-4">mdi-page-next</v-icon>--}}
{{--            <v-list-item-content>--}}
{{--                <v-list-item-title>--}}
{{--                    Page Content--}}
{{--                </v-list-item-title>--}}
{{--            </v-list-item-content>--}}
{{--        </v-list-item>--}}
{{--    </v-list-item-group>--}}
{{--</v-list>--}}
<v-list dense nav shaped>
    <user-logout></user-logout>
    <form id="logout-form" action="{{ route('user.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</v-list>

