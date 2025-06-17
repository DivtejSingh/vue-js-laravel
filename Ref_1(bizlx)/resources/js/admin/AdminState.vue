<template>
    <div>
      <div class="row">
        <div class="col-3">
          <h3 class="fw-bold">State</h3>
        </div>
        <div class="col-6">
          <v-text-field v-model="search" clearable append-icon="mdi-magnify" label="Search" single-line hide-details>
          </v-text-field>
        </div>
        <div class="col-3 test-decoration-none">
            <v-btn  tile color="error" large @click="addDialog = true">
              <v-icon left>mdi-plus</v-icon>CREATE STATE
            </v-btn>
        </div>
      </div>
      <div class=" my-5">
        <v-data-table :search="search" :headers="headers" :items="locations" show-select class="elevation-1" v-model="checkedIds" multi-select>
          <template v-slot:top>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-spacer></v-spacer>
            <v-dialog v-model="dialog" max-width="500px" persistent>
              <v-card>
                <v-card-title style="justify-content:space-between;" class="text-h6 grey lighten-2">Edit State
                  <v-icon  @click="close" class="text-h5">mdi-close</v-icon>
                </v-card-title>
                <v-card-text>
                  <v-container>
                    <v-form ref="form" v-model="valid">
                      <v-row>
                        <v-col cols="12" sm="12" md="12" class="p-1">
                          <v-select
                              v-model="editedItem.country_id"
                              :items="countries"
                              :rules="country"
                              item-text="country"
                              item-value="id"
                              label="Country name"></v-select>
                        </v-col>
                        <v-col cols="12" sm="12" md="12" class="p-1">
                          <v-text-field v-model="editedItem.state" :rules="statename" label="State"></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="12" md="12" class="p-0">
                          <v-switch label="Active" v-model="editedItem.state_status"></v-switch>
                        </v-col>
                      </v-row>
                    </v-form>
                  </v-container>
                </v-card-text>

                <v-card-actions>
                  <v-btn color="error" text @click="close">Cancel</v-btn>
                  <v-btn color="success" :disabled="!valid" @click="editstate">Update</v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
            <v-dialog v-model="dialogDelete" max-width="500px">
              <v-card>
                <v-card-title class="text-h6">Are you sure you want to delete this
                  state?</v-card-title>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn class="cred" text @click="closeDelete">Cancel</v-btn>
                  <v-btn class="cred" text @click="deletestate">OK</v-btn>
                  <v-spacer></v-spacer>
                </v-card-actions>
              </v-card>
            </v-dialog>
            <v-dialog v-model="dialogDelete1" max-width="500px">
              <v-card>
                <v-card-title class="text-h6">Are you sure you want to delete these
                  states?</v-card-title>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn class="cred" text @click="closeDelete1">Cancel</v-btn>
                  <v-btn class="cred" text @click="deleteAll">OK</v-btn>
                  <v-spacer></v-spacer>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </template>
          <template v-slot:[`item.action`]="{ item }">
            <v-icon small class="mr-2" @click="editItem(item)">
              mdi-pencil
            </v-icon>
            <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
          </template>
          <template v-slot:[`item.state_status`]="{ item }">
            <div v-if="item.state_status === 1">
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
                <v-card-title style="justify-content:space-between;" class="text-h6 grey lighten-2">Add State
                    <v-icon  @click="close" class="text-h5">mdi-close</v-icon>
                </v-card-title>
                <v-card-text class="pt-5">
                    <v-form ref="form" v-model="valid">
                        <v-select
                            v-model="stateform.country_id"
                            :items="countries"
                            :rules="country"
                            item-text="country"
                            item-value="id"
                            label="Country name"
                            prepend-inner-icon="mdi-home-city"></v-select>
                        <v-text-field
                            label="State Name"
                            v-model="stateform.statename"
                            :rules="statename"></v-text-field>
                        <div class="my-2">
                            <v-btn color="success" class="mr-4" :disabled="!valid" @click="addState">SAVE</v-btn>
                            <v-btn color="error" class="mr-4" @click="close">CANCEL</v-btn>
                        </div>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>
      <v-snackbar v-model="snackbar" :timeout="timeout" type="success" border="left" elevation="3">
        <template v-slot:action="{ attrs }">
          <v-btn color="blue"  v-bind="attrs" text @click="snackbar = false">Close</v-btn>
        </template>
        {{text}}
      </v-snackbar>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    name: "StateView",
    components: {  },
    data: () => ({
        search: '',
        dialog: false,
        addDialog: false,
        dialogDelete: false,
        dialogDelete1: false,
        countries: [],
        statename: [
          v => !!v || 'State Name is required.',
        ],
        country: [
            v => !!v || 'Country name is required.',
        ],
        headers: [
            { text: "Country", value: "country", sortable: false },
            { text: "State", value: "state" },
            { text: "Status", align: 'end', value: "state_status" },
            { text: "Actions", align: 'end', value: "action", sortable: false },
        ],
        locations: [],
        checkedIds: [],
        snackbar: false,
        text: '',
        timeout: '',
        valid: false,
        editedIndex: -1,
        editedItem: {
            state: "",
            state_status: "",
        },
        stateform:{},
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
        this.getState();
        this.getCountries();
    },

    methods: {
        getCountries() {
            axios.get("/api/admin/get/country")
                .then((resp) => {
                    this.countries = resp.data.countries;
            });
        },
        getState() {
            axios.get("/api/allstates")
              .then((resp) => {
                  this.locations = resp.data.locations;
              });
        },
      editstate() {
        const config = {
          headers: { 'content-type': 'multipart/form-data' }
        }
        let data = {
          id: this.editedItem.id,
          country_id : this.editedItem.country_id ,
          statename: this.editedItem.state,
          state_status: this.editedItem.state_status,
        };
        if (this.editedIndex > -1) { // Edit
          axios.post("/api/edit/state",data,config)
              .then((resp) => {
                if(resp.data.message){
                  this.snackbar = true;
                  this.text = resp.data.message;
                  this.timeout = 3000;
                  this.dialog = false;
                  this.getState();
                  this.$refs.form.reset();
                }
              })
        }
        this.$refs.form.validate();
      },
        addState() {
            const config = {
                headers: { 'content-type': 'multipart/form-data' }
            }
            let data = {
                countryId:  this.stateform.country_id,
                statename:  this.stateform.statename,
                state_status: 1,
            };
            axios.post("add/state",data,config)
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
      deletestate(){
        const config = {
          headers: { 'content-type': 'multipart/form-data' }
        }
        let data = {
          state_id: this.editedItem.id,
        };
        axios.post('/api/delete/state',data,config)
            .then((resp) =>{
              this.snackbar = true;
              this.timeout = 3000;
              this.text = resp.data.message;
              this.dialogDelete = false;
              this.getState();
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
        var stateids = {
          ids:this.checkedIds.map(item => item.id)
        };
        if(stateids.ids.length > 0){
          this.dialogDelete1 = true;
        }else{
          this.snackbar = true;
          this.text = 'Please select state';
          this.timeout = 3000;
        }
      },
      deleteAll() {
        var data = {
          ids:this.checkedIds.map(item => item.id)
        };
        axios.post('/api/delete/multistates',data)
            .then((resp) => {
              if( resp.data.success == true){
                this.dialogDelete1 = false;
                this.getState();
                this.snackbar = true;
                this.text = resp.data.message;
                this.timeout = 3000;
              }else{
                this.dialogDelete1 = false;
                this.getState();
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
        this.editedIndex = this.locations.indexOf(item);
        this.editedItem = Object.assign({}, item);
        this.dialog = true;
    },

    deleteItem(item) {
        this.editedIndex = this.locations.indexOf(item);
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
}
}
</script>
