<template>
    <div>
        <v-app-bar app>
            <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
            <v-spacer></v-spacer>
             <!-- Display user name -->
             <div>Logged In as:<span v-if="auser.name" class="mr-3">{{ auser.name }}</span></div>
            <v-img v-if="auser.profile != null" :src="cdn+auser.profile.user_avatar" max-width="50px" width="50" height="50"></v-img>
            <v-img v-else :src="cdn+'images/logo.png'" max-width="50px" width="50" height="50"></v-img>
        </v-app-bar>
        <v-navigation-drawer v-model="drawer" fixed app>
            <v-list dense nav shaped>
                <v-list-item-group>
                    <v-list-item href="/">
                        <img src="https://bizlx.s3.ap-south-1.amazonaws.com/images/logo.png" class="img-fluid" width="50" alt="Bizlx">
                    </v-list-item>
                </v-list-item-group>
            </v-list>
            <span v-for="bmenu in bmenus" :key="bmenu.id">
                <span v-if="bmenu.role == user_role">
                    <v-list v-for="menu in bmenu.menus" :key="menu.id" dense nav shaped>
                        <v-list-item-group>
                            <v-list-item :href="menu.link">
                                <v-list-item-icon>
                                    <v-icon size="20">{{menu.icon}}</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{menu.title}}
                                    </v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list-item-group>
                    </v-list>
                </span>
            </span>
            <span v-if="user_role == 4">
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
                    <v-list-item href="/admin/publicusers">
                        <v-icon class="me-4">mdi-account-group</v-icon>
                        <v-list-item-content>
                            <v-list-item-title>
                                Public Users
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item href="/admin/subadmins">
                        <v-icon class="me-4">mdi-account-key</v-icon>
                        <v-list-item-content>
                            <v-list-item-title>
                                Sub Admins
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item href="/admin/resellers">
                        <v-icon class="me-4">mdi mdi-handshake</v-icon>
                        <v-list-item-content>
                            <v-list-item-title>
                                Resellers
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item href="/admin/businesses">
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
                    <v-list-item href="/admin/maincategories">
                        <v-icon class="me-4">mdi-shape-plus</v-icon>
                        <v-list-item-content>
                            <v-list-item-title>
                                Main Categories
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item href="/admin/subcategories">
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
                        <v-list-item href="/admin/jobcategories" exact>
                            <v-icon class="me-4">mdi-shape</v-icon>
                            <v-list-item-content>
                                <v-list-item-title>
                                    Job Categories
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item href="/admin/jobtypes" exact>
                            <v-icon class="me-4">mdi-call-merge</v-icon>
                            <v-list-item-content>
                                <v-list-item-title>
                                    Job Types
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item href="/admin/jobqualifications" exact >
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
                        <v-list-item href="/admin/country">
                            <v-icon class="me-4">mdi-city-variant</v-icon>
                            <v-list-item-content>
                                <v-list-item-title>
                                    Countries
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item href="/admin/state">
                            <v-icon class="me-4">mdi-city-variant</v-icon>
                            <v-list-item-content>
                                <v-list-item-title>
                                    State
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item href="/admin/city">
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
                        <v-list-item href="/admin/reviews">
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
                        <v-list-item href="/admin/reports">
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
                        <v-list-item href="/admin/profile">
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
                        <v-list-item href="/admin/plans">
                            <v-list-item-content>
                                <v-list-item-title>
                                    <v-icon class="me-4">mdi-floor-plan</v-icon>
                                    Plans
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item href="/admin/registration/image">
                            <v-icon class="me-4">mdi-image-check</v-icon>
                            <v-list-item-content>
                                <v-list-item-title>
                                    Registration Image
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item href="/admin/contact/phone">
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
                        <v-list-item href="/admin/ads">
                            <v-list-item-content>
                                <v-list-item-title>
                                    <v-icon class="me-4">mdi-advertisements</v-icon>
                                    Top Ads
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item href="/admin/citydeals">
                            <v-list-item-content>
                                <v-list-item-title>
                                    <v-icon class="me-4">mdi-city</v-icon>
                                    City Deals
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item href="/admin/catslider">
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
                        <v-list-item href="/admin/pages">
                            <v-icon class="me-4">mdi-page-layout-sidebar-left</v-icon>
                            <v-list-item-content>
                                <v-list-item-title>
                                    Pages
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list-item-group>
                </v-list>
            </span>
            <v-list v-if="user_role == 1" dense nav shaped>
                <v-list-item-group>
                    <v-list-item :href="'/'+auser.username">
                        <v-list-item-icon>
                            <v-icon size="20">mdi-web</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>
                                Preview Website
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list-item-group>
            </v-list>
            <v-list dense nav shaped v-if="user_role != 4">
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
                <!-- Hidden form for Laravel logout -->
                <form id="logout-form" action="/user/logout" method="POST" style="display: none;">
                    <input type="hidden" name="_token" :value="csrfToken" />
                </form>
            </v-list>
        </v-navigation-drawer>
    </div>
</template>
<script>
export default {
    name:"MenuBar",
    props:{
        auser:Object,
    },
    data(){
        return{
            cdn:"https://bizlx.s3.ap-south-1.amazonaws.com/",
            csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            drawer:true,
            user_role:'',
            bmenus:[
                {id:1,role:0, menus:[
                        {id:1,title:'User Profile',link:"/home",icon:"mdi-home"},
                        {id:2,title:'Wishlist',link:"/user/wishlist",icon:"mdi-cards-heart"},
                        {id:3,title:'Reviews',link:"/user/reviews",icon:"mdi-file-star"},
                    ]
                },
                {id:2,role:1, menus:[
                        {id:1,title:'User Profile',link:"/business/profile",icon:"mdi-badge-account"},
                        {id:2,title:'Cover Image',link:"/business/main-cover-image",icon:"mdi-image"},
                        {id:3,title:'Gallery Images',link:"/business/gallery-images",icon:"mdi-folder-multiple-image"},
                        {id:4,title:'Post Deals',link:"/business/deals",icon:"mdi-handshake"},
                        {id:5,title:'Wishlist',link:"/business/wishlist",icon:"mdi-cards-heart"},
                        {id:6,title:'Reviews',link:"/business/reviews",icon:"mdi-file-star"},
                        {id:7,title:'Reviews By Business',link:"/business/bybusiness",icon:"mdi-file-star"},
                        {id:8,title:'Billing & Plans',link:"/business/billing",icon:"mdi-book"},
                    ]
                },
                {id:3,role:2, menus:[
                        {id:1,title:'Clients',link:"/reseller/business-list",icon:"mdi-account-multiple"},
                        {id:2,title:'Wishlist',link:"/reseller/wishlist",icon:"mdi-cards-heart"},
                        {id:3,title:'Reviews',link:"/reseller/reviews",icon:"mdi-file-star"},
                        {id:4,title:'Profile',link:"/reseller/profile",icon:"mdi-account"},
                        {id:5,title:'Payment',link:"/reseller/payment",icon:"mdi-credit-card-outline"},
                    ]
                },
                {id:4,role:3, menus:[
                        {id:1,title:'User Profile',link:"/business/profile"}]
                },
                {id:5,role:4, menus:[
                        {id:1,title:'Dashboard',link:"/admin/dashboard",icon:"mdi-view-dashboard"},
                    ]
                },
                {id:6,role:21, menus:[
                        {id:1,title:'Clients',link:"/reseller/business-list",icon:"mdi-account-multiple"},
                        {id:2,title:'Business Profile',link:"/reseller/business/profile",icon:"mdi-badge-account"},
                        {id:3,title:'Cover Image',link:"/reseller/business/main-cover-image",icon:"mdi-image"},
                        {id:4,title:'Gallery Images',link:"/reseller/business/gallery-images",icon:"mdi-folder-multiple-image"},
                        {id:5,title:'Post Deals',link:"/reseller/business/deals",icon:"mdi-handshake"},
                        {id:6,title:'Reviews',link:"/reseller/business/reviews",icon:"mdi-file-star"},
                        {id:7,title:'Billing & Plans',link:"/reseller/business/billing",icon:"mdi-book"},
                    ]
                },
            ],
            rurl:'',
            bussid:null,
        }
    },
    created() {
        this.user_role = this.auser.user_role
        this.rurl = window.location.href;
        if(this.rurl.includes('/reseller/business/')){
            this.bussid = 1;
            this.user_role = 21;
        }
    }
}

</script>
<style scoped>

</style>
