<template>
    <div>
        <v-container>
        <v-row class="d-flex flex-wrap" align="center" justify="center">
          <!-- IMAGE COLUMN -->
            <v-col cols="12" lg="7" order="1" order-lg="1">
                        <v-img
                    v-if="getimage.length"
                    :src="getimage[0].path"
                    alt="Reseller Image"
                ></v-img>
            </v-col>
            <v-col cols="12" lg="5" order="2" order-lg="2">
            <v-form ref="form" v-model="valid" lazy-validation>
                <v-card class="elevation-18">
                    <v-toolbar flat color="red">
                        <v-toolbar-title class="mx-auto text-white">List Your Business</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-text-field type="number" v-model="formData.user_role" class="d-none"></v-text-field>
                        <v-text-field type="number" v-model="formData.user_status" class="d-none"></v-text-field>
                        <v-row align-content="center">
                            <v-col cols="12" md="6">
                                <v-text-field type="text"
                                              clearable   
                                              v-model="formData.name"
                                              :rules="nameRules"
                                              :counter="100"
                                              class="my-0 py-0"
                                              label="Store / Business Name"
                                              prepend-inner-icon="mdi-account">
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field class="my-0 py-0"
                                              clearable
                                              v-model="formData.username"
                                              :rules="usernameRules"
                                              @keyup="userNameVerify"
                                              :error-messages="userNameVerifyMessage"
                                              :counter="80"
                                              label="User Name"
                                              type="text"
                                              prepend-inner-icon="mdi-account">
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field class="my-0 py-0"
                                              clearable
                                              v-model="formData.email"
                                              :rules="emailRules"
                                              @keyup="emailVerify"
                                              :error-messages="emailVerifyMessage"
                                              label="Email ID"
                                              type="email"
                                              prepend-inner-icon="mdi-email">
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field class="my-0 py-0"
                                              clearable
                                              v-model="formData.mobile_phone"
                                              :rules="mobileRules"
                                              @keyup="phoneVerify"
                                              :error-messages="phoneVerifyMessage"
                                              :counter="10"
                                              label="Mobile"
                                              type="number"
                                              prepend-inner-icon="mdi-cellphone">
                                </v-text-field>
                            </v-col>
                            <!-- <v-col cols="12" md="6">
                              <v-autocomplete class="my-0 py-0"
                                    v-model="formData.cat_id"
                                    @change="AllCats"
                                    :rules="maincatRuless"
                                    label="Main Category"
                                    :items="allmaincategories"
                                    item-text="cat_name"
                                    item-value="id"
                                    prepend-inner-icon="mdi-tray-arrow-down">
                              </v-autocomplete>
                            </v-col> -->
                            <v-col cols="12" md="6">
                                <v-text-field class="my-0 py-0"
                                              v-model="formData.refferal_code"
                                              :counter="15"
                                              label="Refferal Code"
                                              type="text"
                                              prepend-inner-icon="mdi-infinity">
                                </v-text-field>
                            </v-col>
                            <!-- <v-col cols="12" md="6">
                              <v-autocomplete class="my-0 py-0"
                                    v-model="formData.subcategory_id"
                                    :rules="catRuless"
                                    label="Category"
                                    :items="allcategories"
                                    item-text="subcat_name"
                                    multiple
                                    item-value="id"
                                    :no-data-text="customNoDataText1"
                                    prepend-inner-icon="mdi-tray-arrow-down">
                              </v-autocomplete>
                            </v-col> -->
                            <v-col cols="12" md="6">
                                <v-autocomplete dense class="my-0 py-0"
                                                v-model="formData.subcategory_id"
                                                :rules="catRuless"
                                                label="Category"
                                                :items="allcategories"
                                                item-text="subcat_name"
                                                clearable
                                                item-value="id"
                                                :no-data-text="customNoDataText1"
                                                prepend-inner-icon="mdi-tray-arrow-down"
                                                @keyup.native="AllSubCats">
                                </v-autocomplete>
                            </v-col>
                        </v-row>
                        <v-row align-content="center">
                            <v-col cols="12" md="6">
                                <v-text-field class="my-0 py-0"
                                              clearable
                                              v-model="formData.password"
                                              :counter="15"
                                              :rules="passwordRules"
                                              label="Password"
                                              :append-icon="showPassword ? 'mdi-eye-outline' : 'mdi-eye-off-outline'"
                                              :type="showPassword ? 'text' : 'password'"
                                              @click:append="showPassword = !showPassword"
                                              prepend-inner-icon="mdi-lock">
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field class="my-0 py-0"
                                              clearable
                                              v-model="formData.confirm_password"
                                              :rules="[(formData.password === formData.confirm_password) || 'Confirm password does not match']"
                                              :counter="15"
                                              label="Confirm Password"
                                              :append-icon="showPassword2 ? 'mdi-eye-outline' : 'mdi-eye-off-outline'"
                                              :type="showPassword2 ? 'text' : 'password'"
                                              @click:append="showPassword2 = !showPassword2"
                                              prepend-inner-icon="mdi-lock">
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-autocomplete dense class="my-0 py-0"
                                clearable
                                                v-model="formData.country_id"
                                                @change="getStatesOnChange"
                                                :items="countries"
                                                :rules="countryRules"
                                                label="Country"
                                                item-text="country"
                                                item-value="id"
                                                prepend-inner-icon="mdi-home-city">
                                </v-autocomplete>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-autocomplete dense class="my-0 py-0"
                                clearable
                                                v-model="formData.state_id"
                                                @change="getCitiesOnChange"
                                                :items="allstates"
                                                :rules="stateRules"
                                                label="State"
                                                item-text="state"
                                                item-value="id"
                                                prepend-inner-icon="mdi-home-city">
                                </v-autocomplete>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-autocomplete dense class="my-0 py-0"
                                clearable
                                                v-model="formData.city_id"
                                                :rules="cityRules"
                                                :items="cities"
                                                label="City"
                                                item-text="city"
                                                item-value="id"
                                                prepend-inner-icon="mdi-city" >
                                </v-autocomplete>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-autocomplete
                                    dense
                                    class="my-0 py-0"
                                    clearable
                                    v-model="formData.area"
                                    :items="areas"
                                    :search-input.sync="areaSearch"
                                    label="Area / Sector"
                                    item-text="display_name"
                                    item-value="display_name"
                                    :loading="loadingAreas"
                                    prepend-inner-icon="mdi-map-marker"
                                    @update:search-input="onAreaSearch"
                                    />

                            </v-col>

                            <v-col cols="12" md="6">
                                <v-textarea class="my-0 py-0"
                                clearable
                                            v-model="formData.address"
                                            :rules="addressRules"
                                            :counter="100"
                                            label="Address"
                                            rows="2"
                                            prepend-inner-icon="mdi-map">
                                </v-textarea>
                            </v-col>
                        </v-row>
                        <v-row class="mt-0">
                            <v-col cols="12" md="5">
                                <v-checkbox type="checkbox"
                                            label="Agree to"
                                            v-model="formData.agree_policy"
                                            :rules="agree_policyRules"
                                            value="1"
                                            class="my-0 py-0">
                                    <template v-slot:label>
                                        <div>Agree to
                                            <v-tooltip bottom>
                                                <template v-slot:activator="{ on }">
                                                    <a target="_blank" href="/terms-of-use" @click.stop v-on="on" class="vcart-right-section">Terms of Use</a>
                                                </template>
                                                Opens in new window
                                            </v-tooltip>
                                        </div>
                                    </template>
                                </v-checkbox>
                            </v-col>
                        </v-row>
                    </v-card-text>
                    <v-card-actions class="cart-btn-btm">
   
      <v-btn    :disabled="loading"   @click="BusinessformSubmit" color="red" large block class="text-white" >Register</v-btn>

      <v-progress-circular
      v-if="loading"
      indeterminate
      color="primary"
      size="40"
      width="4"
      class="ma-2"
    ></v-progress-circular>
                    </v-card-actions>
                </v-card>
            </v-form>
            <v-snackbar v-model="snackbar" :timeout="timeout">{{ text }}
                <template v-slot:action="{}">
                    <v-btn color="blue" text @click="snackbar = false">Close</v-btn>
                </template>
            </v-snackbar>
        </v-col>
    </v-row>
</v-container>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    name: "BusinessRegister",
    data() {
        return{
            areaSearch: '',
            areas: [],
            loadingAreas: false,
            loading: false,
            formData:{
                name: '',
                username: '',
                subcategory_id: '',
                cat_id: '',
                mobile_phone: '',
                country_id: '',
                state_id: '',
                city_id: '',
                area: '',
                areaSearch: '',
                refferal_code:'',
                email:'',
                password: '',
                confirm_password:'',
                address: '',
                agree_policy: '',
                user_role: 1,
                user_status: 1,
            },
            getimage:[],
            url: "https://bizlx.s3.ap-south-1.amazonaws.com", // Define CDN URL

            allcategories:[],
            allmaincategories:[],
            allstates:[],
            countries:[],
            // catId: '',
            cities:[],
            userNameVerifyMessage:'',
            emailVerifyMessage:'',
            phoneVerifyMessage:'',
            valid: false,
            snackbar: false,
            text: '',
            showPassword: false,
            showPassword2: false,
            timeout: '',
            customNoDataText: 'Please Select State First',
            customNoDataText1: 'Select Category',
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
                v => !!v || 'Category name is required.',
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
                // v => !!v || 'Password is required.',
                // v => /[A-Z]/.test(v) || 'Use upper case.',
                // v => /[a-z]/.test(v) || 'Use lower case.',
                // v => /[0-9]/.test(v) || 'Use number.',
                // v => /[#?!@$%^&*-]/.test(v) || 'Use special character.',
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
            loggedIn:false,
            user_role:'',
        }
    },
    created() {
        this.loggedIn = this.$store.state.isAuthenticated;
        this.user_role = this.$store.state.userData.user_role;
        if(this.loggedIn === true){
            if(this.user_role == 0){
                window.location.href = '/home'
            }
            if(this.user_role == 1){
                window.location.href = '/business/profile'
            }
            if(this.user_role == 2){
                window.location.href = '/reseller/business-list';
            }
            if(this.user_role == 3){
                window.location.href = '/admin/dashboard';
            }
            if(this.user_role == 4){
                window.location.href = '/admin/dashboard';
            }
        }
    },
    mounted() {
        this.getbusinessimage();
        // this.AllCats();
      //  this.AllSubCats();
        this.All_Countries();
        this.allmaincats();
    },

    methods: {
        // getbusinessimage(){
        //     axios.get('/api/profile/details')
        //         .then((resp)=>{
        //             this.getimage = resp.data.profile_details;
        //         });
        // },
        getbusinessimage() {
    axios.get('/api/setting')
        .then((resp) => {
            if (resp.data && resp.data.business_reg_img) {
                this.getimage = [{
                    path: `${this.url}${resp.data.business_reg_img}` // Prepend CDN URL
                }];
            } else {
                console.error("Image path is missing from API response");
            }
        })
        .catch(error => {
            console.error("Error fetching reseller image:", error);
        });
},
        // AllCats() {
        //   if (this.formData.cat_id) {
        //     axios.get(`subcategories/${this.formData.cat_id}`)
        //         .then((resp) => {
        //           this.allcategories = resp.data;
        //         })
        //         .catch(error => {
        //           console.log(error);
        //         });
        //   } else {
        //     this.allcategories = [];
        //   }
        // },
        AllSubCats(event) {
            var value = event.target.value.trim(); // Trim to remove leading and trailing spaces
            console.log("Enter keys:",value);
            if (value.length > 0) {
            axios.get(`/api/keyword/subcategories?keyword=${value}`)
                .then((resp) => {
                    // Check if the input value is still not an empty string
                    if (event.target.value.trim().length > 0) {
                        this.allcategories = resp.data;
                    }
                });
            }else{
                this.allcategories = [];
            }
            // if (this.formData.cat_id) {
            // axios.get(`/api/subcategories`)
            //     .then((resp) => {
            //         this.allcategories = resp.data;
            //     })
            //     .catch(error => {
            //         console.log(error);
            //     });
            // } else {
            //   this.allcategories = [];
            // }
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
  const state = this.formData.state_id;
  axios.get(`/api/states/${state}`)
    .then(response => {
      this.cities = response.data;

      // 👇 Trigger default areas when city changes
      this.fetchDefaultAreas();
    });
},
        // AllStates(){
        //   axios.get('allstates')
        //       .then((resp)=>{
        //         this.allstates = resp.data.locations;
        //       });
        // },
        // getCities() {
        //   if (this.stateId) {
        //     axios.get(`states/${this.stateId}`)
        //         .then(response => {
        //           this.cities = response.data;
        //         })
        //         .catch(error => {
        //           console.log(error);
        //         });
        //   } else {
        //     this.cities = [];
        //   }
        // },

        userNameVerify(){ // verify username
            if(this.formData.username.length >= 4){
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
  this.loading = true;
  const config = {
    headers: { 'content-type': 'multipart/form-data' }
  };

  // Fix: convert area object to string before sending
  if (typeof this.formData.area === 'object' && this.formData.area !== null) {
    this.formData.area = this.formData.area.display_name || '';
  }

  await axios.post('/api/business/register', this.formData, config)
    .then((resp) => {
      this.loading = false;
      this.snackbar = true;
      this.text = resp.data.message;
      this.timeout = 3000;
      window.location.href = '/login';
    }).catch(error => {
      const data = error.toJSON();
      if (data.status == 400) {
        this.snackbar = true;
        this.text = "something went wrong.";
        this.timeout = 3000;
        this.loading = false;
      }
    });

  this.$refs.form.validate();
},

// onAreaSearch(val) {
//     if (val.length >= 3) {
//       this.fetchAreas(val); // 🔍 search query
//     } else if (val.length === 0 && this.formData.city_id) {
//       this.fetchDefaultAreas(); // 🧭 show defaults when empty
//     } else {
//       this.areas = [];
//     }
//   },
onAreaSearch(val) {
  if (val.length >= 3) {
    this.fetchAreas(val);
  }
},
fetchAreas(query) {
  this.loadingAreas = true;
  const cityName = this.cities.find(c => c.id === this.formData.city_id)?.city;

  axios.get(`https://nominatim.openstreetmap.org/search?q=${query}, ${cityName}, India&format=json&limit=7`)
    .then((resp) => {
      this.areas = resp.data || [];
    })
    .finally(() => {
      this.loadingAreas = false;
    });
},
fetchDefaultAreas() {
  this.loadingAreas = true;
  const cityName = this.cities.find(c => c.id === this.formData.city_id)?.city;

  if (!cityName) return;

  // Only structured search (no 'q')
  axios.get(`https://nominatim.openstreetmap.org/search?city=${cityName}&country=India&format=json&limit=5`)
    .then((resp) => {
      this.areas = resp.data || [];
    })
    .catch(() => {
      this.areas = [];
    })
    .finally(() => {
      this.loadingAreas = false;
    });
}




    }
}
</script>

<style scoped>

</style>