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
                <!-- <v-form ref="form" v-model="valid"> -->
                    <v-form
            ref="generalForm"
            v-model="validGeneral"
            lazy-validation
          >
                    <v-row>
                      <v-col cols="12" md="12">
                        <div v-if="files != '' ">
                          <v-avatar size="100" v-for="(file, f) in files" :key="f">
                            <img :ref="'file'"  style="display:block">
                          </v-avatar>
                        </div>
                        <div v-else-if="bprofiles.user_avatar != null ">
                          <v-avatar size="100">
                            <img :src=(url+bprofiles.user_avatar)>
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
                        <v-text-field clearable label="Enter your business name" v-model="bprofiles.name" :rules="nameRules" :counter="100" required ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field clearable type="email" label="Enter e-mail address" v-model="bprofiles.email" :rules="emailRules" required ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field clearable label="Enter your username" v-model="bprofiles.username" :rules="usernameRules" :counter="80" required ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field clearable label="Enter mobile" v-model="bprofiles.mobile_phone" :rules="mobileRules" :counter="10" required ></v-text-field>
                      </v-col>

                      <!-- <v-switch v-model="isFeatured" label="Feature this Business" color="success"></v-switch>

                      <v-autocomplete
                  v-model="selectedCities"
                  :items="featureAllCities"
                  item-text="city"
                  item-value="id"
                  label="Select Cities"
                  multiple
                  chips
                  clearable
                ></v-autocomplete>
                  <v-autocomplete
                  v-model="selectedCats"
                  :items="categories"
                  item-text="cat_name"
                  item-value="id"
                  label="Select Categories"
                  multiple
                  chips
                  clearable
                ></v-autocomplete> -->
                    </v-row>
                    <div v-if="userdata.id != null">
                      <v-btn class="my-2 ms-4" color="error" :disabled="!valid" @click="updateProfileByreseller">UPDATE</v-btn>
                    </div>
                    <div v-else>
                      <!-- <v-btn class="my-2 ms-4" color="error" :disabled="!valid" @click="updateProfile1">UPDATE</v-btn> -->
                      <v-btn class="my-2 ms-4" color="error" :disabled="!validGeneral || loading" :loading="loading" @click="updateProfile1">
                        <v-progress-circular v-if="loading" indeterminate color="white" size="20"></v-progress-circular>
                        <span v-else>UPDATE</span>
                      </v-btn>
                    </div>
                  </v-form>
              </v-card>
            </v-tab-item>
            <v-tab-item >
              <v-card color="basil" flat >
                <!-- <v-form ref="form" v-model="valid"> -->
                  <v-form
          ref="businessForm"
          v-model="validBusinessInfo"
          lazy-validation
        >
                  <v-row>
                    <v-col cols="12" md="6">
                      <v-autocomplete clearable label="Select Category" v-model="catId" @change="getsubCategory()" @selected="getsubCategory()" :rules="catRules" :items="categories" item-text="cat_name" item-value="id" autocomplete="on" dense></v-autocomplete>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-autocomplete clearable label="Select Sub Category" v-model="subids" :rules="subcatRules" :items="subcategories" item-text="subcat_name" item-value="id"></v-autocomplete>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-autocomplete clearable label="Select Country" v-model="country_id" @change="getStateOnChange()"
                        @selected="getStateOnChange()" :rules="countryRules" :items="countries" item-text="country" item-value="id"></v-autocomplete>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-autocomplete clearable label="Select State" v-model="stateId" @change="getCities()"
                                      @selected="getCities()" :rules="stateRules" :items="allstates" item-text="state" item-value="id"></v-autocomplete>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-autocomplete clearable label="Select City" v-model="bprofiles.city_id" :rules="cityRules" :items="allcities" item-text="city" item-value="id"></v-autocomplete>
                    </v-col>
                    <v-col cols="12" md="6">
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
                    <v-col cols="12" md="4">
                      <v-text-field clearable label="Enter GST Number" v-model="bprofiles.gst" required ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-text-field clearable type="number" v-model="bprofiles.mobile_phone" :rules="mobileRules" :counter="10" label="Enter Whatsapp Number" required ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-text-field type="text" clearable v-model="bprofiles.live_location" label="Enter Live Url" required ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="12" >
                      <v-textarea rows="2" clearable row-height="20" v-model="bprofiles.address" :rules="addressRules" label="Enter Address"></v-textarea>
                    </v-col>
                    <v-col cols="12" md="12" >
                      <label class="fw-bold">About</label>
                      <v-card v-if="bprofiles.about == null">
                        <v-card-text>
                          <v-textarea clearable v-model="bprofiles.about"
                                      :rules="aboutRules" counter auto-grow>
                          </v-textarea>
                        </v-card-text>
                      </v-card>
                      <v-card v-else>
                        <v-card-text>
                          <v-textarea clearable v-model="bprofiles.about" :value="sanitizeText(bprofiles.about)"
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
                            <div v-for="(serv,index) in noservs" :key="index">
                              <v-text-field clearable v-model="noservs[index]" dense counter :rules="serviceRules" :id="index">
                                <template v-slot:append>
                                  <v-icon @click="removenoServ(index)" small color="red">mdi-trash-can</v-icon>
                                </template>
                              </v-text-field>
                            </div>
                            <v-btn small color="primary" @click="addMorenoserv()">Add More</v-btn>
                          </div>
                          <div v-else class="servlist">
                            <div v-for="(serv,index) in servs" :key="index">
                              <v-text-field clearable v-model="servs[index]" dense counter :rules="serviceRules" :id="index">
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
                    <!-- <v-col cols="12" md="12" v-for="(tab, index) in tabsarray" :key="'tab-' + index">
                      <label class="fw-bold">{{ tab.title }}</label>
                      <ckeditor :editor="tab" :name="'tab' + index" v-model="tab.tab_content"></ckeditor>
                    </v-col> -->
                  </v-row>
                  <div v-if="userdata.id != null">
                    <v-btn class="my-2 ms-4" color="error" :disabled="!valid" @click="updateProfile2byreseller">UPDATE</v-btn>
                  </div>
                  <div v-else>
                    <v-btn class="my-2 ms-4" color="error"   :disabled="!validBusinessInfo" @click="updateProfile2">UPDATE</v-btn>

                    <!-- <v-btn class="my-2 ms-4" color="error" :disabled="!infovalid" @click="updateProfile2">UPDATE</v-btn> -->
                    <!-- <v-btn class="my-2 ms-4" color="error" :disabled="!valid || loading" @click="updateProfile2">
                    <v-progress-circular v-if="loading" indeterminate color="white" size="20"></v-progress-circular>
                    <span v-else>UPDATE</span>
                  </v-btn> -->

                  </div>
                </v-form>
              </v-card>
            </v-tab-item>
            <v-tab-item >
              <v-card color="basil" flat >
                <v-form ref="form" v-model="passwordValid">
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
                  <div v-if="userdata.id != null">
                    <v-btn class="my-2" color="error" :disabled="!passwordValid" @click="updatePasswordbyreseller">UPDATE</v-btn>
                  </div>
                  <div v-else>
                    <v-btn class="my-2" color="error" :disabled="!passwordValid" @click="updatePassword">UPDATE</v-btn>
                  </div>
                </v-form>
              </v-card>
            </v-tab-item>
            <v-tab-item >
              <v-card color="basil" flat >
                <v-form>
                  <v-row>
                    <v-col cols="12" md="12" v-if="reseller.length > 0">
                      <h5 class="ms-4">Reseller
                        <span class="h6 text-muted">({{reseller[0].city}})</span>
                      </h5>
                    </v-col>
                    <v-col cols="12" md="12" v-else>
                      <h5 class="ms-4">Business
                        <span class="h6 text-muted">({{bprofiles.city}})</span>
                      </h5>
                    </v-col>
                    <v-col cols="12" md="12" v-if="reseller_data.length > 0">
                      <v-text-field  clearable
                        label="Reseller Name" 
                        v-model="reseller_data[0].name" 
                        readonly
                      ></v-text-field>
                    </v-col>

                      <v-col cols="12" md="12" v-else>
                        <v-text-field clearable label="Business Name" v-model="bprofiles.name" readonly></v-text-field>
                      </v-col>
                      <v-col cols="12" md="12" v-if="reseller.length > 0">
                        <v-text-field clearable label="Reseller Email" v-model="reseller[0].email"  readonly></v-text-field>
                      </v-col>
                      <v-col cols="12" md="12" v-else>
                        <v-text-field clearable label="Business Email" v-model="bprofiles.email" readonly></v-text-field>
                      </v-col>
                      <v-col cols="12" md="12" v-if="reseller.length > 0">
                        <v-text-field clearable label="Reseller Phone Number" v-model="reseller[0].mobile_phone" readonly></v-text-field>
                      </v-col>
                      <v-col cols="12" md="12" v-else>
                        <v-text-field clearable label="Business Phone Number" v-model="bprofiles.mobile_phone" readonly></v-text-field>
                      </v-col>
                      <v-col cols="12" md="12" v-if="reseller.length > 0">
                        <v-text-field clearable label="Reseller Address" v-model="reseller[0].address"  readonly></v-text-field>
                      </v-col>
                      <v-col cols="12" md="12" v-else>
                        <v-text-field clearable label="Business Address" v-model="bprofiles.address" readonly></v-text-field>
                      </v-col>
                      <v-col cols="12" md="12" v-if="reseller.length > 0">
                        <v-text-field clearable label="Reseller ID" v-model="reseller[0].username"  readonly></v-text-field>
                      </v-col>
                      <v-col cols="12" md="12" v-else></v-col>
                  </v-row>
                  <div v-if="reseller.length > 0" class="text-end">
                    <v-btn @click="openModel" class="my-2" color="error">CHANGE RESELLER</v-btn>
                  </div>
                 <div v-else></div>
                </v-form>
              </v-card>
            </v-tab-item>
          </v-tabs-items>
    <v-dialog v-model="showModel" persistent width="800">
      <v-card>
        <v-card-title class="text-h5 grey lighten-2">
          Change Reseller
        </v-card-title>
        <v-card-text>
          <v-form ref="form" v-model="valid">
              <v-row >
                <v-col cols="12" md="12" class="py-0" >
                  <v-textarea :rules="reasonRules" clearable v-model="reason_text" rows="3" row-height="20" label="Reason For Change"></v-textarea>
                </v-col>
                <v-col cols="12" md="6" class="py-0">
                  <v-text-field dense :rules="new_reseller_idRules" clearable v-model="new_reseller_id" label="Switch to Reseller ID"></v-text-field>
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
        <div class="text-end">
            <v-btn class="my-2 me-3" color="error" v-if="userdata.id != null" :disabled="!valid" @click="changeResellerbyreseller">UPDATE</v-btn>
            <v-btn class="my-2 me-3" color="error" v-else :disabled="!valid" @click="changeReseller">UPDATE</v-btn>
            <v-btn @click="showModel = false" class="my-2 me-3" color="error">CLOSE</v-btn>
        </div>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
import axios from "axios";
const nominatim = axios.create({
  baseURL: 'https://nominatim.openstreetmap.org',
  params: {
    format: 'json',
    addressdetails: 1,
    limit: 50
  }
})
export default {
  name: 'BussinessProfile',
  components: {},
  data() {
    return {
      isFeatured: false,   
      featureAllCities: [],         // true when toggle is ON
    selectedCities: [],           // [1, 5, 9]
    selectedCats: [],  
      selectedFeatureCities: [],
      selectedFeatureCategories: [],
      show1: false,
      loading: false,
      validGeneral: false,
    validBusinessInfo: false,
    validPassword: false,
      htmlText: /<[^>]+>/g,
      bprofiles: [],
      formData: [],
      userdata: [],
      reseller: [],
      subids: [],
      tabsarray: [],
      new_reseller_id: '',
      reason_text: '',
      imagepath: '',
      files: [],
      readers: [],
      isHidden: false,
      isimageerror: false,
      password  : '',
      valid: false,
      infovalid: false,
      categories: '',
      subcategories: '',
      countries:'',
      allcities: '',
      allstates: '',
      country_id: '',
      stateId: '',
      catId: '',
      reseller_data: [],
      activeFilePickerId: null,
      showDeleteAvatar: false,
      tab: null,
      showModel: false,
      snackbar: false,
      text: '',
      timeout: -1,
      res: 'RES',
      areaSearch: '',
    areas: [],         // will hold ["Sector 70, Sahibzada ...", ...]
    loadingAreas: false,
      url:'https://bizlx.s3.ap-south-1.amazonaws.com',
      // url:'http://127.0.0.1:8000',
      nameRules: [
        v => !!v || 'Store/Bussine Name is required.',
        v => v.length <= 100 || 'Name must be less than 100 characters.',
      ],
      reasonRules: [
        v => !!v || 'Reason is required.',
        v => v.length <= 100 || 'Reason must be less than 100 characters.',
      ],
      new_reseller_idRules: [
        v => !!v || 'Reseller Id is required.',
      ],
      addressRules: [
        v => !!v || 'Address is required.',
      ],
      catRules: [
        v => !!v || 'Category is required.',
      ],
      subcatRules: [
        v => !!v || 'Sub category is required.',
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
      usernameRules: [
        v => !!v || 'Username is required.',
        v => v.length <= 80 || 'Username must be less than 80 characters.',
        v => (v.split(' ').length <= 1) || 'space not allowed',
        v => /^[\w\s]+$/.test(v) || 'Username may only contain letters, numbers, dashes and underscores'
      ],
      mobileRules: [
        // v => !!v || 'Mobile number is required.',
        // v => v.length != 9 ||  'Mobile number must be 10 characters.',
        // v => v.length > 10 || 'Mobile number must be 10 characters.',
      ],

      emailRules: [
        v => !!v || 'E-mail is required.',
        v => /.+@.+/.test(v) || 'E-mail must be valid.',
      ],
      passwordRules: [
        v => !!v || 'Password is required.',
        // v => /[A-Z]/.test(v) || 'Use upper case.',
        // v => /[a-z]/.test(v) || 'Use lower case.',
        // v => /[0-9]/.test(v) || 'Use number.',
        // v => /[#?!@$%^&*-]/.test(v) || 'Use special character.',
        v => v.length >= 8 || 'Password must be greater than 8 characters.',
        v => v.length <= 15 || 'Password must be less than 15 characters.',
      ],
      noservs:["Service 1","Service 2"],
      servs:[],
      aboutRules: [
        v => !!v || 'About Content Required.',
        v => v.length <= 1000 || 'About content must be less than 1000 characters.',
      ],
      serviceRules: [
        v => v.length <= 50 || 'Service name must be less than 50 characters.',
      ],
    }
  },
  computed: {
    cityName() {
    return this.bprofiles.city || ''
  },
  stateName() {
    return this.bprofiles.state || ''
  },
  countryName() {
    return this.bprofiles.country || ''
  },
    lastWord() {
      const words = this.bprofiles.name.split(' ');
      return words[words.length - 1];
    },
    firstCharOfLastWord() {
      return this.lastWord.charAt(0);
    },
  },
  watch: {
    country_id:          'initAreas',
    stateId:             'initAreas',
    'bprofiles.city_id': 'initAreas'
  },
  computed: {
    cityName() {
      return this.allcities.find(c => c.id === this.bprofiles.city_id)?.city || ''
    },
    stateName() {
      return this.allstates.find(s => s.id === this.stateId)?.state || ''
    },
    countryName() {
      return this.countries.find(c => c.id === this.country_id)?.country || ''
    }
  },
  mounted() {
    this.initAreas();
    this.getAllcities();
    this.getCategory();
    this.getsubCategory();
    this.getCountries();
    this.getStates();
    this.getCities();
    this.addFiles();
    this.user_id();
    this.userdata = this.user_id();
    if(this.userdata.id != null){
      this.getBusinessprofilebyreseller();
    }else{
      this.getBusinessprofile();
    }
  },
  methods: {
    user_id() {
      return this.$store.state.userId;
    },
    sanitizeText(text) {
      // Replace HTML tags with an empty string
      return text.replace(this.htmlText, '');
    },
    addServs(){
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let data = {
        services:this.servs,
      };
      console.log(data,config);
      // axios.post('addservices',data,config)
      //     .then((resp)=>{
      //       console.log(resp.data);
      //     });

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
    getBusinessprofile() {
    axios.get('/api/business/profile')
      .then((resp) => {
        this.bprofiles      = resp.data.buser;
        this.country_id     = resp.data.buser.country_id;
        this.stateId        = resp.data.buser.state_id;
        this.catId          = resp.data.buser.cat_id;
        this.subids         = resp.data.buser.subcat_id;
        this.imagepath      = resp.data.buser.user_avatar;
        this.reseller       = resp.data.reseller_data;
        this.servs          = resp.data.buser.service_text;
        this.tabsarray      = resp.data.tabs;
        this.isFeatured     = resp.data.businessfeature_status === 1;

        // ─── ADD THESE TWO LINES ───────────────────────────────────────────────────
        if (this.bprofiles.area) {
  this.areas      = [ { text: this.bprofiles.area } ];
  this.areaSearch = this.bprofiles.area;
}


        // ─────────────────────────────────────────────────────────────────────────────

        // ✅ Set featured cities and categories
        if (resp.data.feature_cities) {
          this.selectedCities = resp.data.feature_cities.map(c => c.id);
        }
        // if (resp.data.feature_categories) {
        //   this.selectedCats   = resp.data.feature_categories.map(c => c.id);
        // }

        this.getCities();
        this.getsubCategory();

        for (let i = 0; i < resp.data.subcatids.length; i++) {
          this.subids.push(resp.data.subcatids[i].id);
        }
      })
      .then(() => this.$nextTick(() => {
        this.$refs.generalForm.validate();
        this.$refs.businessForm.validate();
        this.$refs.passwordForm.validate();
      }));
  },



    changeReseller(){
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let data = {
        old_reseller_id: this.reseller[0].username,
        new_reseller_id: this.new_reseller_id,
        reason_text: this.reason_text,
      };
      axios.post('/api/update/reseller/id',data,config)
          .then((resp) =>{
            if( resp.data.success == true ){
              this.snackbar = true;
              this.text = resp.data.message;
              this.showModel = false;
              this.timeout = 3000;
              this.getBusinessprofile();
            }else{
              this.snackbar = true;
              this.text = resp.data.message;
              this.showModel = false;
              this.timeout = 3000;
              this.getBusinessprofile();
            }

          });
      this.$refs.form.validate();
      this.$refs.form.reset();
    },
    changeResellerbyreseller(){
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let data = {
        userId:this.userdata.id,
        old_reseller_id: this.reseller[0].username,
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
    updateProfile1() {
    // 1) force a validation run on your general form
    this.$refs.generalForm.validate()

    // 2) if it's still invalid, bail out
    if (!this.validGeneral) return

    // 3) kick off your spinner
    this.loading = true;

    // 4) build your FormData exactly as you had

    const formData = new FormData();
formData.append('name', this.bprofiles.name);
formData.append('username', this.bprofiles.username);
formData.append('email', this.bprofiles.email);
formData.append('mobile_phone', this.bprofiles.mobile_phone);
if (this.files[0]) {
  formData.append('user_avatar', this.files[0]);
}

// ✅ Add featured toggle
formData.append('is_featured', this.isFeatured); // should be true/false

// ✅ Append city & category arrays
formData.append('feature_categories', JSON.stringify(this.selectedCats));
formData.append('feature_cities', JSON.stringify(this.selectedCities));

console.log("Payload being sent:", formData); // 🔍 Check this in browser console
axios.post('/api/update/business/profile', formData, {
  headers: { 'content-type': 'multipart/form-data' }
})
  .then((resp) => {
    this.loading = false;
    this.snackbar = true;
    this.text = resp.data.buser;
    this.timeout = 3000;
  })
  .catch((error) => {
    this.loading = false;
    this.snackbar = true;
    this.text = "Something went wrong.";
    this.timeout = 3000;
  })
    .finally(() => {
      // stop spinner
      this.loading = false
      // re‑validate so your button state updates
      this.$nextTick(() => {
        this.$refs.generalForm.validate()
      })
    })
  },
    
    updateProfileByreseller() {
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let data = {
        name: this.bprofiles.name,
        userId:this.userdata.id,
        email: this.bprofiles.email,
        username: this.bprofiles.username,
        mobile_phone: this.bprofiles.mobile_phone,
        user_avatar: this.files[0],
      };
      axios.post('/api/reseller/update/business/profile',data,config)
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
    },
    updateProfile2() {
      this.loading= true;
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }

      let services = this.servs;
      if(services == null){
        services = this.noservs;
      }
      else {
        services = this.servs;
      }
      let data = {
        cat_id: this.catId,
        subcat_id: this.subids,
        state_id: this.bprofiles.state_id,
        city_id: this.bprofiles.city_id,
        area: this.bprofiles.area,
        gst: this.bprofiles.gst,
        address: this.bprofiles.address,
        about: this.bprofiles.about,
        mobile_phone: this.bprofiles.mobile_phone,
        live_location: this.bprofiles.live_location,
        service_text: services,
        
      };

      const posData = {
        tabids : this.tabsarray.map( tab => tab.id),
        items : this.tabsarray.map( tab => tab.tab_content)
      };
      
      axios.post('/api/update/editor/array',posData)
            .then((resp) => {
              if( resp.data.success == true ){
                console.log('Editors data updated successfully!');
              }else{
                console.log('Something went wrong!');
              }
            });

      axios.post('/api/update/business/profile2',data, config)
            .then((resp) => {
              this.loading = false;
              this.snackbar = true;
              this.text = resp.data.buser;
              this.timeout = 3000;
              this.getBusinessprofile();
            })
            .catch(error => {
              // Handle the error
              var data = error.toJSON();
              if (data.status == 400) {
                this.loading = false;
                this.snackbar = true;
                this.text = "something went wrong.";
                this.timeout = 3000;
              }
            });


    },
    updateProfile2byreseller() {
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let services = this.servs;
      if(services == null){
        services = this.noservs;
      }
      else {
        services = this.servs;
      }
      let data = {
        userId:this.userdata.id,
        cat_id: this.catId,
        subcat_id: this.subids,
        state_id: this.bprofiles.state_id,
        city_id: this.bprofiles.city_id,
        gst: this.bprofiles.gst,
        address: this.bprofiles.address,
        about: this.bprofiles.about,
        mobile_phone: this.bprofiles.mobile_phone,
        live_location: this.bprofiles.live_location,
        service_text: services,
      };
      axios.post('/api/reseller/update/business/profile2',data, config)
            .then((resp) => {
              this.snackbar = true;
              this.text = resp.data.buser;
              this.timeout = 3000;
              this.getBusinessprofilebyreseller();
            })
            .catch(error => {
              // Handle the error
              var data = error.toJSON();
              if (data.status == 400) {
                this.snackbar = true;
                this.text = "something went wrong.";
                this.timeout = 3000;
              }
            });

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
    updatePasswordbyreseller() {
      axios.post('/api/reseller/update/business/password',{
        userId:this.userdata.id,
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
      axios.get("/api/get/states/"+country_id)
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
    getAllcities() {
axios.get(`/api/getallcities`)
  .then(response => {
    this.featureAllCities = response.data.cities;
  })
  .catch(error => {
    console.error("Error loading all cities:", error);
  });
},
    validateProfile() {
  this.$refs.profileForm.validate();
  this.isProfileValid = this.$refs.profileForm.valid;
},
validatePassword() {
  this.$refs.passwordForm.validate();
  this.isPasswordValid = this.$refs.passwordForm.valid;
},
validateService() {
  this.$refs.serviceForm.validate();
  this.isServiceValid = this.$refs.serviceForm.valid;
},
submitFeatureSettings() {
const payload = {
  user_id: this.userdata.id,
  businessfeature_status: this.isFeatured ? 1 : 0,
  cities: this.selectedFeatureCities,
  categories: this.selectedFeatureCategories
};

axios.post('/api/business/feature-toggle', payload)
  .then(res => {
    this.snackbar = true;
    this.text = res.data.message;
    this.timeout = 3000;
  });
},

initAreas() {
      // build the default “City, State, Country” list
      this.fetchDefaultAreas();
    },

    fetchDefaultAreas() {
      // no city? clear
      if (!this.bprofiles.city_id) {
        this.areas = [];
        return;
      }

      // look up the three names
      const q = [ this.cityName, this.stateName, this.countryName ]
        .filter(Boolean)
        .join(', ');

      this.loadingAreas = true;
      axios.get('https://nominatim.openstreetmap.org/search', {
        params: { q, format: 'json', limit: 5 }
      })
      .then(({ data }) => {
        this.areas = data.map(p => ({
          display_name: p.display_name
        }));
      })
      .finally(() => {
        this.loadingAreas = false;
      });
    },

    async onAreaSearch(val) {
      this.areaSearch = val;

      // if they clear or type < 3 chars, fall back to default
      if (!val || val.length < 3) {
        return this.fetchDefaultAreas();
      }

      // prefix with “, City, State, Country”
      const q = [
        val.trim(),
        this.cityName,
        this.stateName,
        this.countryName
      ].filter(Boolean)
       .join(', ');

      this.loadingAreas = true;
      try {
        const { data } = await axios.get(
          'https://nominatim.openstreetmap.org/search',
          { params: { q, format: 'json', limit: 50 } }
        );
        this.areas = data.map(p => ({
          display_name: p.display_name
        }));
      } finally {
        this.loadingAreas = false;
      }
    },


  

  }
}
</script>
<style scoped>
.servlist{
  column-count: 2;
}
@media(max-width: 576px){
  .servlist{
    column-count: 1;
  }
}
.servlist > div{
  margin-bottom: 10px;
}
</style>