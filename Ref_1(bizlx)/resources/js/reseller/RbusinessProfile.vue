<template>
    <div class="container">
      <div class="row">
        <div class="col-3">
          <h4 class="ms-4">Profile</h4>
        </div>
        <div class="col-6">
          <v-snackbar v-model="snackbar" :timeout="timeout" type="success" border="left" elevation="3">
            <template v-slot:action="{ attrs }">
              <v-btn color="blue"  v-bind="attrs" text @click="snackbar = false">Close</v-btn>
            </template>
            {{text}}
          </v-snackbar>
        </div>
      </div>
      <v-tabs v-model="tab">
          <v-tab>General</v-tab>
          <v-tab>Business Information</v-tab>
          <v-tab>Change Password</v-tab>
          <v-tab>Account Information</v-tab>
      </v-tabs>
      <v-tabs-items v-model="tab">
              <v-tab-item>
                <v-card color="basil" flat >
                  <v-form ref="form" v-model="valid">
                    <v-row>
                      <v-col cols="12" md="12">
                        <div v-if="files != '' ">
                          <v-avatar size="100" v-for="(file, f) in files" :key="f">
                            <img :ref="'file'"  style="display:block">
                          </v-avatar>
                        </div>
                        <div v-else-if="bprofiles.user_avatar">
                            <v-avatar size="100">
                            <img :src="url + bprofiles.user_avatar">
                            </v-avatar>
                        </div>
                        <div v-else>
                            <v-avatar size="100" color="error">
                            <span class="white--text fw-bold text-h4">{{bprofiles.name.charAt(0)}}{{firstCharOfLastWord}}</span>
                            </v-avatar>
                        </div>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-file-input label="Add Image/logo" v-model="files" multiple small-chips accept="image/*" @change="addFiles" v-on:click="isHidden = true"></v-file-input>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field label="Enter your business name" v-model="bprofiles.name" :rules="nameRules" :counter="100" required ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field type="email" label="Enter e-mail address" v-model="bprofiles.email" :rules="emailRules" required ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field label="Enter your username" v-model="bprofiles.username" :rules="usernameRules" :counter="80" required ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field label="Enter mobile" v-model="bprofiles.mobile_phone" :rules="mobileRules" :counter="10" required ></v-text-field>
                      </v-col>

                      <!-- <v-switch v-model="isFeatured" label="Feature this Business" color="success"></v-switch>

<div v-show="true">
  <v-autocomplete
    v-model="selectedCities"
    :items="featureAllCities"
    item-text="city"
    item-value="id"
    label="Select Featured Cities"
    multiple
    chips
    clearable
    :disabled="!isFeatured"
  ></v-autocomplete>

  <v-autocomplete
    v-model="selectedCats"
    :items="categories"
    item-text="cat_name"
    item-value="id"
    label="Select Featured Categories"
    multiple
    chips
    clearable
    :disabled="!isFeatured"
  ></v-autocomplete>
</div>

 -->

                    </v-row>
                    <v-btn class="my-2 ms-4" color="error"  @click="updateProfileByreseller">UPDATE</v-btn>
                  </v-form>
                </v-card>
              </v-tab-item>
              <v-tab-item > 
                <v-card color="basil" flat >
                  <v-form ref="form" v-model="valid">
                    <v-row>
                        <v-col cols="12" md="6">
                            <v-autocomplete label="Select Category" v-model="catId" @change="getsubCategory()" @selected="getsubCategory()" :rules="catRules" :items="categories" item-text="cat_name" item-value="id" autocomplete="on" dense></v-autocomplete>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-autocomplete  label="Select Sub Category" v-model="subids" :rules="subcatRules" :items="subcategories" item-text="subcat_name" item-value="id"></v-autocomplete>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-autocomplete label="Select Country" v-model="country_id" @change="getStateOnChange()"
                          @selected="getStateOnChange()" :rules="countryRules" :items="countries" item-text="country" item-value="id"></v-autocomplete>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-autocomplete label="Select State" v-model="stateId" @change="getCities()"
                                        @selected="getCities()" :rules="stateRules" :items="allstates" item-text="state" item-value="id"></v-autocomplete>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-autocomplete label="Select City" v-model="bprofiles.city_id" :rules="cityRules" :items="allcities" item-text="city" item-value="id"></v-autocomplete>
                      </v-col>
                      <v-col cols="12" md="6">
                    <!-- <v-autocomplete
  label="Select Area/Sector"
  dense
  clearable
  v-model="bprofiles.area"
  :items="areas"
  item-text="display_name"
  item-value="display_name"
  :search-input.sync="areaSearch"
  @update:search-input="onAreaSearch"
  :loading="loadingAreas"
  :filter="() => true"       
  no-data-text="No matching areas"
  prepend-inner-icon="mdi-map-marker"
/> -->
<v-autocomplete
  v-model="bprofiles.area"
  :items="areas"
  item-text="display_name"
  item-value="display_name"
  label="Select Area"
  clearable
  :search-input.sync="areaSearch"
  @update:search-input="onAreaSearch"
  :loading="loadingAreas"
  :filter="() => true"
>
  <template v-slot:no-data>
    <v-list-item>
      <v-list-item-content class="d-flex justify-center">
        <!-- show spinner while loading, else text -->
        <div v-if="loadingAreas">
          <v-progress-circular indeterminate size="20"></v-progress-circular>
        </div>
        <div v-else class="text-center">
          No matching sectors/phases
        </div>
      </v-list-item-content>
    </v-list-item>
  </template>
</v-autocomplete>

                    </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field  label="Enter GST Number" v-model="bprofiles.gst" required ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="6" >
                        <v-text-field type="number" v-model="bprofiles.mobile_phone" :rules="mobileRules" :counter="10" label="Enter Whatsapp Number" required ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="6" >
                        <v-text-field type="text" v-model="bprofiles.live_location" label="Enter Live Url" required ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="12" >
                        <v-textarea rows="2" row-height="20" v-model="bprofiles.address" :rules="addressRules" label="Enter Address"></v-textarea>
                      </v-col>
                      <v-col cols="12" md="12" >
                        <label class="fw-bold">About</label>
                        <v-card v-if="bprofiles.about == null">
                          <v-card-text>
                            <v-textarea v-model="bprofiles.about"
                                        :rules="aboutRules" counter auto-grow>
                            </v-textarea>
                          </v-card-text>
                        </v-card>
                        <v-card v-else>
                          <v-card-text>
                            <v-textarea v-model="bprofiles.about" :value="sanitizeText(bprofiles.about)"
                                        :rules="aboutRules" counter auto-grow>
                            </v-textarea>
                          </v-card-text>
                        </v-card>
  <!--                      <ckeditor v-model="bprofiles.about"></ckeditor>-->
                      </v-col>
                      <v-col cols="12" md="12" >
                        <label class="fw-bold">Services</label>
                        <v-card>
                          <v-card-text>
                            <div v-if="bprofiles.service_text == null" class="servlist">
                              <div v-for="(serv, index) in noservs" :key="index">
                                <v-text-field v-model="noservs[index]" dense counter :rules="serviceRules" :id="String(index)">
                                  <template v-slot:append>
                                    <v-icon @click="removenoServ(index)" small color="red">mdi-trash-can</v-icon>
                                  </template>
                                </v-text-field>
                              </div>
                              <v-btn small color="primary" @click="addMorenoserv()">Add More</v-btn>
                            </div>
                            <div v-else class="servlist">
                              <div v-for="(serv, index) in servs" :key="index">
                                <v-text-field v-model="servs[index]" dense counter :rules="serviceRules" :id="String(index)">
                                  <template v-slot:append>
                                    <v-icon @click="removeServ(index)" small color="red">mdi-trash-can</v-icon>
                                  </template>
                                </v-text-field>
                              </div>
                              <v-btn small color="primary" @click="addMoreserv()">Add More</v-btn>
                            </div>
                          </v-card-text>
                        </v-card>
  <!--                       <ckeditor v-model="bprofiles.service_text"></ckeditor>-->
                      </v-col>
                      <v-col cols="12" md="12" v-for="(tab, index) in tabsarray" :key="'tab-' + index">
                        <label class="fw-bold">{{ tab.title }}</label>
                        <ckeditor :editor="tab" :name="'tab' + index" v-model="tab.tab_content"></ckeditor>
                      </v-col>
                    </v-row>
                    <!-- <v-btn class="my-2 ms-4" color="error" @click="updateProfile2byreseller">UPDATE</v-btn> -->
                     <!-- after -->
                    <v-btn
                      class="my-2 ms-4"
                      color="error"
                      :loading="loadingBusinessInfo"
                      :disabled="loadingBusinessInfo"
                      @click="updateProfile2byreseller"
                    >
                      UPDATE
                    </v-btn>

                  </v-form>
                </v-card>
              </v-tab-item>
              <v-tab-item >
                <v-card color="basil" flat >
                  <v-form ref="form" v-model="valid">
                    <v-row>
                      <v-col cols="12" md="12">
                        <!-- <v-text-field v-model="password" type="password" :rules="passwordRules" label="Change Password" hint="Leave empty if you don't want to change the password." required ></v-text-field> -->
                        <v-text-field clearable v-model="password" :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"
                                            :rules="passwordRules"
                                            :type="show1 ? 'text' : 'password'" name="input-10-1"
                                            label="Password"
                                            hint="Leave empty if you don't want to change the password."
                                            persistent-hint
                                            @click:append="show1 = !show1"
                              ></v-text-field>
                      </v-col>
                    </v-row>
                    <v-btn class="my-2" color="error" :disabled="!valid"@click="updatePasswordbyreseller">UPDATE</v-btn>
                  </v-form>
                </v-card>
              </v-tab-item>
              <v-tab-item>
                <!-- Account-related information for the reseller -->
             
                <template v-if="reseller.length > 0">
                    <v-text-field
                      label="Reseller Name"
                      :value="reseller[0]?.name"
                      readonly
                    ></v-text-field>

                    <v-text-field
                      label="Reseller Email"
                      :value="reseller[0]?.email"
                      readonly
                    ></v-text-field>

                    <v-text-field
                      label="Reseller Phone Number"
                      :value="reseller[0]?.mobile_phone"
                      readonly
                    ></v-text-field>

                    <v-text-field
                      label="Reseller Address"
                      :value="reseller[0]?.address"
                      readonly
                    ></v-text-field>

                    <v-text-field
                      label="Reseller ID"
                      :value="reseller[0]?.username"
                      readonly
                    ></v-text-field>
                   </template>

                <template v-else>
                  <v-text-field
                    label="Business Name"
                    :value="bprofiles.name"
                    readonly
                  ></v-text-field>

                  <v-text-field
                    label="Business Email"
                    :value="bprofiles.email"
                    readonly
                  ></v-text-field>
                </template>
                <v-btn @click="openModel" class="my-2" color="error">CHANGE RESELLER</v-btn>
               </v-tab-item>
        
             <!-- Dialog for Changing Reseller -->
             <v-dialog v-model="showModel" persistent width="800">
        <v-card>
          <v-card-title>
            Change Reseller
          </v-card-title>
          <v-card-text>
            <v-form ref="form" v-model="valid">
                <v-row >
                  <v-col cols="12" md="12" class="py-0" >
                    <v-textarea :rules="reasonRules" v-model="reason_text" rows="3" row-height="20" label="Reason For Change"></v-textarea>
                  </v-col>
                  <v-col cols="12" md="6" class="py-0">
                    <v-text-field dense :rules="new_reseller_idRules" v-model="new_reseller_id" label="Switch to Reseller ID"></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6" class="py-0" v-if="reseller.length > 0 ">
                    <v-text-field dense v-model="reseller[0].username" readonly label="Old Reseller ID"></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6" class="py-0" v-else></v-col>
                  <v-col cols="12" md="12" class="py-0" v-if="reseller.length > 0 ">
                    <v-text-field  v-model="reseller[0].email" readonly label="Old Reseller Email"></v-text-field>
                  </v-col>
                  
                  <v-col cols="12" md="12" class="py-0" v-else></v-col>
                  
                </v-row>
            </v-form>
          </v-card-text>
          <v-card-actions class="text-end">
          <v-btn class="my-2 me-3" color="success"  @click="changeResellerbyreseller">UPDATE</v-btn>
          <v-btn @click="showModel = false" class="my-2 me-3" color="error" >CLOSE</v-btn>
        </v-card-actions>
        </v-card>
      </v-dialog>
    </v-tabs-items> 
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        isFeatured: false,   
        featureAllCities: [],         // true when toggle is ON
      selectedCities: [],           // [1, 5, 9]
      selectedCats: [],  
        selectedFeatureCities: [],
        selectedFeatureCategories: [],
     userdata: {},  
     reviews: [],
     reseller: [], 
     bprofiles: {name: ''},
      formData: [],
      country_id: '',
      stateId: '',
      catId: '',
      allcities: [],
      allstates: [],
      imagepath: '',
      isimageerror: false,
      password  : '',
      new_reseller_id: '',
      reason_text: '',
      readers: [],
      servs: [],
      noservs: [],
      subids: [],
      tabsarray: [],
      countries:[],
      snackbar: false,
      showModel: false,
      timeout: 3000,
      reasonRules: [],
      new_reseller_idRules: [],
      text: '',    
      res: 'RES',  
      tab: null,
      valid: false,
      files: [],
      isHidden: false,
      categories: [],
      subcategories: [],
      areaSearch:   '',
    areas:        [],     // will hold your default & search results
    loadingAreas: false,
      url:'https://bizlx.s3.ap-south-1.amazonaws.com',
      nameRules: [
          v => !!v || 'Store/Bussine Name is required.',
          v => v?.length <= 100 || 'Name must be less than 100 characters.',
        ],
      catRules: [
          v => !!v || 'Category is required.',
        ],
        subcatRules: [
          v => !!v || 'Sub category is required.',
        ],
        stateRules: [
          v => !!v || 'State is required.',
        ],
        cityRules: [
          v => !!v || 'City is required.',
        ],
         countryRules: [
          v => !!v || 'Country is required.',
        ],
        usernameRules: [
          v => !!v || 'Username is required.',
          v => v?.length <= 80 || 'Username must be less than 80 characters.',
          v => (v?.split(' ').length <= 1) || 'space not allowed',
          v => /^[\w\s]+$/.test(v) || 'Username may only contain letters, numbers, dashes and underscores'
        ],
        passwordRules: [
          v => !!v || 'Password is required.',
          // v => /[A-Z]/.test(v) || 'Use upper case.',
          // v => /[a-z]/.test(v) || 'Use lower case.',
          // v => /[0-9]/.test(v) || 'Use number.',
          // v => /[#?!@$%^&*-]/.test(v) || 'Use special character.',
          v => v?.length >= 8 || 'Password must be greater than 8 characters.',
          v => v?.length <= 15 || 'Password must be less than 15 characters.',
        ],
        loadingBusinessInfo: false,
        mobileRules: [
          v => !!v || 'Mobile number is required.',
          v => v?.length != 9 ||  'Mobile number must be 10 characters.',
          //v => v.length > 10 || 'Mobile number must be 103 characters.',
        ],
        new_reseller_idRules: [
          v => !!v || 'Reseller Id is required.',
        ],
        addressRules: [
          v => !!v || 'Address is required.',
        ],
        emailRules: [
          v => !!v || 'E-mail is required.',
          v => /.+@.+/.test(v) || 'E-mail must be valid.',
        ],
        reasonRules: [
          v => !!v || 'Reason is required.',
          v => v?.length <= 100 || 'Reason must be less than 100 characters.',
        ],
        // noservs:["Service 1","Service 2"],
        servs:[],
        serviceRules: [
          v => v?.length <= 50 || 'Service name must be less than 50 characters.',
        ],
        aboutRules: [
          v => !!v || 'About Content Required.',
          v => v?.length <= 800 || 'About content must be less than 800 characters.',
        ],
      };
    },
    created(){
      this.userdata = localStorage.getItem('userId');
      this.getBusinessprofilebyreseller();
      this.getCategory();  
      this.getsubCategory();
      this.getStates();
      this.getCities();
      this.getCountries();
    },
    mounted() {
      this.userdata = localStorage.getItem('userId');
      this.getBusinessprofilebyreseller()
    .then(() => {
      // once bprofiles + allcities/allstates/countries are in place…
      this.initAreas()
    }) // Fetch data when component is mounted
  this.getCategory();  
  this.getsubCategory();
  this.getStates();
  this.getCities();
  this.getCountries();
  this.getAllcities();

    },
    beforeMount() {
    // Ensure `bprofiles.name` is a valid string
    if (typeof this.bprofiles.name !== 'string') {
      this.bprofiles.name = ''; // Initialize to an empty string if undefined or not a string
    }
  },
  watch: {
  country_id:          'initAreas',
  stateId:             'initAreas',
  'bprofiles.city_id': 'initAreas',
},

    computed: {
      lastWord() {
        const words = this.bprofiles.name.split(' ');
        return words[words.length - 1];
      },
      firstCharOfLastWord() {
        const lastWord = this.lastWord;
        return lastWord ? lastWord.charAt(0) : '';
      },
      cityName() {
    return this.allcities.find(c => c.id === this.bprofiles.city_id)?.city || ''
  },
  stateName() {
    return this.allstates.find(s => s.id === this.stateId)?.state || ''
  },
  countryName() {
    return this.countries.find(c => c.id === this.country_id)?.country || ''
  },
  },
    methods: {
      
      sanitizeText(text) {
        // Replace HTML tags with an empty string
        return text.replace(this.htmlText, '');
      },
      getBusinessprofilebyreseller() {
        const uid = JSON.parse(localStorage.getItem('userId'));
        axios.post('/api/reseller/business/profile', { userId: uid })
          .then((resp) => {
            this.bprofiles = resp.data.buser;
            this.country_id = resp.data.buser.country_id;
            this.stateId = resp.data.buser.state_id;
            this.catId = resp.data.buser.cat_id;
            this.imagepath = resp.data.buser.user_avatar;
            this.reseller = resp.data.reseller_data; // Ensure this is correct
            this.subids = resp.data.buser.subcat_id;
            this.servs = resp.data.buser.service_text;
            this.getCities();
            this.getsubCategory();
      this.isFeatured = resp.data.businessfeature_status === 1;
      if (resp.data.feature_cities) {
        this.selectedCities = resp.data.feature_cities.map(c => c.id);
      }

      if (resp.data.feature_categories) {
        this.selectedCats = resp.data.feature_categories.map(c => c.id);
      }

            // for (let i = 0; i < resp.data.subcatids.length; i++) {
            //   this.subids.push(resp.data.subcatids[i].id);
            // }
          })
          .catch(error => {
            console.error('API Error:', error);
          });
      },

      changeResellerbyreseller(){
        const uid = JSON.parse(localStorage.getItem('userId'));
        const config = {
          headers: { 'content-type': 'multipart/form-data' }
        }
        let data = {
          userId:uid.id,
          old_reseller_id: this.reseller.length > 0 ? this.reseller[0].username : null,
          new_reseller_id: this.new_reseller_id,
          reason_text: this.reason_text,
        };

        axios.post('/api/reseller/update/reseller/id',data,config)
            .then((resp) =>{
              if( resp.data.success == true ){
                this.snackbar = true;
                this.text = resp.data.message;
                this.showModel = false;
                this.timeout = 3000;
                this.getBusinessprofilebyreseller();
              }else{
                this.snackbar = true;
                this.text = resp.data.message;
                this.showModel = false;
                this.timeout = 3000;
                this.getBusinessprofilebyreseller();
              }
  
            });
        this.$refs.form.validate();
        this.$refs.form.reset();
      },
      updatePassword() {
        axios.post('/api/update/business/password',{
          password: this.password,
        },)
            .then((resp) =>{
              this.snackbar = true;
              this.text = resp.data.buser;
              this.timeout = 3000;
            })
            .catch(error => {
              // Handle the error
              var data = error.toJSON();
              if(data.status == 400){
                this.snackbar = true;
                this.text = "something went wrong.";
                this.timeout = 3000;
              }
            });
        this.$refs.form.validate();
        this.$refs.form.reset();
      },
  updateProfileByreseller() {
    const formData = new FormData();
formData.append('name', this.bprofiles.name);
formData.append('username', this.bprofiles.username);
formData.append('email', this.bprofiles.email);
formData.append('mobile_phone', this.bprofiles.mobile_phone);
formData.append('userId', this.userdata.id || JSON.parse(localStorage.getItem('userId'))?.id);
formData.append('user_avatar', this.files[0] || '');

formData.append('is_featured', this.isFeatured ? 1 : 0);
this.selectedCities.forEach(id => formData.append('feature_cities[]', id));
this.selectedCats.forEach(id => formData.append('feature_categories[]', id));

axios.post('/api/reseller/update/business/profile', formData)
  .then((resp) => {
    this.snackbar = true;
    this.text = resp.data.buser;
    this.timeout = 3000;
  })
  .catch(error => {
    this.snackbar = true;
    this.text = "Something went wrong.";
    this.timeout = 3000;
    console.error("Upload error:", error);
  });

        this.$refs.form.validate();
      },
      updatePasswordbyreseller() {
        const userIdStr = localStorage.getItem('userId');
            if (userIdStr) {  // Check if userIdStr is not null or undefined
            const userId = JSON.parse(userIdStr);
            if (userId && userId.id) {  // Check if userId is an object and has an id property
            axios.post('/api/reseller/update/business/password',{
              userId: userId.id,
              password: this.password,
            },)
            .then((resp) =>{
              this.snackbar = true;
              this.text = resp.data.buser;
              this.timeout = 3000;
            })
            .catch(error => {
              // Handle the error
              var data = error.toJSON();
              if(data.status == 400){
                this.snackbar = true;
                this.text = "something went wrong.";
                this.timeout = 3000;
              }
              else {
                    this.snackbar = true;
                    this.text = "An unexpected error occurred.";
                    this.timeout = 3000;
                }
            });
        this.$refs.form.validate();
        this.$refs.form.reset();
      } 
    } 
      },
      getAllcities() {
        axios.get(`/api/getallcities`)
          .then(response => {
            this.featureAllCities = response.data.cities;
          })
          .catch(error => {
            console.error("Error loading all cities:", error);
          });
      },
      getCategory() {
        this.subids = [];
        axios.get('/api/category')
            .then((resp) =>{
              this.categories = resp.data.categories;
            })
      },
      getsubCategory() {
        
        if (this.catId) {
          axios.get(`/api/subcategories?id=${this.catId}`)
              .then((resp) => {
                this.subcategories = resp.data;
              })
              .catch(error => {
                console.log(error);
              });
        } else {
          this.subcategories = [];
        }
        
      },
      getCountries(){
        axios.get('/api/countries')
          .then((resp)=>{
            this.countries = resp.data.countries;
          });
      },
      getStates() {
        axios.get('/api/allstates')
            .then((resp) =>{
              this.allstates = resp.data.locations;
            })
      },
      getStateOnChange(){
        var country_id = this.country_id;
        axios.get(`/api/get/states/${country_id}`)
          .then(response => {
              this.allstates = response.data;
        });
      },
      getCities() {
        if (this.stateId) {
          axios.get(`/api/states/${this.stateId}`)
              .then(response => {
                this.allcities = response.data;
              })
              .catch(error => {
                console.log(error);
              });
        }
          else {
          this.allcities = [];
        }
      },
        addFiles(){
          this.files.forEach((file, f) => {
            this.readers[f] = new FileReader();
            this.readers[f].onloadend = () => {
              let fileData = this.readers[f].result
              let imgRef = this.$refs.file[f]
              imgRef.src = fileData
              // send to server here...
            }
            this.readers[f].readAsDataURL(this.files[f]);
          })
        },
        openModel() {
        this.showModel = true;
      },
      addMoreserv(){
        this.servs.push('');
      },
      addMorenoserv(){
        this.noservs.push('');
      },
      removeServ(index){
        this.servs.splice(index, 1);
      },
      removenoServ(index){
        this.noservs.splice(index, 1);
      },
      addServs(){
        const config = {
          headers: { 'content-type': 'multipart/form-data' }
        }
        let data = {
          services:this.servs,
        };

      },
      updateProfile2byreseller() {
        this.loadingBusinessInfo = true;
    const userIdString = localStorage.getItem('userId');
    if (!userIdString) {
      return;
    }
    const uid = JSON.parse(userIdString);
    if (!uid || !uid.id) {
      return;
    }

    let services = this.servs || this.noservs;
    let formData = new FormData();
    formData.append('userId', uid.id);
    formData.append('cat_id', this.catId);
    formData.append('subcat_id', this.subids);
    formData.append('state_id', this.bprofiles.state_id);
    formData.append('city_id', this.bprofiles.city_id);
    formData.append('gst', this.bprofiles.gst);
    formData.append('address', this.bprofiles.address);
    formData.append('about', this.bprofiles.about);
    formData.append('mobile_phone', this.bprofiles.mobile_phone);
    formData.append('service_text', services);

    axios.post('/api/reseller/update/business/profile2', formData, { headers: { 'content-type': 'multipart/form-data' } })
      .then((resp) => {
        this.snackbar = true;
        this.text = resp.data.buser;
        this.timeout = 3000;
        this.getBusinessprofilebyreseller();
      })
      .catch(error => {
        var data = error.toJSON();
        if (data.status === 400) {
          this.snackbar = true;
          this.text = "Something went wrong.";
          this.timeout = 3000;
        }
      })
      .finally(() => {
        this.loadingBusinessInfo = false; // Reset loading state
      });
},
initAreas() {
    this.fetchDefaultAreas()
  },

  fetchDefaultAreas() {
    // nothing to do if they haven’t picked a city yet
    if (!this.bprofiles.city_id) {
      this.areas = []
      return
    }

    // build "City, State, Country"
    const q = [ this.cityName, this.stateName, this.countryName ]
      .filter(Boolean)
      .join(', ')

    this.loadingAreas = true
    axios.get('https://nominatim.openstreetmap.org/search', {
      params: { q, format: 'json', limit: 5 }
    })
    .then(({ data }) => {
      this.areas = data.map(p => ({
        display_name: p.display_name
      }))
    })
    .catch(() => {
      this.areas = []
    })
    .finally(() => {
      this.loadingAreas = false
    })
  },

  // replace your existing onAreaSearch with this
  onAreaSearch(val) {
    this.areaSearch = val

    // if they clear or type < 3 chars, fallback to defaults
    if (!val || val.length < 3) {
      return this.fetchDefaultAreas()
    }

    // prefix with “, City, State, Country”
    const q = [
      val.trim(),
      this.cityName,
      this.stateName,
      this.countryName
    ]
      .filter(Boolean)
      .join(', ')

    this.loadingAreas = true
    axios.get('https://nominatim.openstreetmap.org/search', {
      params: { q, format: 'json', limit: 50 }
    })
    .then(({ data }) => {
      this.areas = data.map(p => ({
        display_name: p.display_name
      }))
    })
    .catch(() => {
      this.areas = []
    })
    .finally(() => {
      this.loadingAreas = false
    })
  },

  // … all your other methods …


    },
  }
  </script>
  
