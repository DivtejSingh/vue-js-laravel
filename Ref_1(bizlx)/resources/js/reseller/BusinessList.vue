<template>
<div class="pa-2">
  <div class="row py-2">
          <div class="col-12 col-md-2">
            <h4 class="ms-4">Businesses</h4>
          </div>
          <div class="col-12 col-md-7 py-1">
            <v-text-field clearable v-model="search" append-icon="mdi-magnify" label="Search" single-line hide-details>
            </v-text-field>
          </div>
          <div class="col-12 col-md-3 py-1">
              <v-btn tile color="error" large @click="rbaDialog = true">
                <v-icon left>
                  mdi-account-plus
                </v-icon>
                ADD BUSINESS CLIENT
              </v-btn>
          </div>
        </div>
  <v-tabs v-model="tab">
    <v-tab cols="6">Business List</v-tab>
  </v-tabs>
  <v-tabs-items v-model="tab">
          <v-tab-item>
            <v-card color="basil" flat >
              <v-form>
                <v-row>
                  <v-col class="d-flex" cols="12" md="4">
                    <v-autocomplete ref="cityRef" clearable :items="cities" item-text="city" item-value="id" label="City" v-model="city" @change="getSelectedData(); blurField('cityRef')" dense></v-autocomplete>
                  </v-col>
                  <v-col class="d-flex" cols="12" md="4">
                    <v-autocomplete ref="stateRef" clearable :items="states" item-text="state" item-value="id" v-model="state" label="State" @change="getSelectedData(); blurField('stateRef')" dense></v-autocomplete>
                  </v-col>
                  <v-col class="d-flex" cols="12" md="4">
                    <v-autocomplete ref="statusRef" clearable :items="Status" item-text="name" item-value="id" v-model="statusValue" label="Active/De-active" @change="getSelectedData(); blurField('statusRef')" dense></v-autocomplete>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col class="d-flex" cols="12" md="4">
                    <v-autocomplete clearable ref="planRef" :items="plans" item-text="plan_description" v-model="plan" item-value="id" label="Plan" @change="getSelectedData(); blurField('planRef')" dense></v-autocomplete>
                  </v-col>

                  <v-col class="d-flex" cols="12" md="4">
                    <v-autocomplete clearable ref="subRef" :items="subcategories" item-text="subcat_name" v-model="subcategory" item-value="id" @change="getSelectedData(); blurField('subRef')" label="Sub Category" dense></v-autocomplete>
                  </v-col>

                  <v-col cols="12" md="4">
                    <v-menu v-model="menu2" :close-on-content-click="false" :nudge-right="40"
                            transition="scale-transition" offset-y min-width="auto">
                      <template v-slot:activator="{ on, attrs }">
                        <v-text-field clearable dense v-model="date" label="Expire (with in week)" prepend-icon="mdi-calendar"
                                      v-bind="attrs" v-on="on"></v-text-field>
                      </template>
                      <v-date-picker v-model="date" @change="getSelectedData()" @input="menu2 = false"  ></v-date-picker>
                    </v-menu>
                  </v-col>
                </v-row>
                <div class="">
                  <v-data-table :headers="headers" :search="search" :items="businesslists"
                                hide-header class="elevation-1">
                    <template v-slot:top>
                      <v-divider class="mx-4" inset vertical></v-divider>
                      <v-spacer></v-spacer>
                      <v-dialog v-model="dialogDelete" max-width="500px">
                        <v-card>
                          <v-card-title class="text-h5">Are you sure you want to delete this
                            item?</v-card-title>
                          <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
                            <v-btn color="blue darken-1" text @click="deleteItemConfirm">OK</v-btn>
                            <v-spacer></v-spacer>
                          </v-card-actions>
                        </v-card>
                      </v-dialog>
                    </template>
                      <template v-slot:[`item.action`]="{item}">
                          <!-- <a :href= "'update/business-list/'+item.business_id"> -->
                            <a :href="'/reseller/business-list/edit/' + item.business_id">
                               
                            <v-icon small class="mr-2" title="Edit Business">
                              mdi-pencil
                            </v-icon>
                          </a>
                        <v-icon small class="mr-2" title="Login As Business" @click="resellerbusinessLogin(item.email)"> mdi-login </v-icon>
                      </template>
                    <template v-slot:[`item.active`]="{ item }">
                      <div v-if="item.user_status == '1'">
                        Active
                      </div>
                      <div v-else>
                        Inactiveus
                      </div>
                    </template>
                  </v-data-table>
                </div>
              </v-form>
            </v-card>
          </v-tab-item>
        </v-tabs-items>

    <!-- Modal Dialog -->
    <v-dialog v-model="rbaDialog" max-width="600">
      <v-card>
        <v-card-title>
          Create Business Client
        </v-card-title>

        <!-- Business Client Form -->
        <v-card-text>

          <v-card flat>
            <v-form ref="form" v-model="ravalid">
              <v-row>
                <v-col class="py-2" cols="12" md="12">
                  <v-text-field
                    clearable
                    v-model="formData.business_name"
                    :rules="nameRules"
                    :counter="100"
                    dense
                    label="Store / Business Name"
                    type="text"
                  ></v-text-field>
                </v-col>
                <v-col class="py-2" cols="12" md="12">
                  <v-text-field
                  clearable
                    v-model="formData.email"
                    :rules="emailRules"
                    @keyup="emailVerify"
                    :error-messages="emailVerifyMessage"
                    label="Your Email"
                    dense
                    type="email"
                  ></v-text-field>
                </v-col>
                <v-col class="py-2" cols="12" md="12">
                  <v-text-field
                  clearable
                    v-model="formData.username"
                    :rules="usernameRules"
                    @keyup="userNameVerify"
                    :error-messages="userNameVerifyMessage"
                    :counter="80"
                    dense
                    label="Username"
                    type="text"
                  ></v-text-field>
                </v-col>
                <v-col class="py-2" cols="12" md="12">
                  <v-text-field clearable label="GST" dense v-model="formData.GST"></v-text-field>
                </v-col>
                <v-col class="py-2" cols="12" md="6">
                  <v-text-field
                  clearable
                  :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"
                    v-model="formData.password"
                    :rules="passwordRules"
                    :counter="15"
                    dense
                    label="Enter Password"
                      :type="show1 ? 'text' : 'password'" name="input-10-1"
                    @click:append="show1 = !show1"

                  ></v-text-field>
                </v-col>
                <v-col class="py-2" cols="12" md="6">
                  <v-text-field
                  clearable
                  :append-icon="show2 ? 'mdi-eye' : 'mdi-eye-off'"
                    v-model="formData.confirm_password"
                    :rules="[
                      (formData.password === formData.confirm_password) ||
                      'Confirm password does not match'
                    ]"
                    :counter="15"
                    dense
                    label="Confirm Password"
                      :type="show2 ? 'text' : 'password'" name="input-10-1" 
                    @click:append="show2 = !show2"

                  ></v-text-field>
                </v-col>
                <v-col class="d-flex" cols="12" md="6">
                  <v-autocomplete
                  clearable
                    v-model="formData.category"
                    @change="AllCats"
                    :rules="maincatRuless"
                    label="Select Main Category"
                    :items="allmaincategories"
                    item-text="cat_name"
                    item-value="id"
                    dense
                    prepend-inner-icon="mdi-tray-arrow-down"
                  ></v-autocomplete>
                </v-col>
                 <v-col class="d-flex" cols="12" md="6">                 
                  <v-autocomplete
                  clearable
                    v-model="formData.subcategories"
                    :rules="catRuless"
                    label="Select Sub Category"
                    :items="allcategories"
                    item-text="subcat_name"
                    item-value="id"
                    dense
                    :no-data-text="customNoDataText1"
                    prepend-inner-icon="mdi-tray-arrow-down"
                  ></v-autocomplete>
                </v-col>
                <v-col class="d-flex" cols="12" md="6">
                  <v-autocomplete
                  clearable
                    v-model="formData.country_id"
                    @change="getStatesOnChange"
                    :items="countries"
                    :rules="countryRules"
                    label="Select Country"
                    item-text="country"
                    item-value="id"
                    dense
                    prepend-inner-icon="mdi-home-city"
                  ></v-autocomplete>
                </v-col>
                <v-col class="d-flex" cols="12" md="6">
                  <v-autocomplete
                  clearable
                    v-model="formData.state"
                    @change="getCitiesOnChange"
                    :items="allstates"
                    :rules="stateRules"
                    label="Select State"
                    item-text="state"
                    item-value="id"
                    dense
                    prepend-inner-icon="mdi-home-city"
                  ></v-autocomplete>
                </v-col>
                <v-col class="py-2" cols="12" md="6">
                  <v-autocomplete
                  clearable
                    v-model="formData.city"
                    :rules="cityRules"
                    :items="cities"
                    label="Select City"
                    item-text="city"
                    item-value="id"
                    dense
                    prepend-inner-icon="mdi-city"
                  ></v-autocomplete>
                </v-col>
                <v-col class="py-2"  cols="12" md="6">
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
                <v-col class="py-2" cols="12" md="6">
                  <v-text-field
                  clearable
                    v-model="formData.mobile"
                    :rules="mobileRules"
                    @keyup="phoneVerify"
                    dense
                    :error-messages="phoneVerifyMessage"
                    :counter="10"
                    label="Enter Mobile"
                    type="number"
                  ></v-text-field>
                </v-col>
                <v-col class="py-2" cols="12" md="12">
                  <v-textarea
                  clearable
                    rows="3"
                    v-model="formData.address"
                    dense
                    row-height="20"
                    label="Business Address"
                  ></v-textarea>
                </v-col>
                <v-col class="py-2 ms-2" cols="12" md="2">
                  <v-checkbox v-model="formData.user_status" label="Active"></v-checkbox>
                </v-col>
              </v-row>
              <div class="text-end">
                <v-btn
                  :disabled="!ravalid"
                  @click.prevent="BusinessformSubmit"
                  color="error"
                  large
                >SAVE</v-btn>
              </div>
            </v-form>
            <v-snackbar v-model="snackbar" :timeout="timeout">{{ text }}
              <template v-slot:action="{ attrs }">
                <v-btn color="blue" text v-bind="attrs" @click="snackbar = false"
                  >Close</v-btn
                >
              </template>
            </v-snackbar>
          </v-card>
      
        </v-card-text>
      </v-card>
    </v-dialog>
  



</div>
</template>
<script>
import axios from "axios";

export default {
  name: 'BusinessList',
  data() {
    return {
      show1: false,
      show2: false,

      // Data from the first script
      areaSearch: '',
            areas: [],
            loadingAreas: false,
      formData: {
        business_name: '',
        email: '',
        username: '',
        GST: '',
        password: '',
        confirm_password: '',
        category: null,
        area: '',
        subcategories: [],
        country_id: null,
        state: null,
        city: null,
        mobile: '',
        address: '',
        user_status: true,
      },
      snackbar: false,
      text: '',
      timeout: 3000,
      allmaincategories: [],
      allcategories: [],
      countries: [],
      allstates: [],
      cities: [],
      tab: '',
      userNameVerifyMessage:'',
      emailVerifyMessage:'',
      phoneVerifyMessage:'',
      customNoDataText: 'Please Select State First',
      customNoDataText1: 'Please Select Main Category First',
      rbaDialog: false, // Control for the dialog
      tab: null,
      ravalid: false,
      valid: false,
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
        // v => !!v || 'Password is required.',
        // v => /[A-Z]/.test(v) || 'Use upper case.',
        // v => /[a-z]/.test(v) || 'Use lower case.',
        // v => /[0-9]/.test(v) || 'Use number.',
        // v => /[#?!@$%^&*-]/.test(v) || 'Use special character.',
        // v => v.length >= 8 || 'Password must be greater than 8 characters.',
        v => v.length <= 15 || 'Password must be less than 15 characters.',
      ],
      addressRules: [
        v => !!v || 'Address is required.',
        v => v.length <= 100 || 'Address must be less than 100 characters.',
      ],
      agree_policyRules: [
        v => !!v || 'This field is required.',
      ],

      // Data from the second script
      search: '',
      state: '',
      statusValue: '',
      plan: '',
      subcategory: '',
      date: '',
      Status: [
        { 'id': 1, 'name': 'Active' },
        { 'id': 0, 'name': 'Inactive' },
      ],
      menu: false,
      menu2: false,
      modal: false,
      dialog: false,
      dialogDelete: false,
      headers: [
        { text: "Name", align: "start", sortable: false, value: "name", width: 100 },
        { text: "E-mail", value: "email", width: 100 },
        { text: "Mobile Number", value: "mobile_phone", width: 150 },
        { text: "Plan", value: "plan_description", width: 150 },
        { text: "Reseller", value: "reseller_name", width: 100 },
        { text: "Expires", value: "expires", width: 100 },
        { text: "City", value: "city", width: 100 },
        { text: "Sub Category", value: "subcatsname", width: 150 },
        { text: "Listing Date", value: "created_date", width: 150 },
        { text: "Active", value: "active", sortable: false },
        { text: "Actions", value: "action", sortable: false, width: 100 },
      ],
      businesslists: [],
      searchparams: [],
    };
  },
  watch: {
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
  },
  mounted() {
    this.getBusiness();
    this.getSearchData();
    this.AllCats();
    this.All_Countries();
    this.allmaincats();
  },
  methods: {
    blurField(refName) {
  this.$nextTick(() => {
    const refEl = this.$refs[refName];
    if (refEl?.blur) refEl.blur();
    else if (refEl?.$el?.querySelector?.('input')) {
      refEl.$el.querySelector('input').blur();
    }
  });
},
    BusinessformSubmit() {
      if (typeof this.formData.area === 'object' && this.formData.area !== null) {
    this.formData.area = this.formData.area.display_name || '';
  }
        const fdata = {
          business_name: this.formData.business_name,
          email: this.formData.email,
          username: this.formData.username,
          GST: this.formData.GST,
          password: this.formData.confirm_password,
          confirm_password: this.formData.confirm_password,
          category: this.formData.category,
          subcategories: [this.formData.subcategories], 
          country_id: this.formData.country_id,
          state: this.formData.state,
          city: this.formData.city,
          mobile: this.formData.mobile,
          area:this.formData.area,
          address: this.formData.address,
          user_status: this.formData.user_status,
          
        };


      const config = {
            headers: {
                'Content-Type': 'application/json',
    
            }
        };

        axios.post('/api/register/reseller', fdata, config)
        .then((resp)=>{
          this.snackbar = true;
          this.text = resp.data.message;
          this.rbaDialog = false;
          this.getBusiness();
          this.$refs.form.reset();
        })
        .catch((error)=>{
          this.snackbar = true;
          this.text = "Something went wrong. Please try again.";  // Error message
        })

},

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
          this.fetchDefaultAreas();
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
      if(this.formData.mobile.length == 10){
        axios.post('/api/user/verify', {'mobile_phone':this.formData.mobile})
            .then((resp) => {
              if(resp.data.status==400){
                this.phoneVerifyMessage = resp.data.message;
              }else{
                this.phoneVerifyMessage = "";
              }
            });
      }
    },

    // Methods from the second script
    getBusiness() {
      axios.get('/api/registered/business/list')
        .then((resp) => {
          this.businesslists = resp.data.businesses;
          const uniqueItems = Array.from(new Set(this.businesslists.map(item => item.business_id)))
            .map(id => this.businesslists.find(item => item.business_id === id));
          this.businesslists = uniqueItems;
        });
    },
    async resellerbusinessLogin(email) {
      var data = { email: email };
      await axios.post('/api/reseller/business/login', data)
        .then((resp) => {
          const userId = resp.data.user;
          this.$store.dispatch('setuserid', { userId });
          window.location.href = '/reseller/business/profile';
        })
        .catch((err) => {
          this.error = err.response.data.message;
        });
    },
    getSearchData() {
      axios.get('/api/get/information')
        .then((resp) => {
          this.cities = resp.data.cities;
          this.states = resp.data.state;
          this.plans = resp.data.plan;
          this.subcategories = resp.data.subcategories;
        });
    },
    getSelectedData() {
      var params = {
        city: this.city,
        state: this.state,
        statusValue: this.statusValue,
        plan: this.plan,
        subcategory: this.subcategory,
        date: this.date,
      };
      axios.post('/api/search/business', params)
        .then((resp) => {
          this.businesslists = resp.data.businesses;
          const uniqueItems = Array.from(new Set(this.businesslists.map(item => item.business_id)))
            .map(id => this.businesslists.find(item => item.business_id === id));
          this.businesslists = uniqueItems;
        });
    },
    editItem(item) {
      this.editedIndex = this.businesslists.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },
    deleteItem(item) {
      this.editedIndex = this.businesslists.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },
    deleteItemConfirm() {
      this.businesslists.splice(this.editedIndex, 1);
      this.closeDelete();
    },
    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },
    onAreaSearch(val) {
  if (val.length >= 3) {
    this.fetchAreas(val);
  }
},
fetchAreas(query) {
  this.loadingAreas = true;
  const cityName = this.cities.find(c => c.id === this.formData.city)?.city;

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
  const cityName = this.cities.find(c => c.id === this.formData.city)?.city;

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
  },
};
</script>
