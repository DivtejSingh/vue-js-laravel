<template>
  <div>
    <div v-if="jobdetail.image_url != ''">
      <v-img :src=(url+jobdetail.image_url) cover
             class="white--text align-center desktop"
             height="250" gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)">
        <!-- <v-card-title class="justify-center h2">{{ jobdetail.name }}</v-card-title> -->
      </v-img>
    </div>
    <div v-else>
      <v-img :src="('https://dummyimage.com/299x150/969096/3f4042.jpg')" cover
             class="white--text align-center desktop"
             height="250" gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)">
        <!-- <v-card-title class="justify-center h2">{{ jobdetail.name }}</v-card-title> -->
      </v-img>
    </div>
    <div v-if="jobdetail.image_url != ''">
      <v-img :src=(url+jobdetail.image_url) cover
             class="white--text align-center mobile"
             height="150" gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)">
        <!-- <v-card-title class="justify-center h2">{{ jobdetail.name }}</v-card-title> -->
      </v-img>
    </div>
    <div v-else>
      <v-img :src="('https://dummyimage.com/299x150/969096/3f4042.jpg')" cover
             class="white--text align-center mobile"
             height="150" gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)">
        <!-- <v-card-title class="justify-center h2">{{ jobdetail.name }}</v-card-title> -->
      </v-img>
    </div>
      <v-container>
        <v-row class="desktop">
          <div class="cred text-center"><h4>Job Detail</h4></div>
          <v-col cols="12" md="12">
            <div class="text-center d-md-flex">
              <v-btn color="red" class="me-1 my-1" dark @click="goBack"><v-icon>mdi-chevron-left</v-icon>Back</v-btn>
              <a href="#accordionContact" class="me-1">
                <v-btn color="success" class="my-1"><v-icon>mdi-map-marker</v-icon>Google Location</v-btn>
              </a>
              
              <v-speed-dial  v-model="fab" :direction="direction" :open-on-hover="hover" :transition="transition">
                <template v-slot:activator>
                  <v-btn v-model="hover" color="blue darken-2 my-1" dark>
                    Share
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
          </v-col>
        </v-row>
        <v-row class="mobile mb-2">
          <div class="cred text-center"><h5>Job Detail</h5></div>
          <v-col cols="12" sm="4" md="4" class="d-md-block d-none"></v-col>
          <v-col cols="12" md="8" sm="4" class="pt-0">
            <div class="text-center row gx-1 justify-content-center">
              <div class="col-3 text-end">
                <v-btn color="red" dark @click="goBack"><v-icon>mdi-chevron-left</v-icon>
                  Back
                </v-btn>
              </div>
              <div class="col-4 text-start">
                <a href="#accordionContact">
                  <v-btn color="success">
                    <v-icon>mdi-map-marker</v-icon>
                      Location
                  </v-btn>
                </a>
              </div>
              <div class="col-12 py-0">
                <v-row class="text-center maindiv g-1 justify-content-center">
                  <v-col cols="4 hidediv"></v-col>
                  <v-col cols="3 text-end"><span class="cred">Share</span></v-col>
                  <v-col cols="5 text-start" class="py-1">
                    <v-btn fab dark x-small color="blue" class="fbicon" @click="shareOnFacebook">
                      <v-icon >mdi-facebook</v-icon>
                    </v-btn>
                    <v-btn fab dark x-small color="green" @click="shareOnWhatsApp">
                      <v-icon>mdi-whatsapp</v-icon>
                    </v-btn>
                  </v-col>
                </v-row>
              </div>
             
            </div>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" md="8" class="order-2 order-md-2">
            <div class="accordion" role="tablist">
              <b-card no-body class="mb-1">
                <b-card-header header-tag="header" class="p-1" role="tab">
                  <b-button class="w-100 catsy fw-bold cblu d-flex justify-content-between btn-sm"
                            v-b-toggle.accordion-1 variant="light">Description</b-button>
                </b-card-header>
                <b-collapse id="accordion-1" visible accordion="my-accordion" role="tabpanel">
                  <b-card-body>
                    <b-card-text class="text-justify">
                      {{ jobdetail.job_description }}
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
                  :src=(mapurl+bname+space+jobdetail.city+space+jobdetail.state)
                  allowfullscreen>
              </iframe>
            </div>
          </v-col>
          <v-col cols="12" md="4" class="order-1 order-md-1">
            <div class="apply-job shadow border">
              <h2 class="h5 cred mb-0">{{ jobdetail.job_title }}</h2>
              <div>By:
                <a :href="'/'+jobdetail.username">{{ jobdetail.name }}</a>
              </div>
              <div class="wish">
                  <span v-if="loggedUser == false">
                    <span @click="toggleLike">
                      Add to Wishlist<v-icon>{{ 'mdi-heart-outline' }}</v-icon>
                    </span>
                  </span>
                <span v-else>
                      <span v-if="jobdetail.user_job_wishlist!=null">
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
              <div class="mb-2">{{ jobdetail.city }}, {{ jobdetail.state }}</div>
              <h2 class="h5 cred">Job Details</h2>
              <h3 class="h5">Salary</h3>
              <ul class="list-unstyled mb-5 ps-2">
                <li><v-icon class="cred" size="20">mdi-check-bold</v-icon>₹{{ jobdetail.salary_from }} a Month</li>
              </ul>
              <h3 class="h5">Job Type</h3>
              <ul class="list-unstyled mb-5 ps-2">
                <li><v-icon class="cred" size="20">mdi-check-bold</v-icon>{{ jobdetail.job_type }}</li>
              </ul>
              <h3 class="h5">Qualifications</h3>
              <ul class="list-unstyled mb-5 ps-2">
                <li><v-icon class="cred" size="20">mdi-check-bold</v-icon>
                  {{ jobdetail.qualification }}</li>
              </ul>
              <h3 class="h5">Experience</h3>
              <ul class="list-unstyled mb-5 ps-2">
                <li><v-icon class="cred" size="20">mdi-check-bold</v-icon>
                  <span v-if="jobdetail.min_exp == 0">Fresher</span>
                  <span v-else>{{ jobdetail.min_exp }} Years</span>
                </li>
              </ul>
              <a :href="'tel:+' + jobdetail.phonecode + jobdetail.mobile_phone">
                <v-btn color="red" dark class="me-1 my-1"><v-icon>mdi-phone</v-icon>Call Now</v-btn>
              </a>
              <a :href="('https://wa.me/'+jobdetail.phonecode+jobdetail.mobile_phone+'?text=Enquiry from Bizlx : '+currentURL)">
                <v-btn color="success" class="me-1 my-1"><v-icon>mdi-whatsapp</v-icon>WhatsApp</v-btn>
              </a>
            </div>
          </v-col>
        </v-row>
      </v-container>
    <v-dialog v-model="dialog" persistent max-width="500px">
      <v-card>
        <v-card-title style="justify-content:space-between;" class="text-h6 grey lighten-2">
          <span class="text-h5">Apply Now</span>
          <v-icon  @click="dialog = false" class="text-h5">mdi-close</v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form ref="form1" v-model="valid">
              <input type="hidden" id="business_user_id" ref="business_user_id" :value=jobdetail.business_id>
              <input type="hidden" id="logged_user_id" ref="logged_user_id" :value=loggedUser.id>
              <input type="hidden" id="job_name" ref="job_name" :value=jobdetail.job_title>
              <v-row>
                <v-col cols="12" sm="6" md="6" class="p-1">
                  <v-text-field dense type="text" label="Name" :rules="userRules" v-model="name" required></v-text-field>
                </v-col>
                <v-col cols="12" sm="6" md="6" class="p-1">
                  <v-text-field dense type="email" label="Email" :rules="emailRules" v-model="email"></v-text-field>
                </v-col>
                <v-col cols="12" sm="6" md="12" class="p-1">
                  <v-text-field dense type="number" label="Phone" :rules="mobileRules" v-model="phone" required></v-text-field>
                </v-col>
                <v-col cols="12" sm="12" md="12" class="p-2">
                  <v-autocomplete dense
                        :rules="qualification"
                        solo
                        hide-details
                        placeholder="--Qualification--"
                        v-model="selectQualification"
                        :items="qualifications"
                        item-text="qualification"
                        id="sel2" class="sel2 spacd">
                  </v-autocomplete>
                </v-col>
                <v-col cols="12" sm="12" md="12" class="p-2">
                  <v-autocomplete dense
                        :rules="experiencerul"
                        solo
                        hide-details
                        placeholder="--Experience--"
                        v-model="selectExperience"
                        :items="experience"
                        id="sel2"
                        class="sel2 spacd">
                  </v-autocomplete>
                </v-col>
              </v-row>
              <div class="mt-5 text-end">
                <v-btn class="ms-3" color="success" @click="getAllData">SUBMIT</v-btn>
              </div>
            </v-form>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="MessageModel1" persistent max-width="500">
      <v-card>
        <v-col cols="12" md="12" class="d-flex" style="justify-content: space-between;">
          <div class="mt-1 text-h6">Job has been applied successfully.
          </div>
          <div @click="MessageModel1 = false" class="my-2">
            <v-icon>mdi-close</v-icon>
          </div>
        </v-col>
      </v-card>
    </v-dialog>
    <v-snackbar v-model="snackbar" :timeout="timeout">{{ text }}
      <template v-slot:action="{}">
        <v-btn color="blue" text @click="snackbar = false">Close</v-btn>
      </template>
    </v-snackbar>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "JobDetail",
  props: {
    id: Number,
    data1:Object,
  },
  data(){
    return{
      mapurl:"https://www.google.com/maps/embed/v1/place?key=AIzaSyD1cGNhJz2BiG4oODjDAkfOH__dxXC_N10&q=",
      bname:'',
      space:' ',
      shareurl:'https://bizlx.com/jobs/detail/',
      dialog: false,
      MessageModel1: false,
      valid: false,
      jobMessage: '',
      direction: "right",
      direction1: "left",
      fab: false,
      fling: true,
      hover: true,
      transition: "scale-transition",
      file2: null,
      jobdetail:[],
      qualifications:[],
      loggedUser: false,
      experience: [ {'id':"0", 'text':"Fresher"}, {'id':1, 'text':"1 years"}, {'id':2, 'text':"2 years"}, {'id':3, 'text':"3 years"}, {'id':4, 'text':"4 years"}, {'id':5, 'text':"5 years"}, {'id':6, 'text':"5 + years"} ],
      phone: '',
      name: '',
      email: '',
      job_slug: '',
      userID: null,
      selectExperience: '',
      selectQualification: '',
      timeout: '',
      snackbar: false,
      text: '',
      url:'https://bizlx.s3.ap-south-1.amazonaws.com',
      currentURL:'',
      userRules: [
        v => !!v || 'Name is required.',
        v => v.length <= 50 || 'Name must be less than 50 characters.',
      ],
      mobileRules: [
        v => !!v || 'Mobile number is required.',
        v => v.length != 9 ||  'Mobile number must be 10 characters.',
      ],
      emailRules: [
        v => !!v || 'E-mail is required.',
        v => /.+@.+/.test(v) || 'E-mail must be valid.',
      ],
      qualification: [
        v => !!v || 'E-mail is required.',
        v => /.+@.+/.test(v) || 'E-mail must be valid.',
      ],
      experiencerul: [
        v => !!v || 'E-mail is required.',
        v => /.+@.+/.test(v) || 'E-mail must be valid.',
      ],
    }
  },
    created() {
        this.currentURL = this.getCurrentURL();
    },
    mounted() {
    this.job_slug = this.data1.job_slug;
    if(this.isAuthenticated()){
        this.loggedUser = this.isAuthenticated();
        this.userID = this.$store.state.userData.id;
    } else {
        this.loggedUser = false;
    }
    this.getjobdetail();
    this.getQualification();
    this.getAllData();
  },
  methods: {
    getCurrentURL() {
      return window.location.href;
    },

    shareOnFacebook() {
      const url = this.shareurl+this.job_slug;
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
    },
    shareOnWhatsApp() {
      // Create the WhatsApp share URL
      const url = this.shareurl+this.job_slug;
      const whatsappShareUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent(url)}`;
      window.open(whatsappShareUrl, '_blank');
    },
    goBack() {
      window.history.back();
    },
    getQualification() {
      axios.get("/api/qualification")
          .then((resp) => {
                this.qualifications = resp.data.qualification;
              }
          );
    },
    getAllData(){
      var data = {
        name: this.name,
        email: this.email,
        phone: this.phone,
        qualification: this.selectQualification,
        experience: this.selectExperience,
        business_user_id: this.$refs.business_user_id.value,
        job_name: this.$refs.job_name.value,
        logged_user_id: this.$refs.logged_user_id.value,
      };
      axios.post("/api/jobapply", data)
          .then((resp) => {
            if(resp.data.success == true)
              this.$refs.form1.reset();
              this.dialog = false;
              this.MessageModel1 = true;
          });
      this.$refs.form1.validate();
    },
    getjobdetail(){
      axios.get('/api/jobcategory/job-detail/'+this.data1.job_slug+'/'+this.userID)
          .then((resp) =>{
            this.jobdetail = resp.data.jobdetails;
            var rare1 = this.jobdetail.name;
            this.bname = rare1.replace('&','and');
            if(resp.data.jobdetails.user_job_wishlist!=null){
              this.isLiked = 'red';
              this.heartColor = 'red';
            }

          })
    },
    toggleLike() {
      if(this.loggedUser == false){
          this.snackbar = false;
          window.location.href = '/user/login';
          this.timeout = 3000;
      }else{
        this.isLiked = !this.isLiked;
        let data = {
          business_id: this.jobdetail.business_id,
          wishlist_type: 3,
          services_id: this.jobdetail.id,
        };
        if(this.isLiked==true){ // add
            axios.post("/api/add/wishlist", data)
                .then((resp) => {
                      this.snackbar = true;
                      this.text = resp.data.message;
                      this.timeout = 3000;
                      this.isLiked = 'red';
                      this.heartColor = 'red';
                      this.getjobdetail();
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
                      this.getjobdetail();
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

.apply-job {
  background: #e5e5e5;
  padding: 10px;
}
input#file {
  background: none !important;
  }
form.job-page-form select#Qualification {
  background-color: #f2f2f2;
  }
form.job-page-form select#Experience {
  background-color: #f2f2f2;
  }
form.job-page-form input {
  background-color: #f2f2f2 !important;
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
@media(max-width: 576px){
  .v-btn:not(.v-btn--round).v-size--default {
	height: 30px;
	min-width: 64px;
	padding: 0 7px;
}
.hidediv {
    display: none;
}
.sharebtn {
    width: 100% !important;
}
.sharebtn[data-v-e627e422] {
    width: 100% !important;
    max-width: 84%;
    text-align: left;
    margin: 0 auto;
}
.fbicon {
    margin-left: 10px;
    margin-right: 10px;
}
.fbicons {
    font-size: 16px;
    font-weight: bold;
    text-transform: capitalize;
    color: red !important;
    padding-left: 0;
}
}

</style>
