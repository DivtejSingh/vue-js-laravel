<template>
    <div>
        <div class="row">
            <v-col cols="12" md="6" sm="12">
                <div class="ratio ratio-16x9">
                    <v-img v-if="videoDetail.youtube_id==null" :src="('https://placehold.co/600x400?text='+videoDetail.video_title)+' No video'" class="embed-responsive-item"></v-img>
                    <iframe v-if="videoDetail.youtube_id!=null" :src="('https://www.youtube.com/embed/'+videoDetail.youtube_id)" class="embed-responsive-item"></iframe>
                </div>
            </v-col>
            <v-col cols="12" md="6" sm="12">
                <div class="mobile">
                    <div class="small d-flex justify-content-between">
                        <div class="p-0 mdetails">
                            <h6>{{ videoDetail.video_title }}</h6>
                            <h6>By: <a :href="'/'+videoDetail.username" class="text-decoration-none">{{ videoDetail.name }}</a></h6>
                            <!-- <div class="ratings align-center d-flex mb-3">
                                <v-rating :value=videoDetail.rating_avrage color="amber" dense half-increments size="15" readonly></v-rating>
                                <div class="white--text badge bg-dark ms-2">{{ videoDetail.rating_avrage }}</div>
                            </div> -->
                            <div class="wish">
                                <span v-if="loggedUser == false">
                                    <span @click="toggleLike">
                                    Add to Wishlist<v-icon>{{ 'mdi-heart-outline' }}</v-icon>
                                    </span>
                                </span>
                                <span v-else>
                                    <span v-if="videoDetail.user_job_wishlist!=null">
                                        <span class="wishlist-icon" @click="toggleLike">
                                        Add to Wishlist<v-icon :color="heartColor">
                                            {{ isLiked ? 'mdi-heart' : 'mdi-heart-outline' }}
                                        </v-icon>
                                        </span>
                                    </span>
                                    <span v-else>
                                        <span class="wishlist-icon" @click="toggleLike">
                                        Add to Wishlist<v-icon :color="heartColor">{{ isLiked ? 'mdi-heart' : 'mdi-heart-outline' }}</v-icon>
                                        </span>
                                    </span>
                                </span>
                            </div>
                            <div class="ratings align-center d-flex mb-3 cred fw-semibold">
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
                            <a :href="('https://wa.me/'+'+'+videoDetail.phonecode+videoDetail.mobile_phone+'?text=Enquiry from Bizlx : '+currentURL)">
                                <v-btn fab x-small outlined color="success">
                                    <v-icon>mdi-whatsapp</v-icon>
                                </v-btn>
                                <span class="small text-success">Whatsapp</span>
                            </a>
                            <a :href="'tel:+' + videoDetail.phonecode + videoDetail.mobile_phone" title="Call">
                                <v-btn fab x-small outlined color="primary">
                                    <v-icon>mdi-phone</v-icon>
                                </v-btn>
                                <span class="small text-primary">Call</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="desktop">
                    <v-card-text class="p-0">
                        <h5>{{ videoDetail.video_title }}</h5>
                        <h6>By: <a :href="'/'+videoDetail.username" class="text-decoration-none">{{ videoDetail.name }}</a></h6>
                        <div class="ratings align-center d-flex mb-1">
                            <v-rating :value=videoDetail.rating_avrage color="amber" dense half-increments size="20" readonly></v-rating>
                            <div class="white--text badge bg-dark ms-2">{{ videoDetail.rating_avrage }}</div>
                        </div>
                        <div class="mb-3">
                            <v-icon small>mdi-map-marker</v-icon>
                            <div v-if="videoDetail.address!=null">
                                {{ videoDetail.address }},
                            </div>
                            {{ videoDetail.city }}, {{ videoDetail.state }}
                        </div>
                        <div class="py-1 d-flex">
                            <a :href="'tel:+' + videoDetail.phonecode + videoDetail.mobile_phone">
                                <v-btn color="red" dark class="me-1 my-1">
                                    <v-icon small>mdi-phone</v-icon>Call Now</v-btn>
                            </a>
                            <a :href="'https://wa.me/'+'+'+ videoDetail.phonecode + videoDetail.mobile_phone + '?text=Enquiry from Bizlx : ' + currentURL">
                                <v-btn color="success" class="me-1 my-1">
                                    <v-icon small>mdi-whatsapp</v-icon>WhatsApp
                                </v-btn>
                            </a>
                            <v-speed-dial v-model="fab" :direction="direction" :open-on-hover="hover" :transition="transition" class="m-1">
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
                    </v-card-text>
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
                                    {{ videoDetail.video_description }}
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
                        :src=(mapurl+videoDetail.name+space+videoDetail.city+space+videoDetail.state)
                        allowfullscreen>
                    </iframe>
                </div>
            </v-col>
        </div>
    </div>
</template>

<script>
export default {
    name: "VideodetailPage",
    props:{
        id:Number,
        vidata:Object
    },
    data() {
        return {
            mapurl:"https://www.google.com/maps/embed/v1/place?key=AIzaSyD1cGNhJz2BiG4oODjDAkfOH__dxXC_N10&q=",
            shareurl:'https://bizlx.com/videos/detail/',
            videoDetail: [],
            space:' ',
            nearByHotDealsVideos: [],
            isLiked: false,
            loggedUser: [],
            timeout: '',
            snackbar: false,
            text: '',
            direction: "right",
            direction1: "left",
            fab: false,
            fling: true,
            hover: true,
            transition: "scale-transition",
            currentURL:'',
            video_slug:'',
        }
    },
    mounted() {
        this.loggedUser = this.$store.state.userData;
        this.businessprofile();
        this.currentURL = this.getCurrentURL();
        this.video_slug = this.vidata.video_slug;
    },
    methods:{
        getCurrentURL() {
            return window.location.href;
        },

        shareOnFacebook() {
            const url =  this.shareurl+this.video_slug;
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
        },
        shareOnWhatsApp() {
            // Create the WhatsApp share URL
            const url = this.shareurl+this.video_slug;
            const whatsappShareUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent(url)}`;
            window.open(whatsappShareUrl, '_blank');
        },
        goBack() {
            window.history.back();
        },
        businessprofile(){
            axios.get('/api/video/detail/'+this.vidata.video_slug+'/'+this.loggedUser.id)
                .then((resp) =>{
                    this.videoDetail = resp.data.videoProfile;
                    if(resp.data.videoProfile.user_video_wishlist!=null){
                        this.isLiked = 'red';
                        this.heartColor = 'red';
                    }
                    this.nearByHotDealsVideos = resp.data.nearByHotDealsVideos;
                })
        },
        handleClick(video_slug) {
            this.videoDetail = [];
            this.nearByHotDealsVideos = [];
            axios.get('/api/video/detail/'+video_slug+'/'+this.loggedUser.id)
                .then((resp) =>{
                    this.videoDetail = resp.data.videoProfile;
                    this.nearByHotDealsVideos = resp.data.nearByHotDealsVideos;
                })
        },
        toggleLike() {
            if(!this.loggedUser){
                this.snackbar = false;
                this.$router.push('/user/login');
                this.timeout = 3000;
            }else{
                this.isLiked = !this.isLiked;
                let data = {
                    business_id: this.videoDetail.business_id,
                    wishlist_type: 4,
                    services_id: this.videoDetail.video_id,
                };
                if(this.isLiked==true){ // add
                    axios.post("/api/add/wishlist", data)
                        .then((resp) => {
                                this.snackbar = true;
                                this.text = resp.data.message;
                                this.timeout = 3000;
                                this.isLiked = 'red';
                                this.heartColor = 'red';
                                this.businessprofile();
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
                                this.businessprofile();
                            }
                        );
                }
            }
        }
    },
}
</script>

<style scoped>

</style>
