<template>
  <div>
    <v-card class="elevation-1 py-3 container-fluid gx-md-4 my-2">
      <h5 class="pb-4">Reseller Registration Image</h5>
      <hr>
      <v-form ref="form" v-model="valid">
        <v-col cols="12" md="12" class="py-0">
          <div v-if="files != '' ">
            <v-avatar size="130"  v-for="(file, f) in files" :key="f">
              <img :ref="'file'"  style="display:block">
            </v-avatar>
          </div>
          <div v-else>
            <v-avatar size="130">
              <img :src=(url+getimages.reseller_reg_img)>
            </v-avatar>
          </div>
        </v-col>
        <v-col cols="12" md="12">
          <v-file-input dense label="Upload Image" v-model="files" multiple small-chips accept="image/*" @change="addFiles" v-on:click="isHidden = true"></v-file-input>
        </v-col>
        <div class="text-end">
          <v-btn class="mr-4" color="error" @click="updateresellerimage">SAVE</v-btn>
        </div>
      </v-form>
    </v-card>
    <v-card class="elevation-1 py-3 container-fluid gx-md-4 ">
      <h5 class="pb-4">Business Registration Image</h5>
      <hr>
      <v-form ref="form" v-model="valid">
        <v-col cols="12" md="12" class="py-0">
          <div v-if="files1 != '' ">
            <v-avatar size="130"  v-for="(file1, f) in files1" :key="f">
                <img :ref="'file1'"  style="display:block">
            </v-avatar>
          </div>
          <div v-else>
            <v-avatar size="130">
              <img :src=(url+getimages.business_reg_img)>
            </v-avatar>
          </div>
        </v-col>
        <v-col cols="12" md="12">
          <v-file-input dense label="Upload Image" v-model="files1" multiple small-chips accept="image/*" @change="addFiles1" v-on:click="isHidden1 = true"></v-file-input>
        </v-col>
        <div class="text-end">
          <v-btn class="mr-4" color="error" @click="updatebusinessimage">SAVE</v-btn>
        </div>
      </v-form>
    </v-card>
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
    name: "RegistrationImage",
    metaInfo: {title: 'Bizlx Registration Image'},
  data () {
    return {
      files: [],
      getimages: [],
      getimages_id: '',
      files1: [],
      valid: false,
      readers: [],
      readers1: [],
      isHidden: false,
      isHidden1: false,
      password: '',
      Commission: '',
      snackbar: false,
      text: '',
      timeout: -1,
      url: 'https://bizlx.s3.ap-south-1.amazonaws.com',
    }
  },
  mounted() {
    this.getRegisterimages();
    this.addFiles();
    this.addFiles1();
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
      addFiles1(){
          this.files1.forEach((file1, f) => {
            this.readers1[f] = new FileReader();
            this.readers1[f].onloadend = () => {
              let fileData = this.readers1[f].result
              let imgRef = this.$refs.file1[f]
              imgRef.src = fileData
              // send to server here...
            }
            this.readers1[f].readAsDataURL(this.files1[f]);
          })
        },
      getRegisterimages(){
        axios.get('/api/profile/details')
            .then((resp)=>{
              this.getimages = resp.data.profile_details;
              this.getimages_id = resp.data.profile_details.id;
            });
      },
      updateresellerimage() {
        const config = {
          headers: { 'content-type': 'multipart/form-data' }
        }
        let data = {
          id: this.getimages_id,
          reseller_reg_img: this.files[0],
        };
        axios.post('/api/update/profile/details',data,config)
            .then((resp) =>{
              this.snackbar = true;
              this.text = resp.data.message;
              this.timeout = 3000;
              this.getRegisterimages();
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
      updatebusinessimage() {
        const config = {
          headers: { 'content-type': 'multipart/form-data' }
        }
        let data = {
          id: this.getimages_id,
          business_reg_img: this.files1[0],
        };
        axios.post('/api/update/profile/details',data,config)
            .then((resp) =>{
              this.snackbar = true;
              this.text = resp.data.message;
              this.timeout = 3000;
              this.getRegisterimages();
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
    }
}
</script>
