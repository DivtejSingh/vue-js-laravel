<template>
    <div>
      <!-- Desktop heading -->
      <div class="desktop text-center">
            <span class="h4 cred">Deal Detail</span>
        </div>
        <div class="mobile text-center mb-2">
            <span class="h5 cred">Deal Detail</span>
        </div>

        <div class="text-center text-md-start">
            <v-btn color="red" @click="goBack" dark class="me-1"><v-icon>mdi-chevron-left</v-icon>Back</v-btn>
            <a href="#accordionContact">
                <v-btn color="success" class="me-1"><v-icon>mdi-map-marker</v-icon>Location</v-btn>
            </a>
        </div>
        <v-row>
            <v-col cols="12" md="6" class="pb-0 py-md-2">
                <v-tabs-items v-model="tabs" v-if="dealdetail.images.length > 0">
                    <v-tab-item v-for="(item,i) in dealdetail.images" :key="i" :value="'mobile-tabs-5-' + i">
                        <v-card flat class="mobile">
                            <v-img :src="(url+item.hotdeal_img_url)" cover max-height="200" min-height="200" class="img-fluid"></v-img>
                        </v-card>
                        <v-card flat class="desktop">
                            <v-img :src="(url+item.hotdeal_img_url)" cover max-height="350" min-height="350" class="img-fluid"></v-img>
                        </v-card>
                    </v-tab-item>
                </v-tabs-items>
                <div v-else>
                    <v-img :src="('https://dummyimage.com/350x350&text='+dealdetail.hot_deal_title+' No Image')"
                           cover max-height="350" min-height="350" class="img-fluid">
                    </v-img>
                </div>
                <v-tabs v-if="dealdetail.images.length > 0" v-model="tabs" fixed-tabs class="mt-2" show-arrows>
                    <v-tabs-slider></v-tabs-slider>
                    <v-tab v-for="(item,i) in dealdetail.images" :key="i" :href="('#mobile-tabs-5-'+i)" class="primary--text">
                        <v-img :src="(url+item.hotdeal_img_url)" cover max-width="75" height="75"></v-img>
                    </v-tab>
                </v-tabs>
            </v-col>
            <v-col cols="12" md="6">
                <div class="mobile">
                    <div class="small d-flex justify-content-between align-items-center">
                        <div class="px-md-5">
                            <h2 class="h5 mb-1">{{ dealdetail.hot_deal_title }} - {{ dealdetail.city }} | {{ dealdetail.name }}</h2>

                            <div>By <a :href="'/'+dealdetail.username" class="text-decoration-none">{{dealdetail.name}}</a>
                            </div>
                           <div v-if="loggedUser == false">
                                     <span class="me-1" @click="toggleLike">
                                      Add to Wishlist<v-icon>{{ 'mdi-heart-outline' }}</v-icon>
                                     </span>
                           </div>
                           <div v-else>
                                 <span v-if="dealdetail.user_hotdeal_wishlist!=null">
                                   <span class="me-1 wishlist-icon" @click="toggleLike">
                                     Add to Wishlist<v-icon :color="heartColor">
                                       {{ isLiked ? 'mdi-heart' : 'mdi-heart-outline' }}
                                     </v-icon>
                                   </span>
                                 </span>
                               <span v-else>
                           <span class="me-1 wishlist-icon" @click="toggleLike">
                             Add to Wishlist<v-icon :color="heartColor">{{ isLiked ? 'mdi-heart' : 'mdi-heart-outline' }}</v-icon>
                           </span>
                             </span>
                           </div>
                            <div><span class="cred fw-semibold cred">Price :</span> <span style="color: black;">₹{{dealdetail.price_to}}</span></div>
                            <div><span class="cred fw-semibold cred">Validity : </span> <span style="color: black;">{{dealdetail.date_to}}</span></div>
                            <div class="share cred fw-semibold">
                                Share :
                                <v-btn fab dark x-small color="blue" @click="shareOnFacebook" class="mx-2">
                                    <v-icon >mdi-facebook</v-icon>
                                </v-btn>
                                <v-btn fab dark x-small color="green" @click="shareOnWhatsApp">
                                    <v-icon>mdi-whatsapp</v-icon>
                                </v-btn>
                            </div>
                        </div>
                        <div class="mcontact deb">
                            <a :href="('https://wa.me/'+dealdetail.phonecode+dealdetail.mobile_phone+'?text=Enquiry from Bizlx : '+currentURL)">
                                <v-btn fab x-small outlined color="success"><v-icon>mdi-whatsapp</v-icon></v-btn>
                                <span class="small text-success">Whatsapp</span>
                            </a>
                            <a :href="'tel:+' + dealdetail.phonecode + dealdetail.mobile_phone" title="Call">
                                <v-btn fab x-small outlined color="primary"><v-icon>mdi-phone</v-icon></v-btn>
                                <span class="small text-primary">Call</span>
                            </a>
                        </div>
                    </div>
                    <div class="accordion mt-2" role="tablist">
                        <b-card no-body class="mb-1">
                            <b-card-header header-tag="header" class="p-1" role="tab">
                                <b-button class="w-100 catsy fw-bold cblu d-flex justify-content-between btn-sm"
                                          v-b-toggle.accordion-1 variant="light">Details</b-button>
                            </b-card-header>
                            <b-collapse id="accordion-1" visible accordion="my-accordion" role="tabpanel">
                                <b-card-body>
                                    <b-card-text class="text-justify">
                                        {{dealdetail.hot_deal_description}}
                                    </b-card-text>
                                </b-card-body>
                            </b-collapse>
                        </b-card>
                    </div>
                </div>
                <div class="desktop">
                    <div class="px-md-5">
                        <h2 class="h5 mb-1">{{ dealdetail.hot_deal_title }} - {{ dealdetail.city }} | {{ dealdetail.name }}</h2>

                        <div>By <a :href="'/'+dealdetail.username" class="text-decoration-none">{{dealdetail.name}}</a>
                        </div>
                       <div v-if="loggedUser == false">
                 <span class="me-1" @click="toggleLike">
                  Add to Wishlist<v-icon>{{ 'mdi-heart-outline' }}</v-icon>
                 </span>
                       </div>
                       <div v-else>
                 <span v-if="dealdetail.user_hotdeal_wishlist!=null">
                   <span class="me-1 wishlist-icon" @click="toggleLike">
                     Add to Wishlist<v-icon :color="heartColor">
                       {{ isLiked ? 'mdi-heart' : 'mdi-heart-outline' }}
                     </v-icon>
                   </span>
                 </span>
                           <span v-else>
                   <span class="me-1 wishlist-icon" @click="toggleLike">
                     Add to Wishlist<v-icon :color="heartColor">{{ isLiked ? 'mdi-heart' : 'mdi-heart-outline' }}</v-icon>
                   </span>
                 </span>
                       </div>
                        <div><span class="cred fw-semibold cred">Price :</span> <span style="color: black;">₹{{dealdetail.price_to}}</span></div>
                        <div><span class="cred fw-semibold cred">Validity : </span> <span style="color: black;">{{dealdetail.date_to}}</span></div>
                        <div class="py-1 d-flex">
                            <a :href="'tel:+' + dealdetail.phonecode + dealdetail.mobile_phone">
                                <v-btn color="red" dark class="me-1">
                                <v-icon>mdi-phone</v-icon>
                                Call Now</v-btn></a>
                            <a :href="('https://wa.me/'+dealdetail.phonecode+dealdetail.mobile_phone+'?text=Enquiry from Bizlx : '+currentURL)"><v-btn color="success" class="me-1"><v-icon>mdi-whatsapp</v-icon>WhatsApp</v-btn></a>
                            <v-speed-dial  v-model="fab" :direction="direction" :open-on-hover="hover" :transition="transition">
                                <template v-slot:activator>
                                    <v-btn v-model="hover" color="blue darken-2" dark>
                                        share
                                        <v-icon> mdi-share-variant </v-icon>
                                    </v-btn>
                                </template>
                                <v-btn fab dark x-small color="blue" @click="shareOnFacebook">
                                    <v-icon >mdi-facebook</v-icon>
                                </v-btn>
                                <v-btn fab dark x-small color="green" @click="shareOnWhatsApp">
                                    <v-icon>mdi-whatsapp</v-icon>
                                </v-btn>
                            </v-speed-dial>
                        </div>
                        <!--            <p class="px-1 text-justify">-->
                        <!--              {{dealdetail.hot_deal_description}}-->
                        <!--            </p>-->
                    </div>
                    <div class="accordion pt-2" role="tablist">
                        <b-card no-body class="mb-1  ">
                            <b-card-header header-tag="header" class="p-1" role="tab">
                                <b-button class="w-100 lh-1 catsy fw-bold cblu d-flex justify-content-between btn-sm"
                                          v-b-toggle.accordion-1 variant="light">Details</b-button>
                            </b-card-header>
                            <b-collapse id="accordion-1" visible accordion="my-accordion" role="tabpanel">
                                <b-card-body>
                                    <b-card-text class="text-justify">
                                        {{dealdetail.hot_deal_description}}
                                    </b-card-text>
                                </b-card-body>
                            </b-collapse>
                        </b-card>
                    </div>
                </div>
                <div id="accordionContact" class="mt-4">
                    <iframe
                        width="100%"
                        height="350"
                        frameborder="0" style="border:0"
                        referrerpolicy="no-referrer-when-downgrade"
                        :src=(mapurl+bname+space+dealdetail.city+space+dealdetail.state)
                        allowfullscreen>
                    </iframe>
                </div>
            </v-col>
        </v-row>
        <v-row class="mt-6 px-0 px-md-3">
            <h5 class="my-5"><span class="cblu bbred">Other Deals Near By</span></h5>
            <div class="mobile px-0">
                <carousel v-if="ddeals.length > 0" :autoplay="false" :margin="20" :items="4" :dots="false" :slideBy="1"
                          :navText="['‹','›']"
                          :responsive = "{0:{items:1},576:{items:2},768:{items:3},1200:{items:4}}" class="deals mobile">
                    <a v-for="(deal,i) in ddeals" :key="i" :href="('/deals/detail/'+deal.hotdeal_slug)" class="item mitem">
                        <v-card class="link-dark small d-flex">
                            <div class="mimg">
                                <v-img v-if="deal.hotdeal_single_image && deal.hotdeal_single_image.hotdeal_img_url" :src=url+deal.hotdeal_single_image.hotdeal_img_url cover height="110" class="rounded white--text align-start"></v-img>
                                <v-img v-else :src="('https://placehold.co/600x400?text='+deal.hot_deal_title)" cover height="110" class="white--text align-start"></v-img>
                            </div>
                            <div class="mdetails">
                                <div class="h6 mb-0">{{deal.hot_deal_title}}</div>
                                
                                <div>By:
                                    <a :href="'/'+deal.username" class="text-decoration-none">
                                        {{deal.name}}
                                    </a>
                                </div>
                                <div class="cred"><span>Price :</span><span style="color: black;">₹{{ deal.price_to }}</span></div>
                                <div class="fs11 cred"><span>Validity : </span><span style="color: black;">{{ deal.date_to }}</span></div>
                                <div><v-icon small>mdi-map-marker</v-icon>{{deal.city}}, {{deal.state}}</div>
                            </div>
                        </v-card>
                    </a>
                </carousel>
            </div>
            <div class="desktop">
                <carousel v-if="ddeals.length > 0" :autoplay="false" :margin="20" :items="4" :dots="false" :slideBy="1"
                          :navText="['‹','›']"
                          :responsive = "{0:{items:1},576:{items:2},768:{items:3},1200:{items:4}}" class="deals">
                    <a v-for="(deal,i) in ddeals" :key="i" :href="('/deals/detail/'+deal.hotdeal_slug)" class="item text-decoration-none">
                        <v-card >
                            <v-img v-if="deal.hotdeal_single_image && deal.hotdeal_single_image.hotdeal_img_url" :src=url+deal.hotdeal_single_image.hotdeal_img_url cover height="150" class="white--text align-start"></v-img>
                            <v-img v-else :src="('https://placehold.co/600x400?text='+deal.hot_deal_title)" cover height="150" class="white--text align-start"></v-img>
                            <v-card-text class="link-dark">
                                <div class="h6 mb-0" style="font-size: 15px;line-height: 18px;min-height: 36px;max-height: 36px;overflow: hidden;">{{deal.hot_deal_title}}</div>
                                <div style="max-height: 22px;overflow: hidden;">By:
                                    <a :href="'/'+deal.username" class="text-decoration-none">
                                        {{deal.name}}
                                    </a>
                                </div>
                                <div class="cred"><span>Price :</span><span style="color: black;">₹{{ deal.price_to }}</span></div>
                                <div class="cred"><span>Validity : </span> <span style="color: black;">{{ deal.date_to }}</span></div>
                                <div><v-icon small>mdi-map-marker</v-icon>{{deal.city}}, {{deal.state}}</div>
                            </v-card-text>
                        </v-card>
                    </a>
                </carousel>
            </div>
        </v-row>
        <v-snackbar v-model="snackbar" :timeout="timeout">{{ text }}
            <template v-slot:action="{}">
            <v-btn color="blue" text @click="snackbar = false">Close</v-btn>
            </template>
        </v-snackbar>
    </div>
</template>
<script>
import carousel from "vue-owl-carousel";
import axios from "axios";
export default {
    name: "DealdetailPage",
    components:{carousel},
    props:{
        id:Number,
        data1:Object
    },
    data() {
        return {
            mapurl:"https://www.google.com/maps/embed/v1/place?key=AIzaSyD1cGNhJz2BiG4oODjDAkfOH__dxXC_N10&q=",
            bname:'',
            space:' ',
            shareurl:'https://bizlx.com/deals/detail/',
            direction: "right",
            direction1: "left",
            fab: false,
            fling: true,
            hover: true,
            transition: "scale-transition",
            url:'https://bizlx.s3.ap-south-1.amazonaws.com',
            // url:'https://bizlx.s3.ap-south-1.amazonaws.com',
            dealdetail:[],
            tabs: null,
            ddeals: [],
            isLiked: false,
            loggedUser: false,
            userID: null,
            heartColor:'',
            timeout: '',
            snackbar: false,
            text: '',
            currentURL:'',
            hotdeal_slug:''
        };
    },
    created() {
        if(this.isAuthenticated()){
            this.loggedUser = this.isAuthenticated();
            this.userID = this.$store.state.userData.id;
        } else {
            this.loggedUser = false;
        }
        this.getDealdetail();
        this.currentURL = this.getCurrentURL();
        this.hotdeal_slug = this.data1.hotdeal_slug;
    },
    methods:{
        getCurrentURL() {
            return window.location.href;
        },
        shareOnFacebook() {
            const url = this.shareurl+this.hotdeal_slug;
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
        },
        shareOnWhatsApp() {
            // Create the WhatsApp share URL
            const url = this.shareurl+this.hotdeal_slug;
            const whatsappShareUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent(url)}`;
            window.open(whatsappShareUrl, '_blank');
        },
        getDealdetail() {
    axios.get('/api/deals/details/' + this.data1.hotdeal_slug + '/' + this.userID)
        .then((resp) => {
            this.dealdetail = resp.data.hotdeal;

            var rep = resp.data.hotdeal.name;
            this.bname = rep.replace('&', 'and');
            this.ddeals = resp.data.nearby;

            if (resp.data.hotdeal.user_hotdeal_wishlist != null) {
                this.isLiked = 'red';
                this.heartColor = 'red';
            }

            // ✅ Set document title here
            // if (this.dealdetail.hot_deal_title && this.dealdetail.city && this.dealdetail.name) {
            //     document.title = `${this.dealdetail.hot_deal_title} by ${this.dealdetail.name} in ${this.dealdetail.city} `;
            // }
            if (this.dealdetail.hot_deal_title && this.dealdetail.price_to && this.dealdetail.username && this.dealdetail.city) {
  document.title = `${this.dealdetail.hot_deal_title} Price Rs. ${this.dealdetail.price_to} - by ${this.dealdetail.username} in ${this.dealdetail.city} on Bizlx.com™`;
} else {
  document.title = 'Bizlx.com™';
}

// $title = $dealdetail->hot_deal_title.' Price Rs. '.$dealdetail->price_to.'- by '.$dealdetail->username.' in '.$dealdetail->city_name ;

        })
        .catch((error) => {
            console.error('Error fetching deal details:', error);
        });
},
        goBack() {
            window.history.back();
        },
        toggleLike() {
            if(this.loggedUser == false ){
                this.snackbar = false;
                window.location.href = '/user/login';
                this.timeout = 3000;
            }else{
                this.isLiked = !this.isLiked;
                let data = { 
                business_id: this.dealdetail.business_id,
                wishlist_type: 1,
                services_id: this.dealdetail.id,
                };
                if(this.isLiked==true){ // add 
                    axios.post("/api/add/wishlist", data)
                        .then((resp) => {
                            this.snackbar = true;
                            this.text = resp.data.message;
                            this.timeout = 3000;
                            this.isLiked = 'red';
                            this.heartColor = 'red';
                            this.getDealdetail();
                        }
                    );
                }
                if(this.isLiked==false){ // remove
                    axios.post("/api/add/wishlist", data)
                        .then((resp) => {
                            this.snackbar = true;
                            this.text = resp.data.message;
                            this.timeout = 3000;
                            this.isLiked = '';
                            this.heartColor = 'white';
                            this.getDealdetail();
                        }
                    );
                }
            }
        },
        isAuthenticated() {
            return this.$store.state.isAuthenticated;
        },
    }
}
</script>

<style scoped>
.v-application .wishlist-icon .white--text {
    color: rgba(0, 0, 0, 0.54) !important;
    }
.btn.catsy.collapsed::after {
    content: '+';
    color: var(--cblu);
    font-size: 20px;
    }
.btn.catsy.not-collapsed::after {
    content: '-';
    color: var(--cblu);
    font-size: 20px;
    }
.item.mitem {
    box-shadow: 0 6px 10px -6px rgba(0,0,0,0.5);
    background: #f1f2f380;
    padding: 7px;
    text-decoration: none;
    }
.mimg {
    width: 110px;
    padding: 4px;
    }
.mtitle {
    font-weight: 600;
    font-size: 12.25px;
    }
.mdetails {
    width: 100%;
    padding: 4px 0 0 20px;
    }
.mcontact {
    width: 42px;
    }
.heart-filled {
    color: red;
    }
.heart-unfilled {
    color: grey;
    }
.fs11{font-size: 10.4px;}
@media(max-width: 576px){
    .v-btn:not(.v-btn--round).v-size--default {
        height: 30px;
        min-width: 64px;
        padding: 0 7px;
        }
    .btn.catsy.collapsed::after {
        content: '+';
        color: var(--cblu);
        font-size: 10px;
        }
    .btn.catsy.not-collapsed::after {
        content: '-';
        color: var(--cblu);
        }
    }
</style>
