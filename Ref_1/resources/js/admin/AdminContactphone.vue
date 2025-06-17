<template>
    <div>
      <v-card class="elevation-1 py-3 my-2 container-fluid gx-md-4" >
        <h5 class="pb-4">Contact Phone / Address</h5>
        <hr>
        <v-form ref="form" v-model="valid">
          <v-col cols="12" md="12" class="py-0">
            <v-text-field clearable v-model="getadress.address" label="Address"></v-text-field>
          </v-col>
          <v-col cols="12" md="12" class="py-0">
            <v-text-field clearable v-model="getadress.contact_number" label="Contact"></v-text-field>
          </v-col>
          <div class="text-end">
            <v-btn class="mr-4" color="error" :disabled="!valid" @click="updateaddress">SAVE</v-btn>
          </div>
        </v-form>
      </v-card>
      <v-snackbar clearable v-model="snackbar" :timeout="timeout" type="success" border="left" elevation="3">
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
    name: "ContactPhone",
    metaInfo: {title: 'Bizlx Contact Phone / Address'},
  data () {
    return {
      getadress: [],
      get_id: '',
      snackbar: false,
      valid: false,
      text: '',
      timeout: -1,
    }
  },
  mounted() {
      this.getadresss();
  },
  methods:{
    getadresss(){
      axios.get('/api/profile/details')
          .then((resp)=>{
            this.getadress = resp.data.profile_details;
            this.get_id = resp.data.profile_details.id;
          });
    },
    updateaddress() {
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let data = {
        id: this.get_id,
        address: this.getadress.address,
        contact_number: this.getadress.contact_number,
      };
      axios.post('/api/update/profile/details',data,config)
          .then((resp) =>{
            this.snackbar = true;
            this.text = resp.data.message;
            this.timeout = 3000;
            this.getadresss();
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
<style scoped>
 .container-fluid {
  --bs-gutter-x: 1rem !important;
  }
</style>
