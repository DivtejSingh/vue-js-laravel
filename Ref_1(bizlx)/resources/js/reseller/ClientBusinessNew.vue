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
      <v-tab cols="6">Create Business Client</v-tab>
    </v-tabs>
    <v-tabs-items v-model="tab">
      <v-tab-item >
        <v-card color="basil" flat >
          <v-form ref="form" v-model="valid" lazy-validation>
            <v-row>
              <v-col class="py-4" cols="12" md="12">
                <v-text-field
                        v-model="formData.business_name"
                        :rules="nameRules"
                        :counter="100"
                        dense
                        label="Store / Business Name" type="text">
                </v-text-field>
              </v-col>
              <v-col class="py-2" cols="12" md="12">
                <v-text-field
                        v-model="formData.email"
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
                        v-model="formData.username"
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
                <v-text-field label="GST" v-model="formData.GST"></v-text-field>
              </v-col>
              <v-col class="py-2" cols="12" md="12">
                <v-text-field
                        v-model="formData.password"
                        :rules="passwordRules"
                        :counter="15"
                        dense
                        label="Enter Password"
                        type="password">
                ></v-text-field>
              </v-col>
              <v-col class="py-2" cols="12" md="12">
                  <v-text-field
                        v-model="formData.confirm_password"
                        :rules="[(formData.password === formData.confirm_password) || 'Confirm password does not match']"
                        :counter="15"
                        dense
                        label="Confirm Password"
                        type="password">
                  </v-text-field>
              </v-col>
              <v-col class="d-flex" cols="12" md="12">
                <v-autocomplete
                        v-model="formData.category"
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
                        v-model="formData.subcategories"
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
                    <v-autocomplete
                         v-model="formData.country_id"
                         @change="getStatesOnChange"
                        :items="countries"
                        :rules="countryRules"
                        label="Select Country"
                        item-text="country"
                        item-value="id"
                        prepend-inner-icon="mdi-home-city">
                    </v-autocomplete>
              </v-col>
              <v-col class="d-flex" cols="12" md="12">
                    <v-autocomplete
                         v-model="formData.state"
                         @change="getCitiesOnChange"
                        :items="allstates"
                        :rules="stateRules"
                        label="Select State"
                        item-text="state"
                        item-value="id"
                        prepend-inner-icon="mdi-home-city">
                    </v-autocomplete>
              </v-col>
              <v-col class="py-2" cols="12" md="12">
                    <v-autocomplete
                        v-model="formData.city"
                        :rules="cityRules"
                        :items="cities"
                        label="Select City"
                        item-text="city"
                        item-value="id"
                        prepend-inner-icon="mdi-city">
                    </v-autocomplete>
              </v-col>
              <v-col class="py-2" cols="12" md="12">
                <v-text-field
                        v-model="formData.mobile"
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
                <v-textarea rows="3" v-model="formData.address" dense row-height="20" label="Business Address"></v-textarea>
              </v-col>
              <v-col class="py-2 ms-2" cols="12" md="2">
                <v-checkbox v-model="formData.user_status" label="Active"></v-checkbox>
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
  name: 'ClientBusinessNew',
  components: {},
  data() {
    return{
      formData:{
        business_name: '',
        username: '',
        subcategories: '',
        GST:'',
        category: '',
        mobile: '',
        country_id: '',
        state: '',
        city: '',
        email:'',
        password: '',
        confirm_password:'',
        address: '',
        user_status: '',
    },
      tab: '',
      allcategories:[],
      allmaincategories:[],
      countries: [],
      allstates:[],
      cities:[],
      userNameVerifyMessage:'',
      emailVerifyMessage:'',
      phoneVerifyMessage:'',
      valid: false,
      snackbar: false,
      text: '',
      timeout: '',
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
        v => v.length == 10 || 'Mobile number must be 10 characters.',
      ],
      countryRules: [
        v => !!v || 'Country name is required.',
      ],
      stateRules: [
        v => !!v || 'State name is required.',
      ],
      cityRules: [
        v => !!v || 'City name is required.',
      ],
      emailRules: [
        v => !!v || 'E-mail is required.',
        v => /.+@.+/.test(v) || 'E-mail must be valid.',
      ],
      passwordRules: [
        v => !!v || 'Password is required.',
        v => /[A-Z]/.test(v) || 'Use upper case.',
        v => /[a-z]/.test(v) || 'Use lower case.',
        v => /[0-9]/.test(v) || 'Use number.',
        v => /[#?!@$%^&*-]/.test(v) || 'Use special character.',
        v => v.length >= 8 || 'Password must be greater than 8 characters.',
        v => v.length <= 15 || 'Password must be less than 15 characters.',
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
    this.AllCats();
    this.All_Countries();
    this.allmaincats();
  },

  methods: {
    
    AllCats() {
      if (this.formData.category) {
        axios.get(`/api/subcategories?id=${this.formData.category}`)
            .then((resp) => {
              this.allcategories = resp.data;
            })
            .catch(error => {
              console.log(error);
            });
      } else {
        this.allcategories = [];
      }
    },
    allmaincats() {
      axios.get('/api/maincategory')
          .then((resp) => {
            this.allmaincategories = resp.data.maincategories;
          });
    },


    All_Countries(){
      axios.get('/api/countries')
        .then((resp)=>{
          this.countries = resp.data.countries;
        });
    },
    getStatesOnChange() {
      var country_id = this.formData.country_id;
      axios.get("/api/get/states/"+country_id)
        .then(response => {
            this.allstates = response.data;
      })
    },
    getCitiesOnChange() {
      var state = this.formData.state;
      axios.get(`/api/states/${state}`)
        .then(response => {
          this.cities = response.data;
      })      
    },
    userNameVerify(){ // verify username
      if(this.formData.username.length >= 8){
        axios.post('/api/user/verify', {'username':this.formData.username})
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
      if(/.+@.+/.test(this.formData.email)){
        axios.post('/api/user/verify', {'email':this.formData.email})
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
      if(this.formData.mobile_phone.length == 10){
        axios.post('/api/user/verify', {'mobile_phone':this.formData.mobile_phone})
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
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      await axios.post('/api/register/reseller', this.formData, config)
          .then((resp) => {
            this.snackbar = true;
            this.text = resp.data.message;
            this.timeout = 3000;
            this.$refs.form.reset();
          }).catch(error => {
            // var data = {...error.response};
            var data = error.toJSON();
            if(data.status == 400){
              this.snackbar = true;
              this.text = "something went wrong.";
              this.timeout = 3000;
            }
          });
      this.$refs.form.validate();
    },
  },
}
</script>