<template>
    <div class="pa-2">
      <h4 class="ms-4">Wishlist</h4>
      <v-card-text class="py-0">
              <v-row>
                <v-col cols="2" md="2" class=""></v-col>
                <v-col cols="8" md="8" class=""><h6>Name</h6></v-col>
                <v-col cols="2" md="2" class=""><h6>Action</h6></v-col>
              </v-row>
            </v-card-text>
              <div v-if="rlists.length > 0">
                <v-card class="mb-3" v-for="list in rlists" :key="list.name" :perPage="perPage">
                <!-- hot deal -->
                <v-card-text v-if="list.wishlist_type==1">
                    <v-row >
                      <v-col cols="2" md="2">
                          <div v-if="list.hotdeals_images.hotdeal_img_url!=null">
                                <v-img height="60" :src="url+list.hotdeals_images.hotdeal_img_url" class="img-fluid d-block d-sm-none"></v-img>
                                <v-img height="120" :src="url+list.hotdeals_images.hotdeal_img_url" class="img-fluid d-none d-md-block d-sm-block"></v-img>
                          </div>
                          <div v-if="list.hotdeals_images.hotdeal_img_url==null">
                                <v-img height="60" :src="('https://placehold.co/60?text=')+list.hot_deal_title" class="img-fluid d-block d-sm-none"></v-img>
                                <v-img height="120" :src="('https://placehold.co/120?text=')+list.hot_deal_title" class="img-fluid d-none d-md-block d-sm-block"></v-img>
                          </div>
                      </v-col>
  
                      <v-col cols="8" md="8">
                          <div class="d-block d-sm-none">
                            <h6 class="link-dark mb-0">{{list.hot_deal_title}}</h6>
                            <div>By: <a :href= "'/'+list.buser_username" target="_blank">{{list.buser_name }}</a></div>
                            <div>Category: {{list.subcat_name}}</div>
                            <div><v-icon small>mdi-map-marker</v-icon>{{list.city}},{{list.state}}</div>
                            <div>
                              <a :href= "'/deals/detail/'+list.hotdeal_slug" target="_blank">Visit Now</a>
                            </div>
                            <div v-if="list.wishlist_type==1">Hot Deal</div>
                          </div>
                          <div class="d-none d-md-block d-sm-block">
                            <h5 class="link-dark">{{list.hot_deal_title}}</h5>
                            <div>By: <a :href= "'/'+list.buser_username" target="_blank">{{list.buser_name }}</a></div>
                            <div>Category: {{list.subcat_name}}</div>
                            <span><v-icon small>mdi-map-marker</v-icon>{{list.city}},{{list.state}}</span>
                            <div>
                              <a :href= "'/deals/detail/'+list.hotdeal_slug" target="_blank">Visit Now</a>
                            </div>
                            <div v-if="list.wishlist_type==1">Hot Deal</div>
                          </div>
                      </v-col>
                      <v-col cols="2" md="2" @click="wishlistRemove(list.wishlist_id)">
                          <v-icon class="d-block d-md-none cblu" size="24">mdi-delete</v-icon>
                          <v-btn class="d-none d-md-block bgred text-light btncont">
                          <v-icon size="20">mdi-trash-can-outline</v-icon> Remove</v-btn>
                      </v-col>
                    </v-row>
                  </v-card-text>
                <!-- Sale -->
                  <v-card-text v-if="list.wishlist_type==2">
                    <v-row >
                      <v-col cols="2" md="2">
                          <div v-if="list.sale_imageurl!=null">
                                <v-img height="60" :src="url+list.sale_imageurl" class="img-fluid d-block d-sm-none"></v-img>
                                <v-img height="120" :src="url+list.sale_imageurl" class="img-fluid d-none d-md-block d-sm-block"></v-img>
                          </div>
                          <div v-if="list.sale_imageurl==null">
                                <v-img height="60" :src="('https://placehold.co/60?text=')+list.sale_title" class="img-fluid d-block d-sm-none"></v-img>
                                <v-img height="120" :src="('https://placehold.co/120?text=')+list.sale_title" class="img-fluid d-none d-md-block d-sm-block"></v-img>
                          </div>
                      </v-col>
                      <v-col cols="8" md="8">
                          <div class="d-block d-sm-none">
                            <h6 class="link-dark mb-0">{{list.sale_title}}</h6>
                             <div>By: <a :href= "'/'+list.buser_username" target="_blank">{{list.buser_name }}</a></div>
                            <div>Category: {{list.subcat_name}}</div>
                            <div><v-icon small>mdi-map-marker</v-icon>{{list.city}},{{list.state}}</div>
                            <div>
                              <a :href= "'/sales/detail/'+list.sale_slug" target="_blank">Visit Now</a>
                            </div>
                            <div v-if="list.wishlist_type==2">Sale</div>
                          </div>
                          <div class="d-none d-md-block d-sm-block">
                            <h5 class="link-dark">{{list.sale_title}}</h5>
                             <div>By: <a :href= "'/'+list.buser_username" target="_blank">{{list.buser_name }}</a></div>
                            <div>Category: {{list.subcat_name}}</div>
                            <span><v-icon small>mdi-map-marker</v-icon>{{list.city}},{{list.state}}</span>
                            <div>
                              <a :href= "'/sales/detail/'+list.sale_slug" target="_blank">Visit Now</a>
                            </div>
                            <div v-if="list.wishlist_type==2">Sale</div>
                          </div>
                      </v-col>
                      <v-col cols="2" md="2" @click="wishlistRemove(list.wishlist_id)">
                          <v-icon class="d-block d-md-none cblu" size="24">mdi-delete</v-icon>
                          <v-btn class="d-none d-md-block bgred text-light btncont">
                          <v-icon size="20">mdi-trash-can-outline</v-icon> Remove</v-btn>
                      </v-col>
                    </v-row>
                  </v-card-text>
                <!-- Jobs -->
                  <v-card-text v-if="list.wishlist_type==3">
                    <v-row >
                      <v-col cols="2" md="2">
                        <v-img height="120" :src="picd+list.job_id" class="img-fluid d-none d-md-block d-sm-block"></v-img>
  
                          <!-- <div v-if="list.user_avatar!=null">
                                <v-img height="60" :src="url+list.user_avatar" class="img-fluid d-block d-sm-none"></v-img>
                                <v-img height="120" :src="url+list.user_avatar" class="img-fluid d-none d-md-block d-sm-block"></v-img>
                          </div>
                          <div v-if="list.user_avatar==null">
                                <v-img height="60" :src="('https://placehold.co/60?text=')+list.job_title" class="img-fluid d-block d-sm-none"></v-img>
                                <v-img height="120" :src="('https://placehold.co/120?text=')+list.job_title" class="img-fluid d-none d-md-block d-sm-block"></v-img>
                          </div> -->
                      </v-col>
                      <v-col cols="8" md="8">
                          <div class="d-block d-sm-none">
                            <h6 class="link-dark mb-0">{{list.job_title}}</h6>
                            <div>By: <a :href= "'/'+list.buser_username" target="_blank">{{list.buser_name }}</a></div>
                             <div>Category: {{list.job_cat_name}}</div>
                            <div><v-icon small>mdi-map-marker</v-icon>{{list.city}},{{list.state}}</div>
                            <div>
                              <a :href= "'/jobs/detail/'+list.job_slug" target="_blank">Visit Now</a>
                            </div>
                            <div v-if="list.wishlist_type==3">Job</div>
                          </div>
                          <div class="d-none d-md-block d-sm-block">
                            <h5 class="link-dark">{{list.job_title}}</h5>
                            <div>By: <a :href= "'/'+list.buser_username" target="_blank">{{list.buser_name }}</a></div>
                             <div>Category: {{list.job_cat_name}}</div>
                            <span><v-icon small>mdi-map-marker</v-icon>{{list.city}},{{list.state}}</span>
                            <div>
                              <a :href= "'/jobs/detail/'+list.job_slug" target="_blank">Visit Now</a>
                            </div>
                            <div v-if="list.wishlist_type==3">Job</div>
                          </div>
                      </v-col>
                      <v-col cols="2" md="2" @click="wishlistRemove(list.wishlist_id)">
                          <v-icon class="d-block d-md-none cblu" size="24">mdi-delete</v-icon>
                          <v-btn class="d-none d-md-block bgred text-light btncont">
                          <v-icon size="20">mdi-trash-can-outline</v-icon> Remove</v-btn>
                      </v-col>
                    </v-row>
                  </v-card-text>
                  <!-- Videos -->
                  <v-card-text v-if="list.wishlist_type==4">
                    <v-row >
                      <v-col cols="2" md="2">
                          <div v-if="list.youtube_id!=null">
                              <iframe :src="('https://www.youtube.com/embed/'+list.youtube_id)" height="120" allowfullscreen class="img-fluid d-none d-md-block d-sm-block"></iframe>
                              <iframe :src="('https://www.youtube.com/embed/'+list.youtube_id)" height="60" allowfullscreen class="img-fluid d-block d-sm-none"></iframe>
                          </div>
                          <div v-if="list.video_url==null">
                            <v-img :src="('https://placehold.co/600x400?text='+list.video_title)+' No video'" height="60" class="img-fluid d-block d-sm-none"></v-img>
                            <v-img :src="('https://placehold.co/600x400?text='+list.video_title)+' No video'" height="120" class="img-fluid d-none d-md-block d-sm-block"></v-img>
                          </div>
                      </v-col>
                      <v-col cols="8" md="8">
                          <div class="d-block d-sm-none">
                            <h6 class="link-dark mb-0">{{list.video_title}}</h6>
                              <div>By: <a :href= "'/'+list.buser_username" target="_blank">{{list.buser_name }}</a></div>
                            <p><v-icon small>mdi-map-marker</v-icon>{{list.city}},{{list.state}}</p>
                            <div>
                              <a :href= "'/videos/detail/'+list.video_slug" target="_blank">Visit Now</a>
                            </div>
                            <div v-if="list.wishlist_type==4">Video</div>
                          </div>
                          <div class="d-none d-md-block d-sm-block">
                            <h5 class="link-dark">{{list.video_title}}</h5>
                              <div>By: <a :href= "'/'+list.buser_username" target="_blank">{{list.buser_name }}</a></div>
                            <span><v-icon small>mdi-map-marker</v-icon>{{list.city}},{{list.state}}</span>
                            <div>
                              <a :href= "'/videos/detail/'+list.video_slug" target="_blank">Visit Now</a>
                            </div>
                            <div v-if="list.wishlist_type==4">Video</div>
                          </div>
                      </v-col>
                      <v-col cols="2" md="2" @click="wishlistRemove(list.wishlist_id)">
                          <v-icon class="d-block d-md-none cblu" size="24">mdi-delete</v-icon>
                          <v-btn class="d-none d-md-block bgred text-light btncont">
                          <v-icon size="20">mdi-trash-can-outline</v-icon> Remove</v-btn>
                      </v-col>
                    </v-row>
                 
  
                  </v-card-text>
                  <!-- Business -->
                  <v-card-text v-if="list.wishlist_type==5">
                    <v-row >
                      <v-col cols="2" md="2">
                          <div v-if="list.user_avatar!=null">
                                <v-img height="60" :src="url+list.user_avatar" class="img-fluid d-block d-sm-none"></v-img>
                                <v-img height="120" :src="url+list.user_avatar" class="img-fluid d-none d-md-block d-sm-block"></v-img>
                          </div>
                          <div v-if="list.user_avatar==null">
                                <v-img height="60" :src="('https://placehold.co/60?text=')+list.buser_name" class="img-fluid d-block d-sm-none"></v-img>
                                <v-img height="120" :src="('https://placehold.co/120?text=')+list.buser_name" class="img-fluid d-none d-md-block d-sm-block"></v-img>
                          </div>
                      </v-col>
                      <v-col cols="8" md="8">
                          <div class="d-block d-sm-none">
                            <h6 class="link-dark mb-0">{{list.buser_name}}</h6>
                             <div>Category: {{list.subcat_name}}</div>
                            <div><v-icon small>mdi-map-marker</v-icon>{{list.city}},{{list.state}}</div>
                            <div>
                              <a :href= "'/'+list.buser_username" target="_blank">Visit Now</a>
                            </div>
                            <div v-if="list.wishlist_type==5">Business</div>
                          </div>
                          <div class="d-none d-md-block d-sm-block">
                            <h5 class="link-dark">{{list.buser_name}}</h5>
                             <div>Category: {{list.subcat_name}}</div>
                            <span><v-icon small>mdi-map-marker</v-icon>{{list.city}},{{list.state}}</span>
                            <div>
                              <a :href= "'/'+list.buser_username" target="_blank">Visit Now</a>
                            </div>
                            <div v-if="list.wishlist_type==5">Business</div>
                          </div>
                      </v-col>
                      <v-col cols="2" md="2" @click="wishlistRemove(list.wishlist_id)">
                          <v-icon class="d-block d-md-none cblu" size="24">mdi-delete</v-icon>
                          <v-btn class="d-none d-md-block bgred text-light btncont">
                          <v-icon size="20">mdi-trash-can-outline</v-icon> Remove</v-btn>
                      </v-col>
                    </v-row>
                  </v-card-text>
                </v-card>
                <div class="text-start my-4">
                  <v-pagination v-if="rlists.length > 1" class="pagination pt-3" v-model="page" :length="Math.ceil(wishlist.length/perPage)" circle></v-pagination>
                </div>
              </div>
              <div v-else class="text-center mt-8"><p>Data not found.</p></div>
        <v-snackbar v-model="snackbar" :timeout="timeout">{{ text }}
          <template v-slot:action="{}">
            <v-btn color="blue" text @click="snackbar = false">Close</v-btn>
          </template>
        </v-snackbar>
  
    </div>
  </template>
  <script>
  import axios from 'axios';
  export default {
    name: 'ResellerBusinessWishlist',
    components: {},
    metaInfo: {title: 'Bizlx Business Wishlist'},
    data() {
      return {
        picd:'https://source.unsplash.com/random/400x200?sig=',
        page:1,
        perPage:10,
        wishlist: [],
        url:'https://bizlx.s3.ap-south-1.amazonaws.com',
        snackbar: false,
        text: '',
        timeout: -1,
        userdata: [],
        headers:[
          {text:'WID',value:'wishlist_id'},
          {text:'Type',value:'wishlist_type'},
          {text:'sid',value:'services_id'},
          {text:'id',value:'id'},
        ]
      };
    },
    mounted() {
     this.getWishlist();
    },

    created() {
        const userId = this.user_id(); // Retrieve user ID
        this.userdata = { id: userId }; // Store in `userdata`
        console.log('Userdata:', this.userdata); // Debugging
        if (this.userdata.id) {
            this.getWishlistbyreseller(); // Call API only if userId exists
        } else {
            console.warn('User ID is missing. Skipping API call.');
        }
    },
    methods:{
    //   user_id() {
    //     return this.$store.state.userId;
    //   },
      user_id() {
        const url = window.location.href;
        const lastSegment = url.split('/').filter(Boolean).pop();
        return lastSegment;
      },
      getWishlist() {
          axios.post('/api/businesses/wishlists/getbyadmin',{userId:this.user_id()})
          .then((resp) =>{
              this.wishlist = resp.data.wishlists;
              console.log('fghjkl4444',this.wishlist);
            }
          );
      },

    getWishlistbyreseller() {
            axios.post('/api/businesses/wishlists/getbyreseller',{userId:this.user_id()})
            .then((resp) =>{
                this.wishlist = resp.data.wishlists;
                console.log('fghnjk----',this.wishlist);
            }
            );
        },

      wishlistRemove(wishlist_id){
          axios.get("/api/businesses/wishlists/delete/"+wishlist_id)
            .then((resp) =>{
                  this.snackbar = true;
                  this.text = resp.data.message;
                  this.timeout = 3000;
                  this.getWishlist();
              }
          );
      } ,
      wishlistRemovebyreseller(wishlist_id){
          axios.get("/api/businesses/wishlists/remove"+wishlist_id)
            .then((resp) =>{
                  this.snackbar = true;
                  this.text = resp.data.message;
                  this.timeout = 3000;
                  this.getWishlist();
              }
          );
      }
    },
    computed:{
      rlists(){
        return this.wishlist.slice((this.page - 1)*this.perPage, this.page*this.perPage)
      }
    },
  }
  </script>