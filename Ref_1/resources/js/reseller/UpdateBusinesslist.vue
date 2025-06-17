<template>
  <div>
    <div class="row py-1">
      <div class="col-8 col-md-10">
        <h4 class="ms-4">Client</h4>
      </div>
      <div class="col-4 col-md-2">
        <a href="/reseller/business-list">
          <v-btn tile color="cred">
            <v-icon left>
              mdi-format-list-bulleted
            </v-icon>
            LIST
          </v-btn>
        </a>
      </div>
    </div>
    <v-tabs v-model="tab">
      <v-tab cols="6">Update Business Client</v-tab>
    </v-tabs>
    <v-tabs-items v-model="tab">
      <v-tab-item>
        <v-card color="basil" flat >
          <v-form ref="form" v-model="valid" lazy-validation>
            <v-row>
              <v-col class="py-2" cols="12" md="12">
                <v-text-field label="Reseller Id" v-model="reseller_id" class="d-none"></v-text-field>
              </v-col>
              <v-col class="py-4" cols="12" md="12">
                <v-text-field
                    v-model="bdata.name"
                    :rules="nameRules"
                    :counter="100"
                    dense
                    label="Store / Business Name" type="text">
                </v-text-field>
              </v-col>
              <v-col class="py-2" cols="12" md="12">
                <v-text-field
                    v-model="bdata.email"
                    :rules="emailRules"
                    @keyup="emailVerify"
                    :error-messages="emailVerifyMessage"
                    label="Your Email"
                    dense
                    type="email">
                </v-text-field>
              </v-col>
              <v-col class="py-2" cols="12" md="12">
                <v-text-field
                    v-model="bdata.username"
                    :rules="usernameRules"
                    @keyup="userNameVerify"
                    :error-messages="userNameVerifyMessage"
                    :counter="80"
                    dense
                    label="Username"
                    type="text">
                </v-text-field>
              </v-col>
              <v-col class="py-2" cols="12" md="12">
                <v-text-field label="GST" v-model="bdata.gst"></v-text-field>
              </v-col>
              <v-col class="d-flex" cols="12" md="12">
                <v-autocomplete
                    v-model="bdata.cat_id"
                    @change="AllCats"
                    :rules="maincatRuless"
                    label="Select Main Category"
                    :items="allmaincategories"
                    item-text="cat_name"
                    item-value="id"
                    dense
                    prepend-inner-icon="mdi-tray-arrow-down">
                </v-autocomplete>
              </v-col>
              <v-col cols="12" md="12">
                <v-autocomplete
                    v-model="subids"
                    :rules="catRuless"
                    label="Select Sub Category"
                    :items="allcategories"
                    item-text="subcat_name"
                    multiple
                    item-value="id"
                    dense
                    :no-data-text="customNoDataText1"
                    prepend-inner-icon="mdi-tray-arrow-down">
                </v-autocomplete>
              </v-col>
              <v-col class="d-flex" cols="12" md="12">
                <v-autocomplete label="Select Country" v-model="country_id" @change="getStateOnChange()"
                  @selected="getStateOnChange()" :rules="countryRules" :items="countries" item-text="country" item-value="id"></v-autocomplete>
              </v-col>
              <v-col class="d-flex" cols="12" md="12">
                <v-autocomplete dense
                                v-model="bdata.state_id"
                                @change="getCities"
                                :rules="stateRules"
                                label="Select State"
                                :items="allstates"
                                item-text="state"
                                prepend-inner-icon="mdi-home-city"
                                item-value="id">
                </v-autocomplete>
              </v-col>
              <v-col class="d-flex" cols="12" md="12">
                <v-autocomplete dense
                                v-model="bdata.city_id"
                                :rules="cityRules"
                                label="Select City"
                                :items="cities"
                                item-text="city"
                                prepend-inner-icon="mdi-city"
                                item-value="id"
                                :no-data-text="customNoDataText">
                </v-autocomplete>
              </v-col>
              <v-col class="py-2" cols="12" md="12">
                <v-text-field
                    v-model="bdata.mobile_phone"
                    :rules="mobileRules"
                    @keyup="phoneVerify"
                    dense
                    :error-messages="phoneVerifyMessage"
                    :counter="10"
                    label="Enter Mobile"
                    type="number">
                </v-text-field>
              </v-col>
              <v-col class="py-2" cols="12" md="12">
                <v-textarea rows="3" v-model="bdata.address" dense row-height="20" label="Business Address"></v-textarea>
              </v-col>
              <v-col class="py-2 ms-2" cols="12" md="2">
                <v-checkbox v-model="bdata.user_status" label="Active" value="1"></v-checkbox>
              </v-col>
            </v-row>
            <div class="text-end">
              <v-btn :disabled="!valid" @click="BusinessformSubmit" color="cred" large class="text-white">SAVE</v-btn>
            </div>
          </v-form>
          <v-snackbar v-model="snackbar" :timeout="timeout">{{ text }}
            <template v-slot:action="{attrs}">
              <v-btn color="blue" text v-bind="attrs" @click="snackbar = false">Close</v-btn>
            </template>
          </v-snackbar>
        </v-card>
      </v-tab-item>
    </v-tabs-items>
  </div>
</template>
<script>

import axios from "axios";

export default {
  name: 'UpdateBusinesslist',
  props: {
    business_id: Number,
  },
  components: {},
  data() {
    return{
      bdata: [],
      subids: [],
      reseller_id: '',
      tab: '',
      allcategories:[],
      allmaincategories:[],
      countries:[],
      allstates:[],
      cities:[],
      userNameVerifyMessage:'',
      emailVerifyMessage:'',
      phoneVerifyMessage:'',
      valid: false,
      snackbar: false,
      text: '',
      timeout: '',
      country_id:'',
      customNoDataText: 'Please Select State First',
      customNoDataText1: 'Please Select Main Category First',
      nameRules: [
        v => !!v || 'Store/Bussine Name is required.',
        v => v.length <= 100 || 'Name must be less than 100 characters.',
      ],
      usernameRules: [
        v => !!v || 'Username is required.',
        v => v.length <= 80 || 'Username must be less than 80 characters.',
        v => (v.split(' ').length <= 1) || 'space not allowed',
        v => /^[\w\s]+$/.test(v) || 'Username may only contain letters, numbers, dashes and underscores'
      ],
      catRuless: [
        v => !!v || 'Sub Category name is required.',
      ],
      maincatRuless: [
        v => !!v || 'Main Category name is required.',
      ],
      mobileRules: [
        v => !!v || 'Mobile number is required.',
        v => v.length != 9 ||  'Mobile number must be 10 characters.',
      ],
      countryRules: [
        v => !!v || 'Country is required.',
      ],
      stateRules: [
        v => !!v || 'State is required.',
      ],
      cityRules: [
        v => !!v || 'City is required.',
      ],
      emailRules: [
        v => !!v || 'E-mail is required.',
        v => /.+@.+/.test(v) || 'E-mail must be valid.',
      ],
      addressRules: [
        v => !!v || 'Address is required.',
        v => v.length <= 100 || 'Address must be less than 100 characters.',
      ],
      agree_policyRules: [
        v => !!v || 'This field is required.',
      ],
    }
  },
  mounted() {
    this.All_Countries();
    this.getresellerdata();
    this.reseller_id = this.getresellerdata();
    this.Getbusinessdata();
    this.AllStates();
    this.allmaincats();
  },

  methods: {
    getresellerdata(){
      return this.$store.state.userData.username;
    },
    Getbusinessdata(){
      axios.get(`/api/get/business/details?business_id=${this.business_id}`)
          .then((resp) =>{
            this.bdata = resp.data.registered_business[0];
            this.country_id = resp.data.registered_business[0].country_id;
            this.AllCats();
            for (let i = 0; i < resp.data.subcatids.length; i++) {
              this.subids.push(
                  resp.data.subcatids[i].id
              );
            }
            this.getCities();
          });
    },
    All_Countries(){
      axios.get('/api/countries')
        .then((resp)=>{
          this.countries = resp.data.countries;
        });
    },
    AllCats() {
        axios.get('/api/subcategories?id='+this.bdata.cat_id)
            .then((resp) => {
              this.allcategories = resp.data;
            })
    },
    allmaincats() {
      axios.get('/api/maincategory')
          .then((resp) => {
            this.allmaincategories = resp.data.maincategories;
          });
    },
    AllStates(){
      //api use  -------  "get/states/"+country_id
      axios.get('/api/allstates')
        .then((resp)=>{
          this.allstates = resp.data.locations;
      });
    },
    getStateOnChange(){
      var country_id = this.country_id;
      axios.get("/api/get/states/"+country_id)
        .then(response => {
            this.allstates = response.data;
      });
    },
    getCities() {
        axios.get('/api/states/'+this.bdata.state_id)
          .then(response => {
            this.cities = response.data;
        })
    },

    userNameVerify(){ // verify username
      if(this.bdata.username.length >= 8){
        axios.post('/api/user/verify', {'username':this.bdata.username})
            .then((resp) => {
              if(resp.data.status==400){
                this.userNameVerifyMessage = resp.data.message;
              }else{
                this.userNameVerifyMessage = "";
              }
            });
      }
    },
    emailVerify(){ // verify email
      if(/.+@.+/.test(this.bdata.email)){
        axios.post('/api/user/verify', {'email':this.bdata.email})
            .then((resp) => {
              if(resp.data.status==400){
                this.emailVerifyMessage = resp.data.message;
              }else{
                this.emailVerifyMessage = "";
              }
            });
      }
    },
    phoneVerify(){ // verify phone
      if(this.bdata.mobile_phone.length == 10){
        axios.post('/api/user/verify', {'mobile_phone':this.bdata.mobile_phone})
            .then((resp) => {
              if(resp.data.status==400){
                this.phoneVerifyMessage = resp.data.message;
              }else{
                this.phoneVerifyMessage = "";
              }
            });
      }
    },

    async BusinessformSubmit() {

      var data = {
        business_id: this.business_id,
        reseller_id: this.reseller_id,
        business_name: this.bdata.name,
        email: this.bdata.email,
        username: this.bdata.username,
        mobile: this.bdata.mobile_phone,
        user_status: this.bdata.user_status,
        // password: this.password,
        category: this.bdata.cat_id,
        subcategories: this.subids,
        GST: this.bdata.gst,
        city: this.bdata.city_id,
        state: this.bdata.state_id,
        Address: this.bdata.address
      };
      axios.post('/api/update/reseller/details', data)
          .then((resp) => {
            this.snackbar = true;
            this.text = resp.data.message;
            this.timeout = 3000;
          });
    }
  },
}
</script>