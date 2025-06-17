<template>
    <div>
      <h4 class="ms-4">Reseller Profile</h4>
      <v-tabs v-model="tab"  class="ms-4">
              <v-tab cols="6">GENERAL</v-tab>
              <v-tab cols="6">CHANGE PASSWORD</v-tab>
              <v-tab cols="6">Reviews</v-tab>
              <v-tab cols="6">Wishlist</v-tab>
              <v-tab cols="6">Payment</v-tab>
            </v-tabs>
      <v-tabs-items v-model="tab"  class="ms-4">
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
                  <div v-else-if="reseller.user_avatar != null ">
                    <v-avatar size="100">
                      <img :src=(url+reseller.user_avatar)>
                    </v-avatar>
                  </div>
                  <div v-else>
                    <v-avatar size="100" color="error">
                      <span class="white--text fw-bold text-h4">{{reseller.name.charAt(0)}}{{firstCharOfLastWord}}</span>
                    </v-avatar>
                  </div>
                </v-col>
                <v-col cols="12" md="12">
                  <v-file-input label="Add Image" v-model="files" multiple small-chips accept="image/*" @change="addFiles" v-on:click="isHidden = true"></v-file-input>
                </v-col>
                <v-col cols="12" md="12" class="py-0">
                  <v-text-field label="Name" v-model="reseller.name" :rules="nameRules" required ></v-text-field>
                </v-col>
                <v-col cols="12" md="12" class="py-0">
                  <v-text-field label="E-mail address" v-model="reseller.email" :rules="emailRules" required ></v-text-field>
                </v-col>
                <v-col cols="12" md="12" class="py-0">
                  <v-text-field type="number" label="Enter Mobile" v-model="reseller.mobile_phone" :rules="mobileRules" required ></v-text-field>
                </v-col>
                <v-col cols="12" md="12" class="py-0">
                  <v-text-field label="Reseller ID" v-model="reseller.username" required readonly></v-text-field>
                </v-col>
                <v-col cols="12" md="12" class="py-0">
                  <v-text-field label="GST" class="d-none"  required ></v-text-field>
                </v-col>
                <v-col cols="12" md="12" class="py-0">
                  <v-select :items="items1" label="Select Gender" v-model="reseller.reseller_gender" dense></v-select>
                </v-col>
                <v-col cols="12" md="12" class="py-0">
                  <v-autocomplete label="Select Your Profession" v-model="reseller.profession_id" :rules="profiaction"
                                  :items="allProfessions" item-text="profession" item-value="id" prepend-inner-icon="mdi-tray-arrow-down"
                  ></v-autocomplete>
                </v-col>
                <v-col cols="12" md="12" class="py-0">
                  <div>
                    <v-autocomplete :items="allSkills" :rules="skill" v-model="skillId" prepend-inner-icon="mdi-code-tags"
                                    label="Skills & Experience" item-text="skill" item-value="id" multiple persistent-hint
                    ></v-autocomplete>
                  </div>
  
                </v-col>
                <v-col cols="12" md="12" class="py-0">
                  <v-autocomplete label="Select State" :rules="stateRules" v-model="stateId" @change="getCities" @selected="getCities()"
                                  :items="allstates" item-text="state" item-value="id" prepend-inner-icon="mdi-city"
                  ></v-autocomplete>
                </v-col>
                <v-col cols="12" md="12" class="py-0">
                  <v-autocomplete label="Select City" :rules="cityRules" v-model="reseller.city_id"
                                  :items="cities" item-text="city" item-value="id" prepend-inner-icon="mdi-home-city"
                  ></v-autocomplete>
                </v-col>
                <v-col cols="12" md="12" class="py-0">
                  <v-textarea rows="3" row-height="20" :rules="addressRules" v-model="reseller.address" label="Select Address"></v-textarea>
                </v-col>
              </v-row>
              <div class="text-end">
                <v-btn class="my-2 ms-4" color="error" @click="updateresellerProfile" :disabled="!valid1">UPDATE</v-btn>
              </div>
            </v-form>
          </v-card>
        </v-tab-item>
        <v-tab-item >
          <v-card color="basil" flat >
            <v-form ref="form2" v-model="valid2">
              <v-row>
                <v-col class="py-2" cols="12" md="12">
                  <v-text-field v-model="password"
                                :rules="passwordRules"
                                label="Password"
                                hint="Leave empty if you don't want to change the password."
                                persistent-hint
                  ></v-text-field>
                </v-col>
              </v-row>
              <div class="text-end">
                <v-btn class="my-3 ms-4" color="error" @click="updateresellerPassword" :disabled="!valid2">UPDATE</v-btn>
              </div>
            </v-form>
          </v-card>
        </v-tab-item>
      
      <v-tab-item>
      <AdminResellerReviews />
    </v-tab-item>
      <v-tab-item>
        <AdminResellerWishlistEdit />
      </v-tab-item>
      <v-tab-item>
        <AdminResellerPayment />
      </v-tab-item>
  </v-tabs-items>
      <v-snackbar v-model="snackbar" :timeout="timeout" type="success" border="left" elevation="3">
        <template v-slot:action="{ attrs }">
          <v-btn color="blue"  v-bind="attrs" text @click="snackbar = false">Close</v-btn>
        </template>
        {{text}}
      </v-snackbar>
    </div>
  </template>
  <script>
  
  import axios from "axios";
  import AdminResellerReviews from '../admin/AdminResellerReviews.vue';
  import AdminResellerWishlistEdit from '../admin/AdminResellerWishlistEdit.vue';
  import AdminResellerPayment from '../admin/AdminResellerPayment.vue';
  export default {
    name: 'ResellerProfile',
    components: {
      AdminResellerReviews, AdminResellerWishlistEdit, AdminResellerPayment
    },
    metaInfo: {title: 'Reseller Profile'},
    data() {
      return {
        tab:0,
        allstates:[],
        cities:[],
        allProfessions:[],
        allSkills:[],
        reseller:[],
        isHidden: false,
        files: [],
        readers: [],
        items1: ['Male', 'Female'],
        stateId: '',
        skillId: [],
        password: '',
        show1: false,
        valid1: false,
        valid2: false,
        snackbar: false,
        text: '',
        timeout: -1,
        customNoDataText: 'Please Select State First',
        tab: '',
        url:'https://bizlx.itechvision.in',
        // url:'http://127.0.0.1:8000',
        nameRules: [
          v => !!v || 'The name field is required.',
          v => v.length <= 100 || 'Name must be less than 100 characters.',
        ],
        emailRules: [
          v => !!v || 'The email field is required.',
          v => /.+@.+/.test(v) || 'E-mail must be valid.',
        ],
        mobileRules: [
          v => !!v || 'Mobile number field is required.',
          v => v.length != 9 ||  'Mobile number must be 10 characters.',
        ],
  
        addressRules: [
          v => !!v || 'The address field is required.',
        ],
        profiaction: [
          v => !!v || 'The profession field is required.',
        ],
        skill: [
          v => !!v || 'The Skills field is required.',
        ],
        stateRules: [
          v => !!v || 'The State field is required.',
        ],
        cityRules: [
          v => !!v || 'The City filed is required.',
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
      }
    },
    mounted() {
            // Extract Reseller ID from the URL
      const url = window.location.href;
      const lastSegment = url.split('/').filter(Boolean).pop();
      this.resellerId = lastSegment; 
      this.getResellerprofile();
      this.getResellerProfessions();
      this.getResellerskils();
      this.All_States();
      this.getCities();
      this.addFiles();
    },
    // mounted() {
    //   this.getResellerprofile();
    //   this.getResellerProfessions();
    //   this.getResellerskils();
    //   this.All_States();
    //   this.getCities();
    //   this.addFiles();
    // },
    computed: {
      lastWord() {
        const words = this.reseller.name.split(' ');
        return words[words.length - 1];
      },
      firstCharOfLastWord() {
        return this.lastWord.charAt(0);
      }
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
      getResellerprofile() {
        const config = {
          headers: { 'content-type': 'multipart/form-data' }
        }
        let data = {
            resellerId: this.resellerId,
        };
        axios.post('/api/reseller/profile1',data,config)
            .then((resp) =>{
              this.reseller = resp.data.resellers;
              this.stateId = resp.data.resellers.state_id;
              for (var i = 0; i < resp.data.skillids.length; i++){
                this.skillId.push(resp.data.skillids[i].id);
              }
              this.getCities();
              this.getResellerskils();
            })
      },
      getResellerProfessions() {
        axios.get('/api/professions')
            .then((resp)=>{
              this.allProfessions = resp.data.professions;
            });
      },
      getResellerskils() {
        axios.get('/api/skills')
            .then((resp)=>{
              this.allSkills = resp.data.skills;
            });
      },
      All_States(){
        axios.get('/api/allstates')
            .then((resp)=>{
              this.allstates = resp.data.locations;
            });
      },
      getCities() {
        if (this.stateId) {
          axios.get(`/api/states/${this.stateId}`)
              .then(response => {
                this.cities = response.data;
              })
              .catch(error => {
                console.log(error);
              });
        } else {
          this.cities = [];
        }
      },
      updateresellerProfile() {
        const config = {
          headers: { 'content-type': 'multipart/form-data' }
        }
        let data = {
          name: this.reseller.name,
          email: this.reseller.email,
          mobile_phone: this.reseller.mobile_phone,
          reseller_gender: this.reseller.reseller_gender,
          profession_id: this.reseller.profession_id,
          skills_id: this.skillId,
          state_id: this.stateId,
          city_id: this.reseller.city_id,
          address: this.reseller.address,
          user_avatar: this.files[0],
          reseller_id: this.resellerId
        };
        axios.post('/api/update/reseller/updateResellerprofilebyadmin',data, config)
            .then((resp) => {
              this.snackbar = true;
              this.text = resp.data.reseller_profile;
              this.timeout = 3000;
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
        this.$refs.form1.validate();
      },
      updateresellerPassword() {
        const config = {
          headers: { 'content-type': 'multipart/form-data' }
        }
        let data = {
            reseller_id: this.resellerId, 
            password: this.password       
        };
         
        axios.post('/api/update/reseller/updateresellerPasswordbyadmin', data, config)
        .then((resp) => {
            if (resp.status === 200) {
                this.snackbar = true;
                this.text = resp.data.reseller;
                this.timeout = 3000;
            } else {
                this.snackbar = true;
                this.text = resp.data.reseller;
                this.timeout = 3000;
            }
                })

        this.$refs.form2.validate();
        this.$refs.form2.reset();
      },
    }
  }
  </script>