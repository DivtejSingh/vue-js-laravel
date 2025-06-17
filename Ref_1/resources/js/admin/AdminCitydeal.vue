<template>
  <div>
    <div class="row">
      <div class="col-12 col-md-4"><h4>City Deals</h4></div>
      <div class="col-12 col-md-6">
        <v-text-field v-model="search" clearable append-icon="mdi-magnify" label="Search" single-line hide-details></v-text-field>
      </div>
      <div class="col-12 col-md-2">
        <v-btn @click="openModel" tile color="error" large>
          <v-icon left>mdi-plus</v-icon>
          ADD CITY DEAL
        </v-btn>
      </div>
    </div>
    <div class="row mt-10">
      <v-data-table :headers="headers" :search="search" :items="cityslider" class="elevation-1">
        <template v-slot:[`item.city_img_url`]="{ item }">
          <img :src=(url+item.city_img_url) class="py-1" style="width: 60px; height: 60px" :alt="item.city"/>
        </template>
        <template v-slot:top>
          <v-dialog v-model="dialog" persistent max-width="500px">
            <v-card>
              <v-card-title style="justify-content:space-between;" class="text-h6 grey lighten-2">Edit City Deals
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
                          <img :src=(cityslider.city_img_url)>
                        </div>
                        <div v-else>
                          <img :src=(url+editedItem.city_img_url) style="width: 100px;">
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
                        <v-autocomplete dense v-model="editedItem.city_id" :items="cities" item-text="city" :rules="cityRules"
                                        label="Select City" item-value="id"></v-autocomplete>
                          <div>
                            <v-checkbox dense v-model="editedItem.citydeal_status" label="Active"></v-checkbox>
                          </div>
                        <v-card-actions>
                          <v-btn @click="edithomecityslider" class="my-4 me-3" color="error" :disabled="!valid">UPDATE</v-btn>
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
              <v-card-title class="text-h6">Are you sure you want to delete this city slider?</v-card-title>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="error" text @click="closeDelete">Cancel</v-btn>
                <v-btn class="error" text @click="deletehomecityslider">Delete</v-btn>
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </template>
        <template v-slot:[`item.actions`]="{ item }">
          <v-icon small class="mr-2" @click="editItem(item)">
            mdi-pencil
          </v-icon>
          <v-icon small @click="deleteItem(item)">mdi-delete</v-icon>
        </template>
        <template v-slot:[`item.citydeal_status`]="{ item }">
          <div v-if="item.citydeal_status == '1'">
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
        <v-card-title style="justify-content:space-between;" class="text-h6 grey lighten-2">Add New City Deal
          <v-icon @click="showModel = false" class="text-h5">mdi-close</v-icon>
        </v-card-title>
        <v-card-text>
          <v-form ref="form" v-model="valid">
            <v-row>
              <v-col cols="12" md="12">
                <v-file-input accept="image/*" dense label="Add City Image" prepend-icon="mdi-camera"
                              small-chips
                              v-model="files"
                              @change="addFiles"
                              :rules="filesRules">
                </v-file-input>
              </v-col>
              <v-col cols="12" md="12" class="py-1">
                <v-autocomplete dense :items="cities" label="Select City" v-model="city_id" :rules="cityRules" item-text="city" item-value="id"></v-autocomplete>
                <div>
                  <v-btn class="my-4 me-3" color="error" :disabled="!valid" @click="Addhomecityslider">SAVE</v-btn>
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
  name: "AdminCitydeals",
  components: {},
  data: () => ({
    url:'https://bizlx.s3.ap-south-1.amazonaws.com',
    search: '',
    city_id: '',
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
    cityRules: [
      v => !!v || 'City Name is required.',
    ],
    showModel: false,
    cities: [],
    dialog: false,
    dialogDelete: false,
    headers: [
      { text: 'Image', align: 'start', sortable: false, value: 'city_img_url'},
      { text: 'City Name', value: 'city'},
      { text: "Status", align: 'end', value: "citydeal_status", sortable: false },
      { text: 'Actions', align: 'end', value: 'actions', sortable: false},
    ],
    cityslider: [],
    editedIndex: -1,
    editedItem: {
      id: '',
      city_id: '',
      city_img_url: '',
      citydeal_status: '',
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
    this.allCities();
    this.allCitydeals();
  },
  methods: {
    allCitydeals(){
      axios.get('/api/admin/home/cityslider')
          .then((resp)=>{
            this.cityslider = resp.data.citydeals;
          });
    },
    allCities(){
      axios.get('/api/allcities')
          .then((resp)=>{
            this.cities = resp.data.locations;
            this.city_id = resp.data.locations.id;
          });
    },
    addFiles(){
      this.files.forEach((file, f) => {
        this.readers[f] = new FileReader();
        this.readers[f].onloadend = () => {
          let fileData = this.readers[f].result
          let imgRef = this.$refs.file[f]
          imgRef.src = fileData,
              this.defaultItem.city_img_url = this.files[0]
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
              this.editedItem.city_img_url = this.filesEdit[0]
        }
        this.readers[f].readAsDataURL(this.filesEdit[f]);
      })
    },
    Addhomecityslider(){ // new add
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let data = {
        city_id: this.city_id,
        citydeal_status: 1,
        city_img_url: this.files,
      };
      axios.post("/api/admin/add/citydeal", data, config)
          .then((resp) => {
            this.snackbar = true;
            this.text = resp.data.message;
            this.showModel = false;
            this.timeout = 3000;
            this.allCitydeals();
            this.$refs.form.reset();
            if(resp.data.error){
              if(resp.data.error.city_id){
                this.snackbar = true;
                this.text = 'The City Name has already been taken.';
              }
            }
          })
      this.$refs.form.validate();
    },
    edithomecityslider() {
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let data = {
        city_id: this.editedItem.city_id,
        citydeal_status: this.editedItem.citydeal_status,
        city_img_url: this.filesEdit[0],
      };
      if (this.editedIndex > -1) { // Edit
        axios.post("/api/admin/edit/citydeal/"+this.editedItem.id, data, config)
            .then((resp) => {
              this.snackbar = true;
              this.text = resp.data.message;
              this.timeout = 3000;
              this.allCitydeals();
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
    deletehomecityslider(){
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let data = {
        city_id: this.editedItem.id,
      };
      console.log(data);
      axios.post('/api/admin/delete/citydeal', data, config)
          .then((resp) =>{
            this.snackbar = true;
            this.timeout = 3000;
            this.text = resp.data.message;
            this.dialogDelete = false;
            this.allCitydeals();
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
      this.editedIndex = this.cityslider.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },

    deleteItem(item) {
      this.editedIndex = this.cityslider.indexOf(item)
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
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },

    save() {
      if (this.editedIndex > -1) {
        Object.assign(this.adss[this.editedIndex], this.editedItem)
      } else {
        this.adss.push(this.editedItem)
      }
      this.close()
    },
  }
}
</script>
