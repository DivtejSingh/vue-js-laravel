<template>
    <div class="p-2">
        <h4 class="ms-4">Profile</h4>
        <v-tabs v-model="tab">
            <v-tab cols="6">GENERAL</v-tab>
            <v-tab cols="6">CHANGE PASSWORD</v-tab>
        </v-tabs>
        <v-tabs-items v-model="tab">
            <v-tab-item class="mt-4">
                <v-card color="basil" flat>
                    <v-form ref="form1" v-model="valid1">
                        <v-row>
                            <v-col cols="12" md="12">
                                <div v-if="files != '' ">
                                    <v-avatar size="100" v-for="(file, f) in files" :key="f">
                                        <img :ref="'file'"  style="display:block">
                                    </v-avatar>
                                </div>
                                <div v-else-if="userprofile.user_avatar != null ">
                                    <v-avatar size="100">
                                        <img :src=(url+userprofile.user_avatar)>
                                    </v-avatar>
                                </div>
                                <div v-else>
                                    <v-avatar size="100" color="error">
                                        <span class="white--text fw-bold text-h4">{{userprofile.name.charAt(0)}}{{firstCharOfLastWord}}</span>
                                    </v-avatar>
                                </div>
                            </v-col>
                            <v-col cols="12" md="12" class="py-0">
                                <v-file-input label="Add Image" v-model="files" multiple small-chips accept="image/*" @change="addFiles" v-on:click="isHidden = true"></v-file-input>
                            </v-col>
                            <v-col cols="12" md="12" class="py-0">
                                <v-text-field label="Name" v-model="userprofile.name" :rules="nameRules" required ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="12" class="py-0">
                                <v-text-field label="E-mail address" v-model="userprofile.email" :rules="emailRules" required ></v-text-field>
                            </v-col>
                            <!-- <v-col cols="12" md="12" class="py-0">
                                <v-text-field label="Username" v-model="userprofile.username" :rules="usernameRules" required ></v-text-field>
                            </v-col> -->
                            <v-col cols="12" md="12" class="py-0">
                                <v-autocomplete class=""
                                                v-model="cid"
                                                @change="getStatesOnChange"
                                                :items="countries"
                                                :rules="countryRules"
                                                label="Country"
                                                item-text="country"
                                                item-value="id">
                                </v-autocomplete>
                                <v-autocomplete class=""
                                                v-model="state_id"
                                                @change="getCitiesOnChange"
                                                :items="allstates"
                                                :rules="stateRules"
                                                label="State"
                                                item-text="state"
                                                item-value="id">
                                </v-autocomplete>
                                <v-autocomplete class=""
                                                v-model="city_id"
                                                :rules="cityRules"
                                                :items="cities"
                                                label="City"
                                                item-text="city"
                                                item-value="id">
                                </v-autocomplete>
                            </v-col>
                            <v-col cols="12" md="12" class="py-0">
                                <v-text-field label="Mobile" v-model="userprofile.mobile_phone" :rules="mobileRules" required ></v-text-field>
                            </v-col>
                        </v-row>
                        <div class="text-end">
                            <v-btn class="my-2 ms-4" color="error" :disabled="loading" @click="updateuserProfile">UPDATE</v-btn>
                        </div>
                    </v-form>
                </v-card>
            </v-tab-item>
            <v-tab-item >
                <v-card color="basil" flat >
                    <v-form ref="form" v-model="valid">
                        <v-row>
                            <v-col class="py-2" cols="12" md="12">
                                <v-text-field v-model="password" :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"
                                              :rules="passwordRules"
                                              :type="show1 ? 'text' : 'password'" name="input-10-1"
                                              label="Password"
                                              hint="Leave empty if you don't want to change the password."
                                              persistent-hint
                                              @click:append="show1 = !show1"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <div class="text-end">
                            <v-btn class="my-3 ms-4" color="error" @click="updateuserPassword" :disabled="!valid">UPDATE</v-btn>
                        </div>
                    </v-form>
                </v-card>
            </v-tab-item>
        </v-tabs-items>
    </div>
</template>

<script>
import axios from "axios";
export default {
    name: "UserProfile",
    data() {
        return {
            loading : false,
            valid1: false,
            valid: false,
            isHidden: false,
            userprofile: [],
            files: [],
            readers: [],
            countries:[],
            allstates:[],
            cities:[],
            cid:null,
            state_id:'',
            city_id:'',
            show1: false,
            snackbar: false,
            text: '',
            timeout: -1,
            password: '',
            tab: '',
            url:'https://bizlx.s3.ap-south-1.amazonaws.com',
            nameRules: [
                v => !!v || 'The name field is required.',
                v => v.length <= 100 || 'Name must be less than 100 characters.',
            ],
            emailRules: [
                v => !!v || 'The email field is required.',
                v => /.+@.+/.test(v) || 'E-mail must be valid.',
            ],
            mobileRules: [
                v => !!v || 'The mobile number field is required.',
                v => v.length != 9 ||  'Mobile number must be 10 characters.',
            ],
            usernameRules: [
                v => !!v || 'The username field is required.',
                v => v.length <= 80 || 'Username must be less than 80 characters.',
                v => (v.split(' ').length <= 1) || 'Space not allowed',
                v => /^[\w\s]+$/.test(v) || 'Username may only contain letters, numbers, dashes and underscores'
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
            passwordRules: [
                v => !!v || 'The password field is required.',
                // v => /[A-Z]/.test(v) || 'Use upper case.',
                // v => /[a-z]/.test(v) || 'Use lower case.',
                // v => /[0-9]/.test(v) || 'Use number.',
                // v => /[#?!@$%^&*-]/.test(v) || 'Use special character.',
                v => v.length >= 8 || 'Password must be greater than 8 characters.',
                v => v.length <= 15 || 'Password must be less than 15 characters.',
            ],
        }
    },
    computed: {
        lastWord() {
            const words = this.userprofile.name.split(' ');
            return words[words.length - 1];
        },
        firstCharOfLastWord() {
            return this.lastWord.charAt(0);
        }
    },
    mounted() {
        this.userRole();
        if(this.userRole() == 1){
            window.location.href = '/business/profile';
        }
        this.getUserprofile();
        this.All_Countries();
        this.addFiles();
    },
    methods:{
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
        getUserprofile(){
            axios.get('/api/user/profile')
                .then((resp) =>{
                    this.userprofile = resp.data.userprofile[0];
                    this.cid = resp.data.userprofile[0].country_id;
                    this.state_id = resp.data.userprofile[0].state_id;
                    this.city_id = resp.data.userprofile[0].city_id;
                })
        },
        All_Countries(){
            axios.get('/api/countries')
                .then((resp)=>{
                    this.countries = resp.data.countries;
                    this.getStatesOnChange();
                });
        },
        getStatesOnChange() {
            axios.get("/api/get/states/"+this.cid)
                .then(response => {
                    this.allstates = response.data;
                    this.getCitiesOnChange();
                })
        },
        getCitiesOnChange() {
            var state = this.state_id;
            axios.get(`/api/states/${state}`)
                .then(response => {
                    this.cities = response.data;
                })
        },
        updateuserProfile() {
            this.loading = true;
            const config = {
                headers: { 'content-type': 'multipart/form-data' }
            }
            let data = {
                name: this.userprofile.name,
                email: this.userprofile.email,
                mobile_phone: this.userprofile.mobile_phone,
                // username: this.userprofile.username,
                user_avatar: this.files[0],
                country_id: this.cid,
                state_id: this.state_id,
                city_id: this.city_id,

            };
            axios.post('/api/update/user/profile',data, config)
                .then((resp) => {
                    this.loading = false;
                    this.snackbar = true;
                    this.text = resp.data.user_profile;
                    this.timeout = 3000;
                    this.getUserprofile();
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
            this.$refs.form1.validate();
        },
        updateuserPassword() {
            const config = {
                headers: { 'content-type': 'multipart/form-data' }
            }
            let data = {
                password: this.password,
            };
            axios.post('/api/update/user/password',data, config)
                .then((resp) => {
                    if(data.status === 200){
                        this.snackbar = true;
                        this.text = resp.data.user_password;
                        this.timeout = 3000;
                    }else{
                        this.snackbar = true;
                        this.text = resp.data.user_password;
                        this.timeout = 3000;
                    }
                })
            this.$refs.form.validate();
            this.$refs.form.reset();
        },
        userRole(){
            return this.$store.state.userData.user_role;
    
        }
    }
}
</script>

<style scoped>

</style>
