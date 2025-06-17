<template>
  <div>
    <div class="row">
      <div class="col-12 col-md-3"><h4>Job Qualification</h4></div>
      <div class="col-12 col-md-6">
        <v-text-field v-model="search" clearable append-icon="mdi-magnify" label="Search" single-line hide-details></v-text-field>
      </div>
      <div class="col-12 col-md-3">
        <v-btn @click="openModel" tile color="error" large>
          <v-icon left>mdi-plus</v-icon>
          ADD NEW JOB QUALIFICATION
        </v-btn>
      </div>
    </div>
    <div class="row mt-10">
        <v-col cols="12">
            <v-data-table :headers="headers" :search="search" :items="qualification" class="elevation-1">
                <template v-slot:top>
                    <v-dialog v-model="dialog" persistent max-width="500px">
                        <v-card>
                            <v-card-title style="justify-content:space-between;" class="text-h6 grey lighten-2">
                                Edit Job Qualification
                                <v-icon  @click="close"  class="text-h5">mdi-close</v-icon>
                            </v-card-title>
                            <v-card-text class="p-0">
                                <v-container>
                                    <v-form ref="form" v-model="valid">
                                        <v-row>
                                            <v-col cols="12" md="12">
                                                <v-text-field dense v-model="editedItem.qualification" :rules="jobqualification" label="Job Type"></v-text-field>
                                                <div>
                                                    <v-checkbox dense v-model="editedItem.qualification_status" label="Active"></v-checkbox>
                                                </div>
                                                <v-card-actions>
                                                    <v-btn :disabled="!valid" @click="editjobqualification" class="my-4 me-3" color="success">UPDATE</v-btn>
                                                    <v-btn @click="close" class="my-4 me-3" color="error">Cancel</v-btn>
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
                            <v-card-title class="text-h6">Are you sure you want to delete this item?</v-card-title>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="error" text @click="closeDelete">Cancel</v-btn>
                                <v-btn color="error" text @click="deletejobqualification">Delete</v-btn>
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
                <template v-slot:[`item.qualification_status`]="{ item }">
                    <div v-if="item.qualification_status === 1">
                        <span>Active</span>
                    </div>
                    <div v-else>
                        <span>Inactive</span>
                    </div>
                </template>
            </v-data-table>
        </v-col>
    </div>
    <v-dialog v-model="showModel" persistent max-width="500px">
      <v-card>
        <v-card-title style="justify-content:space-between;" class="text-h6 grey lighten-2">
          Add New Job Qualification
          <v-icon  @click="showModel = false"  class="text-h5">mdi-close</v-icon>
        </v-card-title>
        <v-card-text>
          <v-form ref="form" v-model="valid">
            <v-row>
              <v-col cols="12" md="12">
                <v-text-field dense v-model="jobqualifications" :rules="jobqualification" label="Job Type"></v-text-field>
                <div>
                  <v-btn class="my-4 me-3" color="sucess" :disabled="!valid" @click="addjobqualification">SAVE</v-btn>
                  <v-btn @click="showModel = false" class="my-4 me-3" color="error">CLOSE</v-btn>
                </div>
              </v-col>
            </v-row>
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
import axios from "axios";

export default {
  name: "JobQualification",
  components: {},
  data: () => ({
    url:'https://bizlx.s3.ap-south-1.amazonaws.com',
    search: '',
    jobqualifications: '',
    valid: false,
    snackbar: false,
    showModel: false,
    text: '',
    timeout: -1,
    dialog: false,
    dialogDelete: false,
    jobqualification: [
      v => !!v || 'Job Type Name is required.',
    ],
    headers: [
      { text: 'Job Qualification', value: 'qualification'},
      { text: "Active", align: 'end', value: "qualification_status", sortable: false },
      { text: 'Actions', align: 'end', value: 'actions', sortable: false},
    ],
    qualification: [],
    editedIndex: -1,
    editedItem: {
      qualification: '',
      qualification_status: '',
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
    this.getjobqualification();
  },

  methods: {
    getjobqualification() {
      axios.get('/api/admin/job/qualification')
          .then((resp)=>{
            this.qualification = resp.data.jobqualification;
          });
    },
    addjobqualification() {
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let data = {
        qualification:  this.jobqualifications,
        qualification_status: 1,
      };
      axios.post('/api/admin/add/job/qualification',data, config)
          .then((resp)=>{
              this.snackbar = true;
              this.text = resp.data.message;
              this.timeout = 3000;
              this.showModel = false;
              this.getjobqualification();
              this.$refs.form.reset();
              if(resp.data.error){
                if(resp.data.error.qualification){
                  this.snackbar = true;
                  this.text = 'The job qualification has already been taken.';
                }
              }
          })
      this.$refs.form.validate();
    },
    editjobqualification() {
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let data = {
        qualification: this.editedItem.qualification,
        qualification_status: this.editedItem.qualification_status,
      };
      if (this.editedIndex > -1) {
        axios.post('/api/admin/edit/job/qualification/'+this.editedItem.id, data, config)
            .then((resp) => {
              if (resp.data.message) {
                this.snackbar = true;
                this.text = resp.data.message;
                this.timeout = 3000;
                this.dialog = false;
                this.getjobqualification();
                this.$refs.form.reset();
              }
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
      }
      this.$refs.form.validate();
    },
    deletejobqualification() {
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let data = {
        qualification_id: this.editedItem.id,
      };
      axios.post('/api/admin/delete/job/qualification',data,config)
          .then((resp) =>{
            this.snackbar = true;
            this.timeout = 3000;
            this.text = resp.data.message;
            this.dialogDelete = false;
            this.getjobqualification();
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
      this.editedIndex = this.qualification.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },

    deleteItem(item) {
      this.editedIndex = this.qualification.indexOf(item)
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
  }
}
</script>
