<template>
    <div>
      <div class="row pb-3">
        <div class="col-12 col-md-4 py-0"><h4>Jobs</h4></div>
        <div class="col-12 col-md-6 py-0">
                <v-text-field v-model="search" append-icon="mdi-magnify" label="Search" single-line hide-details>
                </v-text-field>
              </div>
        <div class="col-12 col-md-2 test-decoration-none">
          <v-btn @click="openjobs" class="cred"  tile large>
            <v-icon left>mdi-plus</v-icon>CREATE JOBS
          </v-btn>
        </div>
      </div>
      <div class="">
              <v-data-table :search="search" :headers="headers" :items="jobs"
                            :single-select="singleSelect" show-select class="elevation-1">
                <template v-slot:top>
                  <v-dialog v-model="dialog" max-width="500px">
                    <v-card>
                      <v-card-title>
                        <span class="text-h5">{{ formTitle }}</span>
                      </v-card-title>
                      <v-card-text>
                        <v-container>
                          <v-row>
                            <v-col cols="12" sm="6" md="12">
                              <v-text-field v-model="editedItem.job_title" label="Title"></v-text-field>
                            </v-col>
                            <v-col cols="12" md="12" class="py-0">
                              <v-select v-model="editedItem.job_type" :items="jobtype" item-text="job_type" label="Select Job Type"></v-select>
                            </v-col>
                            <v-col cols="12" md="12" class="py-0">
                              <v-select v-model="editedItem.qualification" :items="qualification" item-text="qualification" label="Select Qualifications"></v-select>
                            </v-col>
                            <v-col cols="12" md="12" class="py-0">
                              <div class="range-slider sec1 bg-white">
                                  <v-text-field label="Salary" v-model="editedItem.salary_from" class="me-2 text-center"></v-text-field>
                              </div>
                            </v-col>
                            <v-col cols="12" md="12" class="py-0">
                              <div class="range-slider sec1 bg-white">
                                  <v-text-field label="Experience" v-model="editedItem.min_exp" class="me-2 text-center"></v-text-field>
                              </div>
                            </v-col>
                          </v-row>
                        </v-container>
                      </v-card-text>
                      <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn class="cred" text @click="close">
                          CANCEL
                        </v-btn>
                        <v-btn class="cred" text @click="save">
                          UPDATE
                        </v-btn>
                      </v-card-actions>
                    </v-card>
                  </v-dialog>
                  <v-dialog v-model="dialogDelete" max-width="500px">
                    <v-card>
                      <v-card-title class="text-h5">Are you sure you want to delete this
                        item?</v-card-title>
                      <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" text @click="closeDelete">CANCEL</v-btn>
                        <v-btn color="blue darken-1" text @click="deleteItemConfirm">OK</v-btn>
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
                <template v-slot:[`item.job_status`]>
                    <v-icon small class="mr-2">
                      mdi-check
                    </v-icon>
                </template>
              </v-data-table>
            </div>
      <v-dialog v-model="showModel" persistent max-width="900">
        <v-card>
          <v-card-title class="text-h5 grey lighten-2">
            Add Jobs
          </v-card-title>
          <v-card-text>
            <v-row>
              <v-col cols="12" md="12" class="py-0">
                <v-autocomplete :items="jobcats" item-text="job_cat_name" label="Select Job Category"></v-autocomplete>
              </v-col>
              <v-col cols="12" md="12" class="py-0">
                <v-text-field label="Select Job Title"></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="py-0">
                <v-select :items="jobtype" item-text="job_type" label="Select Job Type"></v-select>
              </v-col>
              <v-col cols="12" md="12" class="py-0">
                <v-select :items="qualification"  item-text="qualification" label="Select Qualifications"></v-select>
              </v-col>
              <v-col cols="12" md="6" class="py-0">
                <v-text-field label="Salary Start From"></v-text-field>
              </v-col>
              <v-col cols="12" md="6" class="py-0">
                <v-text-field label="Experience"></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="py-0">
                <v-textarea rows="3" row-height="20"  label="Enter Job Description"></v-textarea>
              </v-col>
            </v-row>
          </v-card-text>
          <div class="text-end">
            <v-btn class="my-2 me-3 cred">UPDATE</v-btn>
            <v-btn @click="showModel = false" class="my-2 me-3 cred">CLOSE</v-btn>
          </div>
        </v-card>
      </v-dialog>
    </div>
  </template>
  <script>
  import axios from "axios";
  
  export default {
    name: 'BusinessJobs',
    components: {},
    metaInfo: {title: 'All Jobs'},
    data: () => ({
      inputnumber: '',
      search: '',
      showModel: '',
      singleSelect: '',
      dialog: false,
      dialogDelete: false,
      jobs:[],
      jobtype: [],
      qualification: [],
      jobcats:[],
      headers: [
        { text: "Title", value: "job_title", sortable: false },
        { text: "Salary", value: "salary_from", sortable: false },
        { text: "Job Type", value: "job_type", sortable: false },
        { text: "Qualifications", value: "qualification", sortable: false },
        { text: "Experience", value: "min_exp", sortable: false },
        { text: "Active", align: 'end', value: "job_status", sortable: false },
        { text: "Actions", align: 'end', value: "action", sortable: false },
      ],
      editedIndex: -1,
      editedItem: {
        job_title: "",
        salary_from: "",
        job_type: "",
        qualification: "",
        min_exp: "",
      },
    }),
    created() {
      this.getJobsList();
      this.getJobtype();
      this.getQualification();
      this.getJobcategory();
    },
    computed: {
      formTitle() {
        return this.editedIndex === -1 ? '' : 'Edit Jobs'
      },
    },
  
    methods: {
      openjobs() {
        this.showModel = true;
      },
      onlynumber(e){
       e.target.value = e.target.value.replace(/[^0-9]/g, '');
        // this.inputnumber = this.inputnumber.replace(/[^0-9]/g, '');
      },
      getJobsList(){
        axios.get('/api/business/jobs/list')
            .then((resp)=>{
              this.jobs = resp.data.jobs;
            })
      },
      getJobtype(){
        axios.get('/api/jobtype')
            .then((resp)=>{
              this.jobtype = resp.data.jobtypes;
            })
      },
      getQualification(){
        axios.get('/api/qualification')
            .then((resp)=>{
              this.qualification = resp.data.qualifications;
            })
      },
      getJobcategory(){
        axios.get('/api/jobcategory')
            .then((resp)=>{
              this.jobcats = resp.data.jobcategories;
            })
      },
      editItem(item) {
        this.editedIndex = this.jobs.indexOf(item);
        this.editedItem = Object.assign({}, item);
        this.dialog = true;
      },
  
      deleteItem(item) {
        this.editedIndex = this.jobs.indexOf(item);
        this.editedItem = Object.assign({}, item);
        this.dialogDelete = true;
      },
  
      deleteItemConfirm() {
        this.jobs.splice(this.editedIndex, 1);
        this.closeDelete();
      },
  
      close() {
        this.dialog = false;
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
  
      save() {
        if (this.editedIndex > -1) {
          Object.assign(this.jobs[this.editedIndex], this.editedItem);
        } else {
          this.jobs.push(this.editedItem);
        }
        this.close();
      },
    },
  
  }
  </script>