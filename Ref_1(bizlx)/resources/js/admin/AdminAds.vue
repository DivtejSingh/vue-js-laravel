<template>
  <div>
    <div class="row">
      <div class="col-12 col-md-4"><h4>Top Ads</h4></div>
      <div class="col-12 col-md-6">
        <v-text-field v-model="search" clearable append-icon="mdi-magnify" label="Search" single-line hide-details></v-text-field>
      </div>
      <div class="col-12 col-md-2">
        <v-btn @click="openModel" tile color="error" large>
          <v-icon left>mdi-plus</v-icon>
          ADD NEW ADS
        </v-btn>
      </div>
    </div>
    <div class="row mt-10">
      <v-data-table :headers="headers" :search="search" :items="adss" sort-by="calories" class="elevation-1">
        <template v-slot:[`item.ad_img_url`]="{ item }">
          <img :src=(url+item.ad_img_url) class="py-1" style="width: 100px; height: 60px"/>
        </template>
        <template v-slot:top>
          <v-dialog v-model="dialog" persistent max-width="500px">
            <v-card>
              <v-card-title style="justify-content:space-between;" class="text-h6 grey lighten-2">Edit Ads
                <v-icon  @click="dialog = false" class="text-h5">mdi-close</v-icon>
              </v-card-title>
              <v-card-text class="p-0">
                <v-container>
                  <v-form ref="form" v-model="valid">
                    <v-row>
                      <v-col cols="4">
                        <v-col sm="4" v-for="(file2, f) in filesEdit" :key="f">
                          <img :ref="'file2'" width="100"
                               height="100" style="display:block">
                        </v-col>
                        <div v-if="isHidden2">
                          <img :src=(adss.ad_img_url)>
                        </div>
                        <div v-else>
                          <img :src=(url+editedItem.ad_img_url) style="width: 100px;">
                        </div>
                      </v-col>
                      <v-col cols="8">
                        <v-file-input accept="image/*" label="Image" prepend-icon="mdi-camera" multiple
                                      small-chips
                                      :rules="filesRules"
                                      v-model="filesEdit"
                                      @change="editFiles"
                                      v-on:click="isHidden2 = true">
                        </v-file-input>
                      </v-col>
                      <v-col cols="12" md="12">
                        <v-autocomplete dense :rules="businessRules" v-model="editedItem.business_id" item-text="name" :items="business"
                                        label="Select Business" item-value="id"></v-autocomplete>
                        <div>
                          <v-checkbox dense v-model="editedItem.ad_status" label="Active"></v-checkbox>
                        </div>
                        <v-card-actions>
                          <v-btn @click="edithomeads" class="my-4 me-3" color="red" dark :disabled="!valid">UPDATE</v-btn>
                          <v-btn @click="close" class="my-4 me-3" color="red" dark>Cancel</v-btn>
                        </v-card-actions>
                      </v-col>
                    </v-row>
                  </v-form>
                </v-container>
              </v-card-text>
            </v-card>
          </v-dialog>
          <v-dialog v-model="dialogDelete" max-width="500px">
            <v-card>
              <v-card-title class="text-h6">Are you sure you want to delete this Top Ads?</v-card-title>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error" text @click="closeDelete">Cancel</v-btn>
                <v-btn color="error" text @click="deletehomeads">OK</v-btn>
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </template>
        <template v-slot:[`item.actions`]="{ item }">
          <v-icon small class="mr-2" @click="editItem(item)">
            mdi-pencil
          </v-icon>
          <v-icon small @click="deleteItem(item)"> mdi-delete</v-icon>
        </template>
        <template v-slot:[`item.ad_status`]="{ item }">
          <div v-if="item.ad_status == '1'">
            <span>Active</span>
          </div>
          <div v-else>
            <span>Inactive</span>
          </div>
        </template>
      </v-data-table>
    </div>
    <v-dialog v-model="showModel" persistent max-width="500px">
      <v-card>
        <v-card-title style="justify-content:space-between;" class="text-h6 grey lighten-2">Add New Ads
          <v-icon  @click="showModel = false" class="text-h5">mdi-close</v-icon>
        </v-card-title>
        <v-card-text>
          <v-form ref="form" v-model="valid">
            <v-row>
              <v-col cols="12" md="12">
                <v-file-input accept="image/*" dense label="Image" prepend-icon="mdi-camera"
                              small-chips
                              v-model="files"
                              @change="addFiles"
                              :rules="filesRules">
                </v-file-input>
              </v-col>
              <v-col cols="12" md="12">
                <v-autocomplete dense :items="business" v-model="business_id" :rules="businessRules" label="Select Business" item-text="name" item-value="id"></v-autocomplete>
                <div>
                  <v-btn class="my-4 me-3" color="error" :disabled="!valid" @click="Addhomeads">Save</v-btn>
                  <v-btn @click="showModel = false" color="error" dark class="my-4 me-3">CLOSE</v-btn>
                </div>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-snackbar v-model="snackbar" :timeout="timeout">{{ text }}
      <template v-slot:action="{attrs}">
        <v-btn color="blue" text v-bind="attrs" @click="snackbar = false">Close</v-btn>
      </template>
    </v-snackbar>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "AdsView",
  components: {},
  data: () => ({
    url:'https://bizlx.s3.ap-south-1.amazonaws.com',
    // url:'https://bizlx.s3.ap-south-1.amazonaws.com',
    isHidden2: false,
    files: [],
    filesEdit: [],
    readers: [],
    snackbar: false,
    text: '',
    timeout: -1,
    valid: false,
    filesRules: [
      v => !!v || 'Top Ads image is required.',
    ],
    businessRules: [
      v => !!v || 'Business Name is required.',
    ],
    search: '',
    showModel: false,
    business: [],
    business_id: '',
    dialog: false,
    dialogDelete: false,
    headers: [
      { text: 'Image', align: 'start', sortable: false, value: 'ad_img_url'},
      { text: 'Business Name', value: 'business_name'},
      { text: "Status", align: 'end', value: "ad_status", sortable: false },
      { text: 'Actions', align: 'end', value: 'actions', sortable: false},
    ],
    adss: [],
    editedIndex: -1,
    editedItem: {
      id: '',
      ad_img_url: '',
      business_id: '',
      ad_status: '',
    },
  }),

  watch: {
    dialog(val) {
      val || this.close()
    },
    dialogDelete(val) {
      val || this.closeDelete()
    },
  },

  created() {
    this.getAds();
    this.getbusiness();
    this.addFiles();
    this.editFiles();
  },

  methods: {
    getAds() {
      axios.get('/api/admin/get/topads')
          .then((resp)=>{
            this.adss = resp.data.topads;
          });
    },
    getbusiness() {
      axios.get('/api/businesses/list')
          .then((resp)=>{
            this.business = resp.data.business;
            this.business_id = resp.data.business.id;
          });
    },
    addFiles(){
      this.files.forEach((file, f) => {
        this.readers[f] = new FileReader();
        this.readers[f].onloadend = () => {
          let fileData = this.readers[f].result
          let imgRef = this.$refs.file[f]
          imgRef.src = fileData,
              this.defaultItem.ad_img_url = this.files[0]
        }
        this.readers[f].readAsDataURL(this.files[f]);
      })
    },
    editFiles(){
      this.filesEdit.forEach((file2, f) => {
        this.readers[f] = new FileReader();
        this.readers[f].onloadend = () => {
          let fileData = this.readers[f].result
          let imgRef = this.$refs.file2[f]
          imgRef.src = fileData,
              this.editedItem.ad_img_url = this.filesEdit[0]
        }
        this.readers[f].readAsDataURL(this.filesEdit[f]);
      })
    },
    Addhomeads(){ // new add
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let data = {
        business_id: this.business_id,
        ad_status: 1,
        ad_img_url: this.files,
      };
      axios.post("/api/admin/add/topads", data, config)
          .then((resp) => {
            this.snackbar = true;
            this.text = resp.data.message;
            this.showModel = false;
            this.timeout = 3000;
            this.getAds();
            this.$refs.form.reset();
            if(resp.data.error){
              if(resp.data.error.business_id){
                this.snackbar = true;
                this.text = 'The Business Name has already been taken.';
              }
            }
          })
      this.$refs.form.validate();
    },
    edithomeads() {
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let data = {
        business_id: this.editedItem.business_id,
        ad_status: this.editedItem.ad_status,
        ad_img_url: this.filesEdit[0],
      };
      console.log(data);
      if (this.editedIndex > -1) { // Edit
        axios.post("/api/admin/edit/topads/"+this.editedItem.id, data, config)
            .then((resp) => {
              this.snackbar = true;
              this.text = resp.data.message;
              this.timeout = 3000;
              this.getAds();
              this.close();
              this.$refs.form.reset();
              this.isHidden2 = false;
            })
            .catch(error=>{
              this.error = error;
              this.snackbar = true;
              this.text = "Something went wrong";
              this.timeout = 3000;
              this.getAds();
            });
      }
      this.$refs.form.validate();
    },
    deletehomeads(){
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let data = {
        topads_id: this.editedItem.id,
      };
      console.log(data);
      axios.post('/api/admin/delete/topads', data, config)
          .then((resp) =>{
            this.snackbar = true;
            this.timeout = 3000;
            this.text = resp.data.message;
            this.dialogDelete = false;
            this.getAds();
          })
          .catch(error => {
            // Handle the error
            var data = error.toJSON();
            if(data.status == 400){
              this.snackbar = true;
              this.timeout = 3000;
              this.text = error.data.error.message;
            }
          });
    },
    openModel() {
      this.showModel = true;
    },

    editItem(item) {
      this.editedIndex = this.adss.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },

    deleteItem(item) {
      this.editedIndex = this.adss.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialogDelete = true
    },

    close() {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },

    closeDelete() {
      this.dialogDelete = false
      this.$refs.form.reset();
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },
  }
}
</script>
