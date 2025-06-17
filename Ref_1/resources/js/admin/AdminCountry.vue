<template>
    <div>
      <div class="row">
        <div class="col-3">
          <h3 class="fw-bold">Countries</h3>
        </div>
        <div class="col-6">
          <v-text-field v-model="search" clearable append-icon="mdi-magnify" label="Search" single-line hide-details>
          </v-text-field>
        </div>
        <div class="col-3 test-decoration-none">
            <v-btn  tile color="error" large @click="addDialog = true"><v-icon left>mdi-plus</v-icon>CREATE COUNTRY</v-btn>
        </div>
      </div>
      <div class=" my-5">
        <v-data-table :search="search" :headers="headers" :items="countries"  class="elevation-1" v-model="checkedIds">
          <template v-slot:top>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-spacer></v-spacer>
            <v-dialog v-model="dialog" max-width="500px" persistent>
              <v-card>
                <v-card-title style="justify-content:space-between;" class="text-h6 grey lighten-2">Edit Country
                  <v-icon  @click="close" class="text-h5">mdi-close</v-icon>
                </v-card-title>
                <v-card-text>
                  <v-container>
                    <v-form ref="form" v-model="valid">
                      <v-row>
                        <v-col cols="12" sm="12" md="12" class="p-1">
                            <v-text-field label="Country name" v-model="editedItem.country"
                                          :rules="RuleCountryname" dense></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="12" md="12" class="p-1">
                            <v-text-field label="Short name" v-model="editedItem.sortname"
                                :rules="RuleSortname" dense>
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" sm="12" md="12" class="p-1">
                            <v-text-field label="Phone code" type="number" v-model="editedItem.phonecode"
                                :rules="RulePhonecode" dense>
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" sm="12" md="12" class="py-0 d-flex" style="justify-content: space-between;">
                            <div>
                                <v-switch label="Active" v-model="editedItem.country_status"></v-switch>
                            </div>
                            <div class="ms-3 pb-3">
                                <v-btn class="my-2 me-3" color="error" text @click="close">CANCEL</v-btn>
                                <v-btn class="my-2 me-3" color="success" :disabled="!valid" @click="editstate">UPDATE</v-btn>
                            </div>
                        </v-col>
                      </v-row>
                    </v-form>
                  </v-container>
                </v-card-text>
              </v-card>
            </v-dialog>
            <v-dialog v-model="dialogDelete" max-width="500px">
              <v-card>
                <v-card-title class="text-h6 text-center">Are you sure you want to delete this state?</v-card-title>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn class="cred" text @click="closeDelete">CANCEL</v-btn>
                  <v-btn class="cred" text @click="deleteCountry">OK</v-btn>
                  <v-spacer></v-spacer>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </template>
          <template v-slot:[`item.action`]="{ item }">
            <v-icon small class="mr-2" @click="editItem(item)"> mdi-pencil </v-icon>
            <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
          </template>
          <template v-slot:[`item.country_status`]="{ item }">
            <div v-if="item.country_status === 1">
              <span>Active</span>
            </div>
            <div v-else>
              <span>Inactive</span>
            </div>
          </template>
        </v-data-table>
      </div>
        <v-dialog v-model="addDialog" max-width="500px">
            <v-card>
                <v-card-title style="justify-content:space-between;" class="text-h6 grey lighten-2">Add Country
                    <v-icon  @click="close" class="text-h5">mdi-close</v-icon>
                </v-card-title>
                <v-card-text>
                    <v-form ref="form" v-model="valid">
                        <div class="row py-3">
                            <div class="col-12">
                                <v-text-field label="Country name" v-model="formData.country"
                                              :rules="RuleCountryname" dense>
                                </v-text-field>
                            </div>
                            <div class="col-12">
                                <v-text-field label="Short name" v-model="formData.sortname"
                                              :rules="RuleSortname" dense>
                                </v-text-field>
                            </div>
                            <div class="col-12">
                                <v-text-field label="Phone code" type="number" v-model="formData.phonecode"
                                              :rules="RulePhonecode" dense>
                                </v-text-field>
                            </div>
                            <div class="my-2">
                                <v-btn color="success" class="mr-4" :disabled="!valid" @click="addCountry">SAVE</v-btn>
                                <v-btn color="error" class="mr-4" @click="close">CANCEL</v-btn>
                            </div>
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
    name: "AdminCountry",
    data: () => ({
        search: '',
        dialog: false,
        addDialog: false,
        dialogDelete: false,
        dialogDelete1: false,
        RuleCountryname: [
            v => !!v || 'Country name is required.',
        ],
        RuleSortname: [
            v => !!v || 'Short name is required.',
        ],
        RulePhonecode: [
            v => !!v || 'Phone code is required.',
        ],
        headers: [
            { text: "S.N.", value: "id", sortable: false},
            { text: "Country-name", value: "country"},
            { text: "Short-name", value: "sortname" },
            { text: "Phone-code", value: "phonecode" },
            { text: "Status", align: 'end', value: "country_status" },
            { text: "Actions", align: 'end', value: "action", sortable: false },
        ],
        countries: [],
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
        formData:{},
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
        this.getCountries();
    },
    methods: {
        getCountries() {
            axios.get("/api/admin/get/country")
                .then((resp) => {
                    this.countries = resp.data.countries;
            });
        },
      editstate() {
        const config = {
          headers: { 'content-type': 'multipart/form-data' }
        }
        let editFormData = {
            country_id: this.editedItem.id,
            country: this.editedItem.country,
            sortname: this.editedItem.sortname,
            phonecode: this.editedItem.phonecode,
            country_status: this.editedItem.country_status,
        };
        if (this.editedIndex > -1) { // Edit
            axios.post("/api/admin/edit/country", editFormData, config)
                .then((resp) => {
                    if(resp.data.message){
                        this.snackbar = true;
                        this.text = resp.data.message;
                        this.timeout = 3000;
                        this.dialog = false;
                        this.getCountries();
                        this.$refs.form.reset();
                    }
                }
            )
        }
        this.$refs.form.validate();
      },
        addCountry() {
            const config = {
                headers: { 'content-type': 'multipart/form-data' }
            }
            axios.post("/api/admin/add/country", this.formData, config)
                .then((resp) => {
                    if(resp.data){
                        this.snackbar = true;
                        this.text = resp.data.message;
                        this.$refs.form.reset();
                        this.getCountries();
                    }else{
                        this.snackbar = true;
                        this.text = "Something went wrong!";
                    }
                    this.timeout = 3000;
                })
            this.$refs.form.validate();
        },
      deleteCountry(){
        const config = {
          headers: { 'content-type': 'multipart/form-data' }
        }
        let data = {
            country_id: this.editedItem.id,
        };
        axios.post('/api/admin/delete/country', data, config)
            .then((resp) =>{
              this.snackbar = true;
              this.timeout = 3000;
              this.text = resp.data.message;
              this.dialogDelete = false;
              this.getCountries();
            }
        )
      },
      closeDelete1() {
        this.dialogDelete1 = false;
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem);
          this.editedIndex = -1;
        });
      },

    editItem(item) {
        this.editedIndex = this.countries.indexOf(item);
        this.editedItem = Object.assign({}, item);
        this.dialog = true;
    },

    deleteItem(item) {
        this.editedIndex = this.countries.indexOf(item);
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
