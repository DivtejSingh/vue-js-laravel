@extends('layouts.userapp')

@section('content')
    <div id="app">
        <edit-business :business_id="{{ $business_id }}"></edit-business>
    </div>
@endsection

<!-- Sidebar Items for Different Routes -->
@if (request()->is('reseller/update/business-list'))
    <v-list dense nav shaped>
        <v-list-item-group>
            <v-list-item href="{{ route('reseller.dashboard') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-badge-account</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Clients</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </v-list-item-group>
    </v-list>
    <v-list dense nav shaped>
        <v-list-item-group>
            <v-list-item href="{{ route('reseller.wishlist') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-cards-heart</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Wishlist</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </v-list-item-group>
    </v-list>
    <v-list dense nav shaped>
        <v-list-item-group>
            <v-list-item href="{{ route('reseller.reviews') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-file-star</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Reviews</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </v-list-item-group>
    </v-list>
    <v-list dense nav shaped>
        <v-list-item-group>
            <v-list-item href="{{ route('reseller.payment') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-file-star</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Payments</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </v-list-item-group>
    </v-list>
    <v-list dense nav shaped>
        <v-list-group prepend-icon="mdi-cog" value="true">
            <template v-slot:activator>
                <v-list-item>
                    <v-list-item-content>
                        <v-list-item-title>Settings</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </template>
            <v-list-item href="{{ route('reseller.profile') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-account</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Profile</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </v-list-group>
    </v-list>

@elseif (request()->is('reseller/business/profile'))
    <v-list dense nav shaped>
        <v-list-item-group>
            <v-list-item href="{{ route('rbusiness.dashboard') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-badge-account</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Profile Info</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item href="{{ route('business.coverimage') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-image</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Main Cover Picture</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item href="{{ route('business.galleryimage') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-image</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Gallery Images</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item href="{{ route('business.deals') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-handshake</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Post Deals</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item href="{{ route('business.reviews') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-handshake</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Reviews</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
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

@elseif (request()->is('business/main-cover-image'))
    <v-list dense nav shaped>
        <v-list-item-group>
            <v-list-item href="{{ route('rbusiness.dashboard') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-home</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Profile Info</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item href="{{ route('business.coverimage') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-image</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Main Cover Picture</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item href="{{ route('business.galleryimage') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-image</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Gallery Images</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item href="{{ route('business.deals') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-handshake</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Post Deals</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item href="{{ route('business.reviews') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-handshake</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Reviews</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
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

    @elseif (request()->is('business/gallery-images'))
    <v-list dense nav shaped>
        <v-list-item-group>
            <v-list-item href="{{ route('rbusiness.dashboard') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-home</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Profile Info</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item href="{{ route('business.galleryimage') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-image</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Main Cover Picture</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item href="{{ route('business.galleryimage') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-image</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Gallery Images</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item href="{{ route('business.deals') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-handshake</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Post Deals</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item href="{{ route('business.reviews') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-handshake</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Reviews</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
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

@elseif (request()->is('business/deals'))
    <v-list dense nav shaped>
        <v-list-item-group>
            <v-list-item href="{{ route('rbusiness.dashboard') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-home</v-icon>
                </v-list-item-icon>
                <v-list-item-content> 
                    <v-list-item-title>Profile Info</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item href="{{ route('business.coverimage') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-image</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Main Cover Picture</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item href="{{ route('business.galleryimage') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-image</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Gallery Images</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item href="{{ route('business.deals') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-handshake</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Post Deals</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item href="{{ route('business.reviews') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-handshake</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Reviews</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
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
@elseif (request()->is('business/reviews'))
    <v-list dense nav shaped>
        <v-list-item-group>
            <v-list-item href="{{ route('rbusiness.dashboard') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-home</v-icon>
                </v-list-item-icon>
                <v-list-item-content> 
                    <v-list-item-title>Profile Info</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item href="{{ route('business.coverimage') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-image</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Main Cover Picture</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item href="{{ route('business.galleryimage') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-image</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Gallery Images</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item href="{{ route('business.deals') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-handshake</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Post Deals</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item href="{{ route('business.reviews') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-handshake</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Reviews</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
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
 @elseif (request()->is('business/billing'))
 <v-list dense nav shaped>
        <v-list-item-group>
            <v-list-item href="{{ route('rbusiness.dashboard') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-badge-account</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Profile Info</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item href="{{ route('business.coverimage') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-image</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Main Cover Picture</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item href="{{ route('business.galleryimage') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-image</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Gallery Images</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item href="{{ route('business.deals') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-handshake</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Post Deals</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item href="{{ route('business.reviews') }}">
                <v-list-item-icon>
                    <v-icon size="20">mdi-handshake</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Reviews</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
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
@endif

<!-- Common Sidebar Items -->
<v-list dense nav shaped>
    <v-list-item-group>
        <v-list-item href="https://play.google.com/store/apps/details?id=com.bizlxapp" target="_blank">
            <v-list-item-icon>
                <v-icon size="20">mdi-progress-download</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
                <v-list-item-title>Download App</v-list-item-title>
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
