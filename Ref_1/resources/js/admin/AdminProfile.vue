<template>
    <div>
      <h4>Profile</h4>
      <v-tabs v-model="tab" >
        <v-tab >General</v-tab>
        <v-tab >Localization</v-tab>
        <v-tab class="d-none">Payment Commission</v-tab>
        <v-tab >Change password</v-tab>
        <v-tab >App Settings</v-tab>
      </v-tabs>
      <v-tabs-items v-model="tab" >
        <hr class="m-0">
        <v-tab-item>
            <v-card class="elevation-1 py-3">
              <v-form ref="form" v-model="valid">
                <v-col cols="12" md="12">
                  <div v-if="files != '' ">
                    <v-avatar size="100" v-for="(file, f) in files" :key="f">
                      <img :ref="'file'"  style="display:block">
                    </v-avatar>
                  </div>
                  <div v-else-if="getadmindata.user_avatar != null ">
                    <v-avatar size="100">
                      <img :src=(url+getadmindata.user_avatar)>
                    </v-avatar>
                  </div>
                  <div v-else>
                    <v-avatar size="100" color="error">
                      <span class="white--text fw-bold text-h4">{{getadmindata.name.charAt(0)}}{{firstCharOfLastWord}}</span>
                    </v-avatar>
                  </div>
                </v-col>
                <v-col cols="12" md="12">
                  <v-file-input dense label="Add Image" v-model="files" multiple small-chips accept="image/*" @change="addFiles" v-on:click="isHidden = true"></v-file-input>
                </v-col>
                <v-col cols="12" md="12">
                  <v-text-field clearable dense v-model="getadmindata.name" :rules="nameRules" label="Enter your name" required ></v-text-field>
                </v-col>
                <v-col cols="12" md="12" >
                  <v-text-field dense clearable v-model="getadmindata.email" :rules="emailRules" label="Enter e-mail address" required ></v-text-field>
                </v-col>
                <div class="text-end">
                  <v-btn :disabled="!valid" class="mr-4" color="red" dark @click="updateadminProfile">UPDATE</v-btn>
                </div>
              </v-form>
            </v-card>
        </v-tab-item>
        <v-tab-item >
          <v-card class="elevation-1 py-3">
            <v-form ref="form" v-model="valid">
              <v-col cols="12" md="12">
                <v-autocomplete dense clearable v-model="getlocation.locale_id" :items="getlocale" item-text="locale_name" item-value="id" label="Locale"></v-autocomplete>
              </v-col>
              <v-col cols="12" md="12">
                <v-autocomplete dense  clearable v-model="getlocation.timezone_id" :items="gettimezone" item-text="timezone_name" item-value="id" label="Timezone"></v-autocomplete>
              </v-col>
              <v-col cols="12" md="12">
                <v-autocomplete dense clearable v-model="getlocation.currency_id" :items="getcurrencies" item-text="currency_code" item-value="id" label="Currency"></v-autocomplete>
              </v-col>
              <div class="text-end">
                <v-btn class="mr-4" color="red" dark @click="updatelocations">UPDATE</v-btn>
              </div>
            </v-form>
          </v-card>
        </v-tab-item>
        <v-tab-item class="d-none">
          <v-card class="elevation-1 py-3">
            <v-form ref="form" v-model="valid">
              <v-col cols="12" md="12">
                <v-text-field type="number" v-model="Commission" label="Commission (%) for reseller credit amount" required ></v-text-field>
              </v-col>
              <div class="text-end">
                <v-btn class="mr-4" color="red" dark>UPDATE</v-btn>
              </div>
            </v-form>
          </v-card>
        </v-tab-item>
        <v-tab-item >
          <v-card class="elevation-1 py-3" >
            <v-form ref="form" v-model="valid">
              <v-col cols="12" md="12">
                <!-- <v-text-field dense v-model="password" :rules="passwordRules" label="Change password" hint="Leave empty if you don't want to change the password." persistent-hint required ></v-text-field> -->
                <v-text-field v-model="password" :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"
                                              :rules="passwordRules"
                                              :type="show1 ? 'text' : 'password'" name="input-10-1"
                                              label="Password"
                                              hint="Leave empty if you don't want to change the password."
                                              persistent-hint
                                              @click:append="show1 = !show1"
                                ></v-text-field>
              </v-col>
              <div class="text-end">
                <v-btn :disabled="!valid" class="mr-4" color="red" dark @click="updateadminPassword">UPDATE</v-btn>
              </div>
            </v-form>
          </v-card>
        </v-tab-item>
        <v-tab-item >
          <v-card class="elevation-1 py-3">
            <v-form ref="form"  v-model="valid">
              <v-col cols="12" md="12">
                <v-switch dense v-model="getlocation.reseller_status" label="Reseller Registration" color="red" value="1" hide-details></v-switch>
              </v-col>
              <v-col cols="12" md="12">
                <v-switch dense v-model="getlocation.business_status" label="Business Registration" color="red" value="1" hide-details></v-switch>
              </v-col>
              <div class="text-end">
                <v-btn class="mr-4" color="red" dark @click="updateappsetting">UPDATE</v-btn>
              </div>
            </v-form>
          </v-card>
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

export default{
    name: "ProfileView",

    data () {
      return {
        show1: false,
        tab: null,
        getadmindata: [],
        getlocale: [],
        gettimezone: [],
        getcurrencies: [],
        getlocation: [],
        get_id: '',
        files: [],
        valid: false,
        readers: [],
        isHidden: false,
        password: '',
        Commission: '',
        snackbar: false,
        text: '',
        reseller_status: '',
        business_status: '',
        timeout: -1,
        url:'https://bizlx.s3.ap-south-1.amazonaws.com',
        // url:'http://127.0.0.1:8000',
        nameRules: [
          v => !!v || 'Name is required.',
          v => v.length <= 50 || 'Name must be less than 50 characters.',
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
      }
    },
  mounted() {
      this.getadminprofile();
      this.getlocations();
      this.getadminlocation();
      this.addFiles();
  },
  computed: {
    lastWord() {
      const words = this.getadmindata.name.split(' ');
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

    getadminprofile(){
      axios.get('/api/admin/Profile')
          .then((resp)=>{
            this.getadmindata = resp.data.Adminprofile;
          });
    },
    getadminlocation(){
      axios.get('/api/locales/timezones/currencies')
          .then((resp)=>{
            this.getlocale = resp.data.locales;
            this.gettimezone = resp.data.timezones;
            this.getcurrencies = resp.data.currencies;
          });
    },
    getlocations(){
      axios.get('/api/profile/details')
          .then((resp)=>{
            this.getlocation = resp.data.profile_details;
            this.get_id = resp.data.profile_details.id;
          });
    },
    updatelocations() {
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let data = {
        id: this.get_id,
        locale_id: this.getlocation.locale_id,
        timezone_id: this.getlocation.timezone_id,
        currency_id: this.getlocation.currency_id,
      };
      axios.post('/api/update/profile/details',data,config)
          .then((resp) =>{
            this.snackbar = true;
            this.text = resp.data.message;
            this.timeout = 3000;
            this.getlocations();
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
    updateappsetting() {
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let data = {
        id: this.get_id,
        business_status: this.getlocation.business_status,
        reseller_status: this.getlocation.reseller_status,
      };
      axios.post('/api/update/profile/details',data,config)
          .then((resp) =>{
            this.snackbar = true;
            this.text = resp.data.message;
            this.timeout = 3000;
            this.getlocations();
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
    updateadminProfile() {
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let data = {
        name: this.getadmindata.name,
        email: this.getadmindata.email,
        user_avatar: this.files[0],
      };
      axios.post('/api/admin/Profile/update',data,config)
          .then((resp) =>{
            this.snackbar = true;
            this.text = resp.data.adminupdateprofile;
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

    updateadminPassword() {
      axios.post('/api/admin/password/update',{
        password: this.password,
      },)
          .then((resp) =>{
            this.snackbar = true;
            this.text = resp.data.adminpassword;
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
  }


}
</script>
<style scoped>
.v-window {
   overflow: unset;
  }
</style>
