<template>
  <v-container>
    <div class="desktop text-center">
      <span class="h4 cred">Sale Detail</span>
    </div>
    <div class="mobile text-center mb-2">
      <span class="h5 cred">Sale Detail</span>
    </div>
    <div class="text-center text-md-start">
      <v-btn color="red" dark @click="goBack" class="me-1"><v-icon>mdi-chevron-left</v-icon>Back</v-btn>
      <a href="#accordionContact">
        <v-btn color="success" class="me-1"><v-icon>mdi-map-marker</v-icon>Location</v-btn>
      </a>
    </div>
    <v-row>
      <!-- <v-col cols="12" md="6">
        <v-card flat class="desktop">
          <v-img v-if="sdetail.sale_imageurl==''" src="https://dummyimage.com/1500x1250/fd0606/fff&text=Sale" cover max-height="350" min-height="350" class="img-fluid"></v-img>
          <v-img v-if="sdetail.sale_imageurl!=''" :src=url+sdetail.sale_imageurl cover max-height="350" min-height="350" class="img-fluid"></v-img>
        </v-card>
        <v-card flat class="mobile">
          <v-img v-if="sdetail.sale_imageurl==''" src="https://dummyimage.com/1500x1250/fd0606/fff&text=Sale" cover max-height="150" min-height="150" class="img-fluid"></v-img>
          <v-img v-if="sdetail.sale_imageurl!=''" :src=url+sdetail.sale_imageurl cover max-height="150" min-height="150" class="img-fluid"></v-img>
        </v-card>
      </v-col> -->
      <v-col cols="12" md="6" class="pb-0 py-md-2">
  <!-- Image Tabs Items -->
  <v-tabs-items v-model="tabs" v-if="sdetail.sales_images.length > 0">
    <v-tab-item
      v-for="(item, i) in sdetail.sales_images"
      :key="i"
      :value="'mobile-tabs-5-' + i"
    >
      <!-- Mobile View Image -->
      <v-card flat class="mobile">
        <v-img
          :src="item.sale_img_url ? (url + item.sale_img_url) : 'https://dummyimage.com/350x200/cccccc/000000&text=No+Image'"
          cover
          max-height="200"
          min-height="200"
          class="img-fluid"
        />
      </v-card>

      <!-- Desktop View Image -->
      <v-card flat class="desktop">
        <v-img
          :src="item.sale_img_url ? (url + item.sale_img_url) : 'https://dummyimage.com/350x350/cccccc/000000&text=No+Image'"
          cover
          max-height="350"
          min-height="350"
          class="img-fluid"
        />
      </v-card>
    </v-tab-item>
  </v-tabs-items>

  <!-- If No Images Found -->
  <div v-else>
    <v-img
      :src="`https://dummyimage.com/350x350/cccccc/000000&text=${encodeURIComponent(sdetail.hot_deal_title || 'No Image')}`"
      cover
      max-height="350"
      min-height="350"
      class="img-fluid"
    />
  </div>

  <!-- Thumbnail Navigation Tabs -->
  <v-tabs
    v-if="sdetail.sales_images.length > 0"
    v-model="tabs"
    fixed-tabs
    class="mt-2"
    show-arrows
  >
    <v-tabs-slider></v-tabs-slider>
    <v-tab
      v-for="(item, i) in sdetail.sales_images"
      :key="i"
      :href="'#mobile-tabs-5-' + i"
      class="primary--text"
    >
      <v-img
        :src="item.sale_img_url ? (url + item.sale_img_url) : 'https://dummyimage.com/75x75/cccccc/000000&text=No+Img'"
        cover
        max-width="75"
        height="75"
      />
    </v-tab>
  </v-tabs>
</v-col>

      <v-col cols="12" md="6">
        <div class="mobile">
          <div class="small d-flex justify-content-between align-items-center">
            <div class="px-md-5">
              <h2 class="h5 mb-1">{{sdetail.sale_title}}</h2>
              <!-- <div>By <a :href="'/'+hotd.username""{name:'businessprofile',params:{id:sdetail.user_id,uname:sdetail.username}}" class="text-decoration-none" title="Business Name"> -->
                <div>By <a :href="'/'+sdetail.username" class="text-decoration-none" title="Business Name">
                {{sdetail.name}}</a>
              </div>
              <div class="wish">
                <span v-if="loggedUser == false">
                  <span class="me-1" @click="toggleLike">
                    Add to Wishlist<v-icon small>{{ 'mdi-heart-outline' }}</v-icon>
                  </span>
                </span>
                <span v-else>
                  <span v-if="sdetail.user_sale_wishlist!=null">
                    <span class="me-1 wishlist-icon" @click="toggleLike">
                      Add to Wishlist<v-icon small :color="heartColor">
                        {{ isLiked ? 'mdi-heart' : 'mdi-heart-outline' }}
                      </v-icon>
                    </span>
                  </span>
                  <span v-else>
                    <span class="me-1 wishlist-icon" @click="toggleLike">
                      Add to Wishlist<v-icon small :color="heartColor">{{ isLiked ? 'mdi-heart' : 'mdi-heart-outline' }}</v-icon>
                    </span>
                  </span>
                </span>
              </div>
              <div><span class="cred fw-semibold">Price :</span> <del class=" ms-1 h6 text-muted">₹{{ sdetail.normal_price }}</del> -  <span class=" mx-1 h4">₹{{ sdetail.sale_price }}</span>
               {{Math.round(100-(100*sdetail.sale_price/sdetail.normal_price))}}% off
              </div>
              <div><span class="cred fw-semibold">Validity : </span> <span style="color: black;">{{ sdetail.saledate_to }}</span></div>
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
              <a :href="('https://wa.me/'+sdetail.phonecode+sdetail.mobile_phone+'?text=Enquiry from Bizlx : '+currentURL)">
                <v-btn fab x-small outlined color="success" class="mb-2"><v-icon>mdi-whatsapp</v-icon></v-btn>
                <span class="small text-success">Whatsapp</span>
              </a>
              <a :href="'tel:+' + sdetail.phonecode + sdetail.mobile_phone">
                <v-btn fab x-small outlined color="primary"><v-icon>mdi-phone</v-icon></v-btn>
                <span class="small text-primary">Call</span>
              </a>
              <!-- <v-speed-dial class="my-2" :direction="direction1" :open-on-hover="hover" :transition="transition">
                <template v-slot:activator>
                  <v-btn fab small v-model="hover" color="blue darken-2" dark>
                    <v-icon> mdi-share-variant </v-icon>
                  </v-btn>
                </template>
                <v-btn fab dark x-small color="blue" @click="shareOnFacebook">
                  <v-icon >mdi-facebook</v-icon>
                </v-btn>
                <v-btn fab dark x-small color="green" @click="shareOnWhatsApp">
                  <v-icon>mdi-whatsapp</v-icon>
                </v-btn> -->
                <!--              <v-btn fab dark x-small color="primary" @click="shareOnLinkedIn">-->
                <!--                <v-icon>mdi-linkedin</v-icon>-->
                <!--              </v-btn>-->
                <!--              <v-btn fab dark x-small color="cyan" @click="shareOnTwitter">-->
                <!--                <v-icon>mdi-twitter</v-icon>-->
                <!--              </v-btn>-->
              <!-- </v-speed-dial> -->
            </div>
          </div>
          <div class="accordion py-2" role="tablist">
            <b-card no-body class="mb-1">
              <b-card-header header-tag="header" class="p-1" role="tab">
                <b-button class="w-100 catsy fw-bold cblu d-flex justify-content-between btn-sm"
                          v-b-toggle.accordion-1 variant="light">Details</b-button>
              </b-card-header>
              <b-collapse id="accordion-1" visible accordion="my-accordion" role="tabpanel">
                <b-card-body>
                  <b-card-text class="text-justify">
                    {{ sdetail.sale_description }}
                  </b-card-text>
                </b-card-body>
              </b-collapse>
            </b-card>
          </div>
          <div id="accordionContact">
            <iframe
                width="100%"
                height="350"
                frameborder="0" style="border:0"
                referrerpolicy="no-referrer-when-downgrade"
                :src=(mapurl+bname+space+sdetail.city+space+sdetail.state)
                allowfullscreen>
            </iframe>
          </div>
        </div>
        <div class="desktop">
          <div class="px-md-5">
            <h2 class="h4 mb-0">{{sdetail.sale_title}}</h2>
            <div>By
              <!-- <a :href="{name:'businessprofile',params:{id:sdetail.user_id,uname:sdetail.username}}" class="text-decoration-none" title="Business Name"> -->
                <a :href="'/'+sdetail.username" class="text-decoration-none" title="Business Name">
                {{sdetail.name}}
              </a>
            </div>
            <div class="wish">
                <span v-if="loggedUser == false">
                  <span class="me-1" @click="toggleLike">
                    Add to Wishlist<v-icon>{{ 'mdi-heart-outline' }}</v-icon>
                  </span>
                </span>
              <span v-else>
                  <span v-if="sdetail.user_sale_wishlist!=null">
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
                </span>
            </div>
            <div class="cred">Sale
              <del class=" ms-1 h6 text-muted" style="color: black;">₹{{ sdetail.normal_price }}</del>
              <span class=" mx-1 h4 " style="color: black;">₹{{ sdetail.sale_price }}</span>
              <span class="text-bg-light px-2 py-1 fs-6">{{Math.round(100-(100*sdetail.sale_price/sdetail.normal_price))}}% off</span>
            </div>
            <div><span class="fw-semibold cred">Validity: <span style="color:black">{{ sdetail.saledate_to }}</span></span></div>
            <div class="py-1 d-flex">
              <a :href="'tel:+' + sdetail.phonecode + sdetail.mobile_phone">
                <v-btn color="red" dark class="me-1">
                  <v-icon>mdi-phone</v-icon>
                  Call Now
                </v-btn>
              </a>
              <a :href="('https://wa.me/'+sdetail.phonecode+sdetail.mobile_phone+'?text=Enquiry from Bizlx : '+currentURL)"><v-btn color="success" class="me-1"><v-icon>mdi-whatsapp</v-icon>WhatsApp</v-btn></a>
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
          </div>
          <div class="accordion py-2 px-5" role="tablist">
            <b-card no-body class="mb-1">
              <b-card-header header-tag="header" class="p-1" role="tab">
                <b-button class="w-100 catsy fw-bold cblu d-flex justify-content-between btn-sm"
                          v-b-toggle.accordion-1 variant="light">Details</b-button>
              </b-card-header>
              <b-collapse id="accordion-1" visible accordion="my-accordion" role="tabpanel">
                <b-card-body>
                  <b-card-text class="text-justify">
                    {{ sdetail.sale_description }}
                  </b-card-text>
                </b-card-body>
              </b-collapse>
            </b-card>
          </div>
          <div id="accordionContact">
            <iframe
                width="100%"
                height="350"
                frameborder="0" style="border:0"
                referrerpolicy="no-referrer-when-downgrade"
                :src=(mapurl+bname+space+sdetail.city+space+sdetail.state)
                allowfullscreen>
            </iframe>
          </div>

        </div>
      </v-col>
    </v-row>

    <v-snackbar v-model="snackbar" :timeout="timeout">{{ text }}
      <template v-slot:action="{}">
        <v-btn color="blue" text v-bind="attrs" @click="snackbar = false">Close</v-btn>
      </template>
    </v-snackbar>

  </v-container>
</template>

<script>
import axios from "axios";

export default {
  name: "SaleDetail",
  props:{
    id:Number,
    data1:Object
  },
  data(){
    return{
      mapurl:"https://www.google.com/maps/embed/v1/place?key=AIzaSyD1cGNhJz2BiG4oODjDAkfOH__dxXC_N10&q=",
      bname:'',
      space:' ',
      shareurl:'https://bizlx.com/sales/detail/',
      url:'https://bizlx.s3.ap-south-1.amazonaws.com',
      sdetail:[],
      tabs: null,
      isLiked: false,
      loggedUser: false,
      userID: null,
      timeout: '',
      snackbar: false,
      text: '',
      direction: "right",
      direction1: "left",
      fab: false,
      fling: true,
      hover: true,
      sale_slug:'',
      transition: "scale-transition",
      currentURL:'',
    }
  },
  created() {
    if(this.isAuthenticated()){
        this.loggedUser = this.isAuthenticated();
        this.userID = this.$store.state.userData.id;
    } else {
        this.loggedUser = false;
    }
    this.getSaleDetail();
    this.currentURL = this.getCurrentURL();
    this.sale_slug = this.data1.sale_slug;
  },
  methods:{
    getCurrentURL() {
      return window.location.href;
    },

    shareOnFacebook() {
      const url = this.shareurl+this.sale_slug;
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
    },
    shareOnWhatsApp() {
      // Create the WhatsApp share URL
      const url = this.shareurl+this.sale_slug;
      const whatsappShareUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent(url)}`;
      window.open(whatsappShareUrl, '_blank');
    },

    getSaleDetail(){
      axios.get('/api/sales/detail/'+this.data1.sale_slug+'/'+this.userID)
        .then((resp)=>{
            this.sdetail = resp.data.sdetail;
            var rep = resp.data.sdetail.name;
            this.bname = rep.replace('&','and');
            if(resp.data.sdetail.user_sale_wishlist!=null){
              this.isLiked = 'red';
              this.heartColor = 'red';
            }
        });
    },
    goBack() {
      window.history.back();
    },

    toggleLike() {
      if(!this.loggedUser){
          this.snackbar = false;
          window.location.href = '/user/login';
          this.timeout = 3000;
      }else{
        this.isLiked = !this.isLiked;
        let data = {
          business_id: this.sdetail.business_id,
          wishlist_type: 2,
          services_id: this.sdetail.id,
        };
        if(this.isLiked==true){ // add
            axios.post("/api/add/wishlist", data)
                .then((resp) => {
                  this.snackbar = true;
                  this.text = resp.data.message;
                  this.timeout = 3000;

                  this.isLiked = 'red';
                  this.heartColor = 'red';
                  this.getSaleDetail();
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
                  this.getSaleDetail();
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
.mcontact {
  width: 42px;
  }
.heart-filled {
  color: red;
}
.heart-unfilled {
  color: grey;
}
@media(max-width: 576px){
  .v-btn:not(.v-btn--round).v-size--default {
    height: 30px;
    min-width: 64px;
    padding: 0 7px;
    }
  .btn.catsy.collapsed::after {
    content: '+';
    color: var(--cblu);
    }
  .btn.catsy.not-collapsed::after {
    content: '-';
    color: var(--cblu);
    }
  }
</style>
