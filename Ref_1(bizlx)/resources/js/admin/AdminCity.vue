<template>
    <div>
      <div class="row">
        <div class="col-3">
          <h3 class="fw-bold">City</h3>
        </div>
        <div class="col-6">
          <v-text-field v-model="search" clearable append-icon="mdi-magnify" label="Search" single-line hide-details>
          </v-text-field>
        </div>
        <div class="col-3 test-decoration-none">
            <v-btn  tile color="error" large @click="addDialog = true">
              <v-icon left>mdi-plus</v-icon>CREATE CITY</v-btn>
        </div>
      </div>
      <div class=" my-5">
        <v-data-table :search="search" :headers="headers" :items="location" sort-by="calories"
                      show-select class="elevation-1" v-model="checkedIds" multi-select>
          <template v-slot:top>
            <v-spacer></v-spacer>
            <v-dialog v-model="dialog" max-width="500px" persistent>
              <v-card>
                <v-card-title style="justify-content:space-between;" class="text-h6 grey lighten-2">Edit City
                  <v-icon  @click="close" class="text-h5">mdi-close</v-icon>
                </v-card-title>
                <v-card-text>
                  <v-container>
                      <v-form ref="form" v-model="valid">
                        <v-row>
                          <v-col cols="12" sm="12" md="12" class="py-1">
                            <v-autocomplete @change="onChangeCountry"
                              v-model="editedItem.country_id"
                              :items="countries"
                              :rules="countryRule"
                              item-text="country"
                              item-value="id"
                              label="Country name"></v-autocomplete>
                          </v-col>
                          <v-col cols="12" sm="12" md="12" class="py-1">
                            <v-autocomplete class="my-0 py-0"
                              v-model="editedItem.state_id"
                              label="Select state"
                              :items="states"
                              item-text="state"
                              item-value="id"
                              :rules="stateRule"></v-autocomplete>
                          </v-col>
                          <v-col cols="12" sm="12" md="12" class="py-1">
                            <v-text-field v-model="editedItem.city" label="City Name" :rules="cityname" dense></v-text-field>
                          </v-col>
                          <v-col cols="12" sm="12" md="12"  class="p-0">
                            <v-switch label="Active" v-model="editedItem.city_status"></v-switch>
                          </v-col>
                        </v-row>
                      </v-form>
                  </v-container>
                </v-card-text>
                <v-card-actions>
                  <v-btn color="error" text @click="close">CANCEL</v-btn>
                  <v-btn color="success" :disabled="!valid" @click="editcity">UPDATE</v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
            <v-dialog v-model="dialogDelete" max-width="500px">
              <v-card>
                <v-card-title class="text-h6">Are you sure you want to delete this city?</v-card-title>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="error" text @click="closeDelete">Cancel</v-btn>
                  <v-btn color="error" text @click="deletecity">Delete</v-btn>
                  <v-spacer></v-spacer>
                </v-card-actions>
              </v-card>
            </v-dialog>
            <v-dialog v-model="dialogDelete1" max-width="500px">
              <v-card>
                <v-card-title class="text-h6">Are you sure you want to delete these cities?</v-card-title>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="error" text @click="closeDelete1">Cancel</v-btn>
                  <v-btn color="error" text @click="deleteAll">Delete</v-btn>
                  <v-spacer></v-spacer>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </template>
          <template v-slot:[`item.action`]="{ item }">
            <v-icon small class="mr-2" @click="editItem(item)">mdi-pencil </v-icon>
            <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
          </template>
          <template v-slot:[`item.city_status`]="{ item }">
            <div v-if="item.city_status === 1">
              <span>Active</span>
            </div>
            <div v-else>
              <span>Inactive</span>
            </div>
          </template>
          <template v-slot:footer>
            <div class="text-muted py-3 px-5">
              <v-btn @click="getSelectedIds" color="grey">DELETE SELECTED</v-btn>
            </div>
          </template>
        </v-data-table>
      </div>
        <v-dialog v-model="addDialog" max-width="500px">
            <v-card>
                <v-card-title style="justify-content:space-between;" class="text-h6 grey lighten-2">Add City
                    <v-icon  @click="close" class="text-h5">mdi-close</v-icon>
                </v-card-title>
                <v-card-text class="pt-3">
                    <v-form ref="form" v-model="valid">
                        <v-autocomplete @change="onChangeaddCountry"
                                        v-model="cityform.country_id"
                                        :items="countries"
                                        :rules="countryRule"
                                        item-text="country"
                                        item-value="id"
                                        label="Country name"></v-autocomplete>
                        <v-autocomplete class="my-0 py-0"
                                        v-model="cityform.stateId"
                                        label="Select state"
                                        :items="states"
                                        item-text="state"
                                        :rules="stateRule"
                                        item-value="id"></v-autocomplete>
                        <v-text-field label="City name"
                                      v-model="cityform.cityname"
                                      :rules="cityname">
                        </v-text-field>
                        <div class="my-2">
                            <v-btn color="success" class="mr-4" :disabled="!valid" @click="addCity">SAVE</v-btn>
                            <v-btn color="error" class="mr-4" @click="close">CANCEL</v-btn>
                        </div>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>
      <v-snackbar v-model="snackbar" :timeout="timeout" type="success" border="left" elevation="3">
        <template v-slot:action="{ attrs }">
          <v-btn color="blue"  v-bind="attrs" text @click="snackbar = false">Close</v-btn>
        </template>{{text}}
      </v-snackbar>
    </div>
</template>

<script>
import axios from 'axios';
export default{
    name: "AdminCity",
    data: () => ({
        countries: [],
        states: [],
        cityform:{},
        search: '',
        addDialog: false,
        dialog: false,
        dialogDelete: false,
        dialogDelete1: false,
        checkedIds: [],
        countryRule: [
            v => !!v || 'Country is required.',
        ],
        stateRule: [
            v => !!v || 'State is required.',
        ],
        cityname: [
          v => !!v || 'City Name is required.',
        ],
        headers: [
            { text: "Country", value: "country", sortable: false },
            { text: "State", value: "state", sortable: false },
            { text: "City", value: "city" },
            { text: "Status", align: 'end', value: "city_status" },
            { text: "Actions", align: 'end', value: "action", sortable: false },
        ],
        location: [],
        snackbar: false,
        text: '',
        timeout: '',
        valid: false,
        editedIndex: -1,
        editedItem: {},
    }),
    watch: {
        dialog(val) {
            val || this.close();
        },
        dialogDelete(val) {
            val || this.closeDelete();
        },
    },
    created() {
        this.getcities();
        this.getCountries();
        this.allStates();
    },
    methods: {
        getCountries() {
            axios.get("/api/admin/get/country")
                .then((resp) => {
                    this.countries = resp.data.countries;
            });
        },
        allStates() {
            axios.get("/api/allstates")
                .then((resp) => {
                    this.states = resp.data.locations;
                });
        },
        onChangeCountry() {
          var country_id = this.editedItem.country_id;
          axios.get("/api/getstate/"+country_id)
              .then((resp) => {
                this.states = resp.data;
          });
        },
        onChangeaddCountry() {
            var country_id = this.cityform.country_id;
            axios.get("/api/getstate/"+country_id)
                .then((resp) => {
                    this.states = resp.data;
                });
        },
        getcities() {
            axios.get("/api/allcities")
            .then((resp) =>{
                this.location =  resp.data.locations;
            });
        },
        addCity() {
            const config = {
                headers: { 'content-type': 'multipart/form-data' }
            }
            let data = {
                country_id:  this.cityform.country_id,
                stateId:  this.cityform.stateId,
                cityname:  this.cityform.cityname,
                city_status: 1,
            };
            axios.post("add/city",data,config)
                .then((resp) => {
                    if(resp.data.message) {
                        this.snackbar = true;
                        this.text = resp.data.message;
                        this.timeout = 3000;
                        this.$refs.form.reset();
                    }
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
        reset(){
            this.$refs.form.reset();
        },
      editcity() {
        const config = {
          headers: { 'content-type': 'multipart/form-data' }
        }
        let data = {
          id: this.editedItem.id,
          // country_id: this.editedItem.country_id,
          state_id: this.editedItem.state_id,
          cityname: this.editedItem.city,
          city_status: this.editedItem.city_status,
        };
          if (this.editedIndex > -1) { // Edit
            axios.post("/api/edit/city",data,config)
                .then((resp) => {
                  if(resp.data.message){
                    this.snackbar = true;
                    this.text = resp.data.message;
                    this.timeout = 3000;
                    this.dialog = false;
                    this.getcities();
                    this.$refs.form.reset();
                  }
                })
          }
        this.$refs.form.validate();
        },
      deletecity(){
        const config = {
          headers: { 'content-type': 'multipart/form-data' }
        }
        let data = {
          city_id: this.editedItem.id,
        };
        axios.post('/api/delete/city',data,config)
            .then((resp) =>{
              this.snackbar = true;
              this.timeout = 3000;
              this.text = resp.data.message;
              this.dialogDelete = false;
              this.getcities();
            })
            .catch(error => {
              // Handle the error
              var data = error.toJSON();
              if(data.status == 400){
                this.snackbar = true;
                this.timeout = 3000;
                this.text = "something went wrong.";
              }
            });
      },
      getSelectedIds() {
        var cityids = {
          ids:this.checkedIds.map(item => item.id)
        };
        if(cityids.ids.length > 0){
          this.dialogDelete1 = true;
        }else{
          this.snackbar = true;
          this.text = 'Please select city';
          this.timeout = 3000;
        }
      },
      deleteAll() {
        var data = {
          ids:this.checkedIds.map(item => item.id)
        };
        axios.post('/api/delete/multicities',data)
            .then((resp) => {
              if( resp.data.success == true){
                this.dialogDelete1 = false;
                this.getcities();
                this.snackbar = true;
                this.text = resp.data.message;
                this.timeout = 3000;
              }else{
                this.dialogDelete1 = false;
                this.getcities();
                this.snackbar = true;
                this.text = "Something went wrong";
                this.timeout = 3000;
              }
            });

      },
      closeDelete1() {
        this.dialogDelete1 = false;
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem);
          this.editedIndex = -1;
        });
      },
        editItem(item) {
            this.editedIndex = this.location.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialog = true;
            var country_id = this.editedItem.country_id;
            axios.get("/api/getstate/"+country_id)
                .then((resp) => {
                  this.states = resp.data;
            });
        },
        deleteItem(item) {
            this.editedIndex = this.location.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialogDelete = true;
        },
        close() {
            this.dialog = false;
            this.addDialog = false;
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem);
                this.editedIndex = -1;
            });
        },
        closeDelete() {
            this.dialogDelete = false;
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem);
                this.editedIndex = -1;
            });
        },
    },
}
</script>
