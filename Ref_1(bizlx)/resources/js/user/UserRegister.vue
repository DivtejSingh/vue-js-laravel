<template>
    <v-layout align-center justify-center>
        <v-responsive class="mx-auto col-12 col-md-6">
            <v-card class="my-5 border rounded-3">
                <v-form @submit.prevent="RegisterformSubmit" ref="form" v-model="valid" lazy-validation>
                    <v-card-title class="bgred text-white rounded-top">Register</v-card-title>
                    <v-card-text>
                        <v-row>

                            <v-text-field type="number" v-model="form1.user_role" class="d-none"></v-text-field>
                            <v-text-field type="number" v-model="form1.user_status" class="d-none"></v-text-field>

                            <v-text-field clearable type="text" placeholder="Name" v-model="form1.name"
                                          :rules="nameRules"
                                          :counter="50"
                                          prepend-inner-icon="mdi-account"  class="col-md-6">
                            </v-text-field>
                            <!-- <v-text-field type="text" placeholder="Username" v-model="form1.username"
                                          :rules="usernameRules"
                                          :counter="50"
                                          @keyup="userNameVerify"
                                          :error-messages="userNameVerifyMessage"
                                          prepend-inner-icon="mdi-rename" class="col-md-6" ></v-text-field> -->
                        </v-row>
                        <v-row>
                            <v-text-field clearable type="email" placeholder="Email Address" v-model="form1.email"
                                          :rules="emailRules"
                                          @keyup="emailVerify"
                                          :error-messages="emailVerifyMessage"
                                          prepend-inner-icon="mdi-email" class="col-md-6">
                            </v-text-field>
                            <v-text-field
                                clearable
                                :append-icon="showPassword ? 'mdi-eye-outline' : 'mdi-eye-off-outline'"
                                :type="showPassword ? 'text' : 'password'"
                                @click:append="showPassword = !showPassword"
                                placeholder="Password" v-model="form1.password"
                                :counter="15"
                                :rules="passwordRules"
                                prepend-inner-icon="mdi-lock" class="col-md-6" ></v-text-field>
                        </v-row>
                        <v-row>
                            <v-autocomplete class="col-md-6"
                                            clearable
                                            v-model="form1.country_id"
                                            @change="getStatesOnChange"
                                            :items="countries"
                                            :rules="countryRules"
                                            label="Country"
                                            item-text="country"
                                            item-value="id" dense
                                            prepend-inner-icon="mdi-home-city">
                            </v-autocomplete>

                            <v-autocomplete class="col-md-6"
                                            clearable
                                            v-model="form1.state_id"
                                            @change="getCitiesOnChange"
                                            :items="allstates"
                                            :rules="stateRules"
                                            label="State"
                                            item-text="state"
                                            item-value="id" dense
                                            prepend-inner-icon="mdi-home-city">
                            </v-autocomplete>

                            <v-autocomplete class="col-md-6"
                                            clearable
                                            v-model="form1.city_id"
                                            :rules="cityRules"
                                            :items="cities"
                                            label="City"
                                            item-text="city"
                                            item-value="id" dense
                                            prepend-inner-icon="mdi-city" >
                            </v-autocomplete>

                            <!-- <v-autocomplete class="col-md-6" placeholder="City" :items="allCities" item-text="city" v-model="form1.city_id"
                                :rules="cityRules"
                                item-value="id" prepend-inner-icon="mdi-city" item-state_id="state_id">
                                <template v-slot:item="{item}">
                                    <span v-text="item.city"></span>&nbsp;,&nbsp;<span v-text="item.state" v-value="state_id"></span>
                                </template>
                            </v-autocomplete> -->

                            <v-text-field type="number" placeholder="Phone" v-model="form1.mobile_phone"
                                            clearable
                                          :rules="phoneRules"
                                          :counter="10"
                                          @keyup="phoneVerify"
                                          :error-messages="phoneVerifyMessage" dense
                                          prepend-inner-icon="mdi-cellphone" class="col-md-6" ></v-text-field>
                        </v-row>
                        <div class="mt-3 justify-content-between d-flex align-center">
                            <v-btn type="submit" color="primary" :disabled="loading">Register</v-btn>
                            <span>Already have an account?
                        <a href="/user/login" class="text-decoration-none">Log In</a>
                  </span>
                        </div>
                    </v-card-text>
                </v-form>
            </v-card>
            <!-- Add the v-snackbar here -->
            <v-snackbar v-model="snackbar" :timeout="timeout">
                {{ text }}
                <template v-slot:action="{ attrs }">
                    <v-btn color="blue" text v-bind="attrs" @click="snackbar = false">Close</v-btn>
                </template>
            </v-snackbar>
        </v-responsive>
    </v-layout>
</template>

<script>
export default {
    name: "UserRegister", 
    name: "UserLogin", 
    data() {
        return{
            loading: false,
            form1:{
                user_role: 0,
                user_status: 1,
                name: '',
                // username: '',
                email : '',
                password: '',
                country_id: '',
                state_id: '',
                city_id: '',
                mobile_phone: '',
            },
            valid: false,
            allCities:'',
            cities:[],
            userNameVerifyMessage:'',
            emailVerifyMessage:'',
            phoneVerifyMessage:'',
            snackbar: false,
            showPassword: false,
            text: '',
            timeout: '3000',
            allstates:[],
            nameRules: [
                v => !!v || 'Name is required.',
                v => v.length <= 50 || 'Name must be less than 50 characters.',
            ],
            countries:[],
            usernameRules: [
                v => !!v || 'Username is required.',
                // v => v.length >= 8 || 'Username must be greater than 8 characters.',
                // v => v.length <= 50 || 'Username must be less than 50 characters.',
                v => (v.split(' ').length <= 1) || 'space not allowed',
                v => /^[\w\s]+$/.test(v) || 'Username may only contain letters, numbers and underscores'
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
            countryRules: [
                v => !!v || 'Country name is required.',
            ],
            stateRules: [
                v => !!v || 'State name is required.',
            ],
            cityRules: [
                v => !!v || 'City is required.',
            ],
            phoneRules: [
                v => !!v || 'Phone number is required.',
                v => v.length == 10 || 'Phone number must be 10 characters.',
            ],
            loggedIn:false,
            user_role:'',
        }
    },
    created(){
        this.All_Countries();
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
    methods:{
        All_Countries(){
            axios.get('/api/countries')
                .then((resp)=>{
                    this.countries = resp.data.countries;
                });
        },
        getStatesOnChange() {
            var country_id = this.form1.country_id;
            axios.get("/api/get/states/"+country_id)
                .then(response => {
                    this.allstates = response.data;
                })
        },
        getCitiesOnChange() {
            var state = this.form1.state_id;
            axios.get(`/api/states/${state}`)
                .then(response => {
                    this.cities = response.data;
                })
        },
        userNameVerify(){ // verify username
            if(this.form1.username.length >= 8){
                axios.post('/api/user/verify', {'username':this.form1.username})
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
            if(/.+@.+/.test(this.form1.email)){
                axios.post('/api/user/verify', {'email':this.form1.email})
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
            if(this.form1.mobile_phone.length == 10){
                axios.post('/api/user/verify', {'mobile_phone':this.form1.mobile_phone})
                    .then((resp) => {
                        if(resp.data.status==400){
                            this.phoneVerifyMessage = resp.data.message;
                        }else{
                            this.phoneVerifyMessage = "";
                        }
                    });
            }
        },

        // async RegisterformSubmit() { // Register
        //     const config = {
        //         headers: { 'content-type': 'multipart/form-data' }
        //     }
        //     await axios.post('/api/user/register', this.form1, config)
        //         .then((resp) => {
        //             this.snackbar = true;
        //             this.text = resp.data.message;
        //             this.$router.replace({name: 'user/login'});
        //         }).catch(error => {
        //             // var data = {...error.response};
        //             var data = error.toJSON();
        //             if(data.status == 400){
        //                 this.snackbar = true;
        //                 this.text = "something went wrong.";
        //                 this.timeout = 3000;
        //             }
        //         });
        //     this.$refs.form.validate();
        // },
//         async RegisterformSubmit() {
//             this.loading= true;
//     const config = {
//         headers: { 'content-type': 'multipart/form-data' }
//     };

//     try {
//         // Send registration request
//         const response = await axios.post('/api/user/register', this.form1, config);
//         console.log("API response", response);

//         // Show success message
//         this.loading= false;
//         this.snackbar = true;
//         this.text = response.data.message || "Registration successful!";
//         this.timeout = 3000; // Set timeout for snackbar

//         // Redirect to login page after snackbar timeout
//         setTimeout(() => {
//             // Automatically click on the login link
//             document.querySelector('a[href="/user/login"]').click();
//         }, 3000);
//     } catch (error) {
//         // Handle registration error
//         this.snackbar = true;
//         this.loading= false;
        
//         if (error.response) {
//             // Axios error with response data
//             if (error.response.status === 400) {
//                 this.text = "Something went wrong.";
//             } else {
//                 this.text = error.response.data.message || "An error occurred.";
//             }
//         } else if (error.request) {
//             // No response received from the server
//             this.text = "No response received from the server.";
//         } else {
//             // Some other error occurred
//             this.text = "An unexpected error occurred.";
//         }

//         this.timeout = 3000; // Set timeout for snackbar on error
//     }

//     // Validate form after submission attempt
//     this.$refs.form.validate();
// }
async RegisterformSubmit() {
    this.loading = true;
    const config = {
        headers: { 'content-type': 'multipart/form-data' }
    };

    try {
        // Send registration request
        const response = await axios.post('/api/user/register', this.form1, config);
        console.log("API response", response);

        // Show success message
        this.loading = false;
        this.snackbar = true;
        this.text = response.data.message || "Registration successful!"; // Fallback message
        this.timeout = 3000; // Set timeout for snackbar

        // Redirect to login page after snackbar timeout
        setTimeout(() => {
            // Automatically click on the login link
            document.querySelector('a[href="/user/login"]').click();
        }, 3000);
    } catch (error) {
        // Handle registration error
        this.snackbar = true;
        this.loading = false;

        if (error.response) {
            // Axios error with response data
            if (error.response.status === 400) {
                this.text = error.response.data.message || "Something went wrong."; // Fallback message
            } else {
                this.text = error.response.data.message || "An error occurred."; // Fallback message
            }
        } else if (error.request) {
            // No response received from the server
            this.text = "No response received from the server.";
        } else {
            // Some other error occurred
            this.text = "An unexpected error occurred.";
        }

        this.timeout = 3000; // Set timeout for snackbar on error
    }

    // Validate form after submission attempt
    this.$refs.form.validate();
}
}}
</script>

<style scoped>

</style>
