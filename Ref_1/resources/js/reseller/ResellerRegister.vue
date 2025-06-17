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
    
          <!-- FORM COLUMN -->
          <v-col cols="12" lg="5" order="2" order-lg="2">
            <v-form ref="form" v-model="valid" lazy-validation>
              <v-card class="elevation-18">
                <v-toolbar flat color="red">
                  <v-toolbar-title class="mx-auto text-white">
                    Register As Service Agent
                  </v-toolbar-title>
                </v-toolbar>
                <v-card-text>
                  <v-text-field type="number" v-model="businessform.user_role" class="d-none"></v-text-field>
                  <v-text-field type="number" v-model="businessform.user_status" class="d-none"></v-text-field>
                  <v-row>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    clearable
                                    v-model="businessform.name"
                                    :rules="nameRules"
                                    :counter="50"
                                    label="Your Name"
                                    type="text" dense
                                    prepend-inner-icon="mdi-account">
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-select
                                    clearable
                                    v-model="businessform.gender"
                                    :rules="genderRules"
                                    label="Select Gender"
                                    :items="genders"
                                    item-text="gender"
                                    item-value="gender" dense
                                    prepend-inner-icon="mdi-human">
                                </v-select>
                            </v-col>
                        </v-row>
                        <v-row align-content="center">
                            <v-col cols="12">
                                <v-text-field
                                    clearable
                                    v-model="businessform.email"
                                    :rules="emailRules"
                                    @keyup="emailVerify"
                                    :error-messages="emailVerifyMessage"
                                    label="Your Email"
                                    type="email" dense
                                    prepend-inner-icon="mdi-email">
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field class=""
                                              clearable
                                              v-model="businessform.password"
                                              label="Enter Password"
                                              :rules="passwordRules"
                                              :append-icon="showPassword ? 'mdi-eye-outline' : 'mdi-eye-off-outline'"
                                              :type="showPassword ? 'text' : 'password'"
                                              @click:append="showPassword = !showPassword"
                                              :counter="15" dense
                                              prepend-inner-icon="mdi-lock">
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    clearable
                                    v-model="businessform.confirm_password"
                                    label="Confirm Password"
                                    :rules="[(businessform.password === businessform.confirm_password) || 'Confirm password does not match']"
                                    :counter="15" dense
                                    :append-icon="showPassword2 ? 'mdi-eye-outline' : 'mdi-eye-off-outline'"
                                    :type="showPassword2 ? 'text' : 'password'"
                                    @click:append="showPassword2 = !showPassword2"
                                    prepend-inner-icon="mdi-lock">
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-autocomplete
                                    clearable
                                    v-model="businessform.profession_id"
                                    :rules="professionRules"
                                    label="Select Profession"
                                    :items="allProfessions"
                                    item-text="profession"
                                    item-value="id" dense
                                    prepend-inner-icon="mdi-tray-arrow-down">
                                </v-autocomplete>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-autocomplete
                                    clearable
                                    v-model="businessform.skill_id"
                                    :rules="skillRules"
                                    label="Skills & Profession"
                                    :items="allSkills"
                                    item-text="skill"
                                    item-value="id"
                                    multiple dense
                                    prepend-inner-icon="mdi-code-tags">
                                </v-autocomplete>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    clearable
                                    v-model="businessform.mobile_phone"
                                    :rules="mobileRules"
                                    @keyup="phoneVerify"
                                    :error-messages="phoneVerifyMessage"
                                    label="Mobile No."
                                    type="number"
                                    :counter="10" dense
                                    prepend-inner-icon="mdi-cellphone">
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    clearable
                                    v-model="businessform.date_of_birth"
                                    :rules="dateRules"
                                    type="date"
                                    label="Enter DOB" dense
                                    prepend-inner-icon="mdi-calendar">
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-autocomplete
                                    clearable
                                    v-model="businessform.country_id"
                                    @change="getStatesOnChange"
                                    :items="countries"
                                    :rules="countryRules"
                                    label="Select Country"
                                    item-text="country"
                                    item-value="id" dense
                                    prepend-inner-icon="mdi-home-city">
                                </v-autocomplete>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-autocomplete
                                    clearable
                                    v-model="businessform.state_id"
                                    @change="getCitiesOnChange"
                                    :items="allstates"
                                    :rules="stateRules"
                                    label="Select State"
                                    item-text="state"
                                    item-value="id" dense
                                    prepend-inner-icon="mdi-home-city">
                                </v-autocomplete>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-autocomplete
                                    clearable
                                    v-model="businessform.city_id"
                                    :rules="cityRules"
                                    :items="cities"
                                    label="Select City"
                                    item-text="city"
                                    item-value="id" dense
                                    prepend-inner-icon="mdi-city" >
                                </v-autocomplete>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-textarea class=""
                                            clearable
                                            v-model="businessform.address"
                                            :rules="addressRules"
                                            :counter="100"
                                            label="Enter Address"
                                            rows="2" dense
                                            prepend-inner-icon="mdi-map">
                                </v-textarea>
                            </v-col>
                        </v-row>
                        <v-row class="mt-0">
                            <v-col cols="12" md="12">
                                <v-checkbox type="checkbox" value="1" class="my-0 py-0"
                                            v-model="businessform.agree_policy"
                                            :rules="agree_policyRules">
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
                        <v-btn :disabled="loading" @click="ResellerformSubmit" color="red" large block class="text-white">Create</v-btn>
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
                    <v-btn color="blue" text v-bind="attrs" @click="snackbar = false">Close</v-btn>
                </template>
            </v-snackbar>
          </v-col>
    </v-row>
</v-container>
    </div>
</template>

<script>
export default {
    name: "ResellerRegister",
    data() {
        return{
            loading: false,
            businessform:{
                name: '',
                gender: '',
                email:'',
                password: '',
                confirm_password:'',
                profession_id: '',
                skill_id: '',
                mobile_phone: '',
                date_of_birth: '',
                country_id: '',
                state_id: '',
                city_id: '',
                address: '',
                agree_policy: '',
                user_role: 2,
                user_status: 1,
            },
            countries: [],
            allstates:[],
            cities:[],
            getimage:[],
            url:'https://bizlx.s3.ap-south-1.amazonaws.com',
            allProfessions:[],
            allSkills:[],
            emailVerifyMessage:'',
            phoneVerifyMessage:'',
            valid: false,
            snackbar: false,
            text: '',
            showPassword: false,
            showPassword2: false,
            timeout: '',
            url: "https://bizlx.s3.ap-south-1.amazonaws.com", // Define CDN URL
           
            genders:[{id:1,gender:'Male'},{id:2,gender:'Female'}],
            nameRules: [
                v => !!v || 'Name is required.',
                v => v.length <= 50 || 'Name must be less than 10 characters.',
            ],
            genderRules: [
                v => !!v || 'Gender is required.',
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

            professionRules: [
                v => !!v || 'Profession is required.',
            ],
            skillRules: [
                v => !!v || 'Skill is required.',
            ],

            mobileRules: [
                v => !!v || 'Mobile number is required.',
                v => v.length == 10 || 'Mobile number must be 10 characters.',
            ],
            dateRules: [
                v => !!v || 'DOB is required.',
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
    mounted() {
        this.getbusinessimage();
        this.All_Countries();
        this.All_Professions();
        this.All_skills();
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
    methods: {
        // getbusinessimage(){
        //     axios.get('/api/profile/details')
        //         .then((resp)=>{
        //             this.getimage = resp.data.profile_details;
        //         });
        // },

        getbusinessimage() {
    axios.get('/api/settings')
        .then((resp) => {
            if (resp.data && resp.data.reseller_reg_img) {
                this.getimage = [{
                    path: `${this.url}${resp.data.reseller_reg_img}` // Prepend CDN URL
                }];
            } else {
                console.error("Image path is missing from API response");
            }
        })
        .catch(error => {
            console.error("Error fetching reseller image:", error);
        });
},

        All_Professions(){
            axios.get('/api/professions')
                .then((resp)=>{
                    this.allProfessions = resp.data.professions;
                });
        },
        All_skills(){
            axios.get('/api/skills')
                .then((resp)=>{
                    this.allSkills = resp.data.skills;
                });
        },
        All_Countries(){
            axios.get('/api/countries')
                .then((resp)=>{
                    this.countries = resp.data.countries;
                });
        },
        getStatesOnChange() {
            var country_id = this.businessform.country_id;
            axios.get("/api/get/states/"+country_id)
                .then(response => {
                    this.allstates = response.data;
                })
        },
        getCitiesOnChange() {
            var state_id = this.businessform.state_id;
            axios.get(`/api/states/${state_id}`)
                .then(response => {
                    this.cities = response.data;
                })
        },
        emailVerify(){ // verify email
            if(/.+@.+/.test(this.businessform.email)){
                axios.post('/api/user/verify', {'email':this.businessform.email})
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
            if(this.businessform.mobile_phone.length == 10){
                axios.post('/api/user/verify', {'mobile_phone':this.businessform.mobile_phone})
                    .then((resp) => {
                        if(resp.data.status==400){
                            this.phoneVerifyMessage = resp.data.message;
                        }else{
                            this.phoneVerifyMessage = "";
                        }
                    });
            }
        },

        async ResellerformSubmit() {
            this.loading = true;
            const config = {
                headers: { 'content-type': 'multipart/form-data' }
            }
            await axios.post('/api/reseller/register', this.businessform, config)
                .then((resp) => {
                    this.loading=false;
                    this.snackbar = true;
                    this.text = resp.data.message;
                    this.timeout = 3000;
                    window.location.href = '/reseller/login';
                }).catch(error => {
                    // var data = {...error.response};
                    var data = error.toJSON();
                    if(data.status == 400){
                        this.loading=false;
                        this.snackbar = true;
                        this.text = "something went wrong.";
                        this.timeout = 3000;
                    }
                });
            this.$refs.form.validate();
        },

    }
}
</script>

<style scoped>

</style>
