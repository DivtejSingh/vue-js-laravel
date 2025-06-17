<template>
    <div>
        <div class="row pb-3">
          <div class="col-12 col-sm-4">
            <v-text-field v-model="search3" append-icon="mdi-magnify" label="Search" single-line hide-details>
            </v-text-field>
          </div>
          <div class="col-12 col-sm-4 test-decoration-none">
            <div v-if="jobscount === userDta.jobs">
              <v-btn @click="MessageModel4 = true" color="error"  tile large>
                <v-icon left>mdi-plus</v-icon>CREATE JOB
              </v-btn>
            </div>
            <div v-else>
              <v-btn @click="openjobs" color="error" tile large>
                <v-icon left>mdi-plus</v-icon>CREATE JOB
              </v-btn>
            </div>
          </div>
        </div>
        <div class="">
          <v-data-table :search="search3" :headers="jobsHeaders" :items="itemsWithSno" class="elevation-1">
            <template v-slot:top>
              <v-dialog v-model="dialog3" max-width="900px">
                <v-card>
                  <v-form @submit.prevent="jobAddFormSubmit" ref="form" v-model="valid" lazy-validation>
                      <v-card-title class="text-h5 grey lighten-2" style="justify-content:space-between;">{{ formTitle3 }}
                        <v-icon  @click="close" class="text-h5">mdi-close</v-icon>
                      </v-card-title>
                      <v-card-text>
                        <v-row>
                          <v-col cols="12" md="12" class="py-0">
                                <v-text-field
                                    label="Job Title"
                                    v-model="editedItem.job_title"
                                    :rules="job_titleRules"
                                    :counter="100">
                                </v-text-field>
                          </v-col>
                          <v-col cols="12" md="6" class="py-0">
                                <v-autocomplete
                                    label="Select Job Category"
                                    v-model="editedItem.job_cat_id"
                                    :rules="job_cat_idRules"
                                    :items="jobcategories"
                                    item-text="job_cat_name"
                                    item-value="id">
                                </v-autocomplete>
                          </v-col>
                          <v-col cols="12" md="6" class="py-0">
                                <v-autocomplete
                                    label="Select Job Type"
                                    v-model="editedItem.job_type_id"
                                    :rules="job_type_idRules"
                                    :items="jobtypes"
                                    item-text="job_type"
                                    item-value="id">
                                </v-autocomplete>
                          </v-col>
                          <v-col cols="12" md="6" class="py-0">
                                <v-select
                                    label="Select Qualifications"
                                    v-model="editedItem.job_qualification_id"
                                    :rules="job_qualification_idRules"
                                    :items="qualifications"
                                    item-text="qualification"
                                    item-value="id">
                                </v-select>
                          </v-col>
                          <v-col cols="12" md="6" class="py-0">
                                  <v-autocomplete
                                    label="Select City"
                                    v-model="editedItem.city_id"
                                    :rules="city_idRules"
                                    :items="cities"
                                    item-text="city"
                                    item-value="id" readonly disabled>
                                  </v-autocomplete>
                          </v-col>
                          <v-col cols="12" md="6" class="py-0">
                                <v-text-field
                                    type="number"
                                    label="Salary Start From"
                                    v-model="editedItem.salary_from"
                                    :rules="salary_fromRules">
                                </v-text-field>
                          </v-col>
                          <v-col cols="12" md="6" class="py-0">
                                <v-autocomplete
                                    label="Experience"
                                    v-model="editedItem.min_exp"
                                    :rules="min_expRules"
                                    :items="experience"
                                    item-text="text"
                                    item-value="id">
                                </v-autocomplete>
                          </v-col>
                          <v-col cols="12" md="12" class="py-0">
                                <v-textarea
                                    rows="3"
                                    row-height="20"
                                    label="Enter Job Description"
                                    v-model="editedItem.job_description"
                                    :rules="job_descriptionRules">
                                </v-textarea>
                          </v-col>
                          <v-col cols="12" md="12" class="py-0 d-flex" style="justify-content: space-between;">
                              <div>
                                <v-checkbox v-model="editedItem.job_status" label="Active"></v-checkbox>
                              </div>
                              <div class="ms-3 pb-3">
                                <v-btn v-if="isLoading" class="my-2 me-3 cred">Loading...</v-btn>
                                <v-btn v-else class="my-2 me-3" color="success" type="submit" :disabled="!valid">UPDATE</v-btn>
                                <v-btn color="error" class="my-2 me-3" @click="close">CANCEL</v-btn>
                              </div>
                          </v-col>
                        </v-row>
                      </v-card-text>
                  </v-form>
                </v-card>
              </v-dialog>
              <v-dialog v-model="dialogDelete" max-width="500px">
                <v-card>
                  <v-card-title class="text-h5">Are you sure you want to delete this item?</v-card-title>
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="closeDelete">CANCEL</v-btn>
                    <v-btn color="blue darken-1" text @click="deleteItemConfirm">OK</v-btn>
                    <v-spacer></v-spacer>
                  </v-card-actions>
                </v-card>
              </v-dialog>
            </template>
            <template v-slot:[`item.min_exp`]="{ item }">
                <div v-if="item.min_exp==6" class="mr-2">5 + years</div>
                <div v-if="(item.min_exp >= 1) && (item.min_exp <=5)" class="mr-2">{{ item.min_exp }} years</div>
                <div v-if="item.min_exp==0" class="mr-2">Fresher</div>
            </template>
            <template v-slot:[`item.description`]="{ item }">
                <div>{{ item.job_description.slice(0, descMaxLength)+'...' }}</div>
            </template>
            <template v-slot:[`item.eye`]="{ item }">
              <a :href= "'/jobs/detail/'+item.job_slug" target="_blank" class="link-dark">
                <v-icon small class="mr-2">mdi-eye </v-icon>
              </a>
            </template>
            <template v-slot:[`item.action3`]="{ item }">
              <v-icon small class="mr-2" @click="editItem(item)">mdi-pencil</v-icon>
              <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
            </template>

            <template v-slot:[`item.active3`]="{ item }">
                <div v-if="item.job_status === 1">
                    <span>Active</span>
                </div>
                <div v-else>
                  <span>Inactive</span>
                </div>
            </template>

          </v-data-table>
        </div>
    <v-dialog v-model="showModel3" persistent max-width="900">
      <v-card>
        <v-form @submit.prevent="jobAddFormSubmit" ref="form" v-model="valid" lazy-validation>
            <v-card-title class="text-h5 grey lighten-2 d-flex" style="justify-content:space-between;">{{ formTitle3 }}
                <v-icon @click="showModel3 = false" class="text-h5">mdi-close</v-icon>
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12" md="12" class="py-0">
                      <v-text-field
                          label="Job Title"
                          v-model="formData.job_title"
                          :rules="job_titleRules"
                          :counter="100">
                      </v-text-field>
                </v-col>
                <v-col cols="12" md="6" class="py-0">
                      <v-autocomplete
                          label="Select Job Category"
                          v-model="formData.job_cat_id"
                          :rules="job_cat_idRules"
                          :items="jobcategories"
                          item-text="job_cat_name"
                          item-value="id">
                      </v-autocomplete>
                </v-col>
                <v-col cols="12" md="6" class="py-0">
                      <v-autocomplete
                          label="Select Job Type"
                          v-model="formData.job_type_id"
                          :rules="job_type_idRules"
                          :items="jobtypes"
                          item-text="job_type"
                          item-value="id">
                      </v-autocomplete>
                </v-col>
                <v-col cols="12" md="6" class="py-0">
                      <v-select
                          label="Select Qualifications"
                          v-model="formData.job_qualification_id"
                          :rules="job_qualification_idRules"
                          :items="qualifications"
                          item-text="qualification"
                          item-value="id">
                      </v-select>
                </v-col>
                <v-col cols="12" md="6" class="py-0 d-none">
                        <v-autocomplete
                          label="Select City"
                          v-model="formData.city_id"
                          :items="cities"
                          item-text="city"
                          item-value="id">
                        </v-autocomplete>
                </v-col>
                <v-col cols="12" md="6" class="py-0">
                      <v-text-field
                          type="number"
                          label="Salary Start From"
                          v-model="formData.salary_from"
                          :rules="salary_fromRules">
                      </v-text-field>
                </v-col>
                <v-col cols="12" md="6" class="py-0">
                      <v-autocomplete
                          label="Experience"
                          v-model="formData.min_exp"
                          :rules="min_expRules"
                          :items="experience"
                          item-text="text"
                          item-value="id"
                          >
                      </v-autocomplete>
                </v-col>
                <v-col cols="12" md="12" class="py-0">
                      <v-textarea
                          rows="3"
                          row-height="20"
                          label="Enter Job Description"
                          v-model="formData.job_description"
                          :rules="job_descriptionRules">
                      </v-textarea>
                </v-col>
              </v-row>
            </v-card-text>
            <div class="ms-3 pb-3">
              <v-btn v-if="isLoading" class="my-2 me-3 cred">Loading...</v-btn>
              <v-btn v-else class="my-2 me-3" color="success" type="submit" :disabled="!valid">SAVE</v-btn>
              <v-btn @click="showModel3 = false" class="my-2 me-3" color="error">CLOSE</v-btn>
            </div>
        </v-form>
      </v-card>
    </v-dialog>

    <v-dialog v-model="MessageModel3" persistent max-width="500">
      <v-card>
        <v-col cols="12" md="12" class="py-0 d-flex" style="justify-content: space-between;">
          <div class="mt-2 text-h6">{{ text }}</div>
          <div>
              <v-btn @click="closeDelete" class="my-2"><v-icon>mdi-close</v-icon></v-btn>
          </div>
        </v-col>
      </v-card>
    </v-dialog>
      <v-dialog v-model="MessageModel4" persistent max-width="500">
      <v-card>
        <v-col cols="12" md="12" class="d-flex" style="justify-content: space-between;">
          <div class="mt-1 text-h6">Your present plan has limit of {{userDta.jobs}} jobs</div>
          <div @click="closeDelete" class="my-2">
              <v-icon>mdi-close</v-icon>
          </div>
        </v-col>
      </v-card>
    </v-dialog>
    </div>
</template>

<script>
import axios from 'axios';
export default{
    name: 'JobsView',
    metaInfo: {title: 'Jobs'},
    data: () => ({
      url:'https://bizlx.s3.ap-south-1.amazonaws.com',
      descMaxLength: 20,
      jobs: [],
      jobscount: '',
      userDta: [],
      singleSelect3: '',
      search3: '',
      MessageModel3: false,
      MessageModel4: false,
      dialogDelete: false,
      showModel3: false,
      dialog3: false,
      experience: [
              {'id':"0", 'text':"Fresher"},
              {'id':1, 'text':"1 years"},
              {'id':2, 'text':"2 years"},
              {'id':3, 'text':"3 years"},
              {'id':4, 'text':"4 years"},
              {'id':5, 'text':"5 years"},
              {'id':6, 'text':"5 + years"},
            ],
      jobsHeaders: [
        { text: "Serial #", value: 'sno', sortable: false },
        { text: "Title", value: "job_title", sortable: false },
        { text: "Salary", value: "salary_from", sortable: false },
        { text: "Job Type", value: "job_type", sortable: false },
        { text: "Qualifications", value: "qualification", sortable: false },
        { text: "Experience", value: "min_exp", sortable: false },
        { text: "Description", value: "description", sortable: false },
        { text: "Status", align: 'end', value: "active3", sortable: false },
        { text: "View Link", align: 'end', value: "eye", sortable: false },
        { text: "Actions", align: 'end', value: "action3", sortable: false },
      ],
      jobcategories: [],
      jobtypes: [],
      cities:[],
      qualifications:[],
      valid: false,
      formData:{
        job_title: '',
        job_cat_id: '',
        job_type_id: '',
        job_qualification_id: '',
        city_id: '',
        salary_from: '',
        min_exp: '',
        job_description: '',
        job_status: 1,
      },
      job_titleRules: [
        v => !!v || 'Job title is required.',
        v => v.length >= 8 || 'Job title must be greater than 8 characters.',
      ],
      job_cat_idRules: [
          v => !!v || 'Job category is required.',
      ],
      job_type_idRules: [
          v => !!v || 'Job type is required.',
      ],
      job_qualification_idRules: [
          v => !!v || 'Job qualification is required.',
      ],
      city_idRules: [
          v => !!v || 'City is required.',
      ],
      salary_fromRules: [
          v => !!v || 'Salary is required.',
      ],
      min_expRules: [
          v => !!v || 'Experience is required.',
      ],
      job_descriptionRules: [
          v => !!v || 'Description is required.',
      ],
      editedIndex: -1,
      editedItem: {},
      defaultItem: {
          title: '',
          salary: '',
          job_type: '',
          qualifications: '',
          experience: '',
      },
      text: '',
      isLoading: false,

    }),
  created() {
    this.jobCategory();
    this.jobTypes();
    this.getCities();
    this.getQualifications();

    this.user_id();
    this.userdata = this.user_id();
    if(this.userdata.id != null){
      this.getjobsByReseller();
    }else{
      this.getjobs();
    }
  },
  computed: {
    itemsWithSno() {
      return this.jobs.map((d, index) => ({ ...d, sno: index + 1 }))
    },
    formTitle3() {
      return this.editedIndex === -1 ? 'Add Job' : 'Edit Job'
    },
  },
  methods:{
    user_id() {
      return this.$store.state.userId;
    },
    openjobs() {
      this.showModel3 = true;
      this.$refs.form.reset();
    },
    jobCategory() {
        axios.get("/api/jobcategory")
          .then((resp) =>{
              this.jobcategories = resp.data.jobcategories;
            }
          );
    },
    jobTypes() {
        axios.get("/api/jobtypes")
          .then((resp) =>{
              this.jobtypes = resp.data.jobtypes;
            }
          );
    },
    getCities() {
        axios.get("/api/getcities")
          .then((resp) =>{
              this.cities = resp.data.cities;
            }
          );
    },
    getQualifications() {
      axios.get("/api/qualifications")
      .then((resp) =>{
          this.qualifications = resp.data.qualifications;
        }
      );
    },
    getjobsByReseller() { // get for reseller
      axios.post("/api/businesses/jobs/getbyReseller", {userId:this.userdata.id})
        .then((resp) =>{
            this.jobs = resp.data.jobs;
            this.jobscount = resp.data.jobs.length;
            this.userDta = resp.data.user;
          }
        );
    },
    getjobs() { // get business
      axios.get("/api/businesses/jobs/get")
        .then((resp) =>{
            this.jobs = resp.data.jobs;
            this.jobscount = resp.data.jobs.length;
            this.userDta = resp.data.user;
          }
        );
    },
    editItem(item) {
      this.editedIndex = this.itemsWithSno.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog3 = true;
    },
    deleteItem(item) {
      this.editedIndex = this.itemsWithSno.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },
    deleteItemConfirm() {
      let data = {
          job_id: this.editedItem.job_id,
      };
      if(this.userdata.id != null){
        axios.post("/api/delete/jobbyReseller", data)
          .then((resp) => {
            this.closeDelete();
                this.MessageModel3 = true;
                this.text = resp.data.message;
            this.getjobsByReseller();
          }
        );
      }else{
        axios.post("/api/delete/job", data)
          .then((resp) => {
            this.closeDelete();
                this.MessageModel3 = true;
                this.text = resp.data.message;
            this.getjobs();
        });
      }
    },
    close() {
      this.dialog3 = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
        this.$refs.form.reset();
      });
    },
    closeDelete() {
      this.dialogDelete = false;
      this.MessageModel3 = false;
      this.MessageModel4 = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },
    jobAddFormSubmit() {
      if (this.editedIndex > -1) { // edit deals
          const config = {
              headers: { 'content-type': 'multipart/form-data' }
          }
          let data = {
              user_id: this.userDta.user_id,
              job_id: this.editedItem.job_id,
              job_title: this.editedItem.job_title,
              job_cat_id: this.editedItem.job_cat_id,
              job_type_id: this.editedItem.job_type_id,
              job_qualification_id: this.editedItem.job_qualification_id,
              city_id: this.editedItem.city_id,
              salary_from: this.editedItem.salary_from,
              min_exp: this.editedItem.min_exp,
              job_description: this.editedItem.job_description,
              job_status: this.editedItem.job_status,
          };
          if(this.editedItem.job_id && this.editedItem.job_title!='' && this.editedItem.job_cat_id!='' && this.editedItem.job_type_id!='' && this.editedItem.job_qualification_id!='' &&
            this.editedItem.city_id!='' && this.editedItem.salary_from!='', this.editedItem.min_exp, this.editedItem.job_description){
              this.isLoading = true;
              if(this.userdata.id != null){
                axios.post("/api/update/jobbyReseller", data, config)
                    .then((resp) => {
                      this.close();
                        this.isLoading = false;
                        this.MessageModel3 = true;
                        this.text = resp.data.message;
                      this.getjobsByReseller();
                      this.$refs.form.reset();
                    }
                );
              }else{
                axios.post("/api/update/job", data, config)
                    .then((resp) => {
                      this.close();
                        this.isLoading = false;
                        this.MessageModel3 = true;
                        this.text = resp.data.message;
                      this.getjobs();
                      this.$refs.form.reset();
                    }
                );
              }
          }
        Object.assign(this.hotdeals[this.editedIndex], this.editedItem);
      } else { // Add new job
        const config = {
            headers: { 'content-type': 'multipart/form-data' }
        }
        let data = {
            user_id: this.userDta.user_id,
            plan_id: this.userDta.plan_id,
            job_title: this.formData.job_title,
            job_cat_id: this.formData.job_cat_id,
            job_type_id: this.formData.job_type_id,
            job_qualification_id: this.formData.job_qualification_id,
            city_id: this.formData.city_id,
            salary_from: this.formData.salary_from,
            min_exp: this.formData.min_exp,
            job_description: this.formData.job_description,
            job_status: 1,
        };
        if(this.formData.job_title!='' && this.formData.job_cat_id!='' && this.formData.job_type_id!='' && this.formData.job_qualification_id!='' &&
        this.formData.city_id!='' && this.formData.salary_from!='', this.formData.min_exp, this.formData.job_description){
          this.isLoading = true;
          if(this.userdata.id != null){
            axios.post("/api/add/jobbyReseller", data, config)
              .then((resp) => {
                  this.showModel3 = false;
                    this.isLoading = false;
                    this.MessageModel3 = true;
                    this.text = resp.data.message;
                  this.getjobsByReseller();
                  this.$refs.form.reset();
            });
          }else{
            axios.post("/api/add/job", data, config)
              .then((resp) => {
                  this.showModel3 = false;
                    this.isLoading = false;
                    this.MessageModel3 = true;
                    this.text = resp.data.message;
                  this.getjobs();
                  this.$refs.form.reset();
            });
          }
        }
        this.$refs.form.validate();
      }
    },



  }
}
</script>
<style>
.image-preview {
  display: inline-block;
  margin-right: 10px;
}
.preview-image {
  width: 100px;
  height: 100px;
  object-fit: cover;
}
</style>
