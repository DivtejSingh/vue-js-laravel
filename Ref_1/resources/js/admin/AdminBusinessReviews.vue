<template>
    <div class="pa-2">
      <h4 class="ms-4">Reviews to Business</h4>
        <div v-if="reviews.length>0" class="row">
          <div class="col-12 col-md-6" v-for="(review,i) in reviews" :key="i">
            <v-card>
              <v-card-text>
                <v-row>
                  <!-- <v-col cols="3" md="2">
                    
                    <v-avatar v-if="review.user_avatar!=null">
                      <img :src="url+review.user_avatar" alt="avatar">
                    </v-avatar>
  
                    <v-avatar v-if="review.user_avatar==null" color="indigo" size="36">
                      <span class="white--text text-h5">!</span>
                    </v-avatar>
  
                  </v-col> -->
                  <v-col cols="9" md="9">
                      <span class="h5 link-dark ms-1">
                          {{ review.name }}
                          <v-rating :value="review.rating" color="amber" dense readonly half-increments size="20"></v-rating>
                      </span>
                  </v-col>
                  <v-col cols="3" md="3"><span class="small text-muted">
                      {{ review.added_date | fdate}}
                    <!-- 1 Day ago -->
                  </span></v-col>
                </v-row>
                <v-row>
                  <v-col cols="12" md="12"><p class="link-dark mb-0">{{ review.review_text }}</p></v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </div>
        </div>
        <div v-else class="row">
          <p class="ms-4 text-center">No data found.</p>
        </div>
    </div>
  </template>
  <script>
  import axios from 'axios';
  export default {
    name: 'BusinessReviews',
    components: {},
    metaInfo: {title: 'Bizlx Business Reviews'},
    data() {
      return {
        url:'https://bizlx.s3.ap-south-1.amazonaws.com',
        reviews:[],
      }
    },
    filters:{
      fdate(value) {
        if (!value) return '';
        const [year, month, day] = value.split('-');
        return `${day}-${month}-${year}`;
      },
      reverseFdate(value) {
        if (!value) return '';
        const [day, month, year] = value.split('-');
        return `${year}-${month}-${day}`;
      }
    },
    created(){
      this.user_id();
      this.userdata = this.user_id();
      if(this.userdata.id != null){
        this.getReviewsbyreseller();
      }else{
        this.getReviews();
      }
    },
    methods:{
    //   user_id() {
    //     const uid = localStorage.getItem('userId');
    //     try{
    //       return JSON.parse(uid);
    //     }
    //     catch(e)
    //     {
    //     return this.$store.state.userId;
    //     }
     
    //   },
    user_id() {
        const url = window.location.href;
        const lastSegment = url.split('/').filter(Boolean).pop();
        return lastSegment;
      },
    
        getReviews(){
        //   axios.get("/api/businesses/reviews/get")
          axios.post("/api/businesses/reviews/getbyadmin",{ userId: this.user_id() })
            .then((resp) =>{
                this.reviews = resp.data.reviews;
            }
          );
        },
      getReviewsbyreseller(){
          axios.post("/api/businesses/reviews/getbyreseller",{userId:this.userdata.id})
            .then((resp) =>{
                this.reviews = resp.data.reviews;
            }
          );
        },
    }
  }
  </script>