<template>
  <div>
    <div class="row">
      <div class="col-3">
        <h3 class="fw-bold">Job Category</h3>
      </div>
      <div class="col-6">
        <v-text-field v-model="search" clearable append-icon="mdi-magnify" label="Search" single-line hide-details>
        </v-text-field>
      </div>
      <div class="col">
        <v-btn @click="openModel" tile color="error" large>
          <v-icon left>
            mdi-plus
          </v-icon>
          CREATE JOB CATEGORY
        </v-btn>
      </div>
    </div>
    <div class="my-5">
      <v-data-table :search="search" :headers="headers" :items="jobcategory" class="elevation-1">
        <template v-slot:[`item.job_img_url`]="{ item }">
          <img :src=(url+item.job_img_url) style="width: 50px; height: 50px"/>
        </template>
        <template v-slot:top>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-dialog v-model="dialog" persistent max-width="800px">
            <v-card>
              <v-card-title style="justify-content:space-between;" class="text-h6 grey lighten-2">Edit Job Category
                <v-icon  @click="close" class="text-h5">mdi-close</v-icon>
              </v-card-title>
              <v-card-text>
                <v-form ref="form" v-model="valid">
                  <v-row>
                    <v-col cols="12" sm="6" md="12" class="py-1">
                      <v-row>
                        <v-col cols="4">
                          <v-col sm="4" v-for="(file2, f) in filesEdit" :key="f">
                            <img :ref="'file2'" width="100"
                                 height="100" style="display:block">
                          </v-col>
                          <div v-if="isHidden2">
                            <img :src=(jobcategory.job_img_url)>
                          </div>
                          <div v-else>
                            <img :src=(url+editedItem.job_img_url) style="width: 100px;">
                          </div>
                        </v-col>
                        <v-col cols="8">
                          <v-file-input accept="image/*" label="Job Category Image" prepend-icon="mdi-camera" multiple
                                        small-chips
                                        v-model="filesEdit"
                                        @change="editFiles"
                                        v-on:click="isHidden2 = true">
                          </v-file-input>
                        </v-col>
                      </v-row>
                    </v-col>
                    <v-col cols="12" sm="6" md="6" class="py-1">
                      <v-text-field v-model="editedItem.job_cat_name" :rules="job_cat_nameRules" label="Job Category Name" type="text"></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="6" class="py-1">
                      <v-text-field v-model="editedItem.job_cat_sort" label="Category Sort" type="number"></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="6" class="py-1">
                      <v-checkbox v-model="editedItem.job_cat_feature" label="Featured"></v-checkbox>
                    </v-col>
                    <v-col cols="12" sm="6" md="6" class="py-1">
                      <v-checkbox v-model="editedItem.job_cat_status" label="Active"></v-checkbox>
                    </v-col>
                    <v-col cols="12" md="12">
                      <div class="">
                        <v-btn @click="close" color="error" class="my-4 me-3">CANCEL</v-btn>
                        <v-btn class="my-4 me-3" color="success" :disabled="!valid" @click="editjobcategory">UPDATE</v-btn>
                      </div>
                    </v-col>
                  </v-row>
                </v-form>
              </v-card-text>
            </v-card>
          </v-dialog>

          <v-dialog v-model="dialogDelete" max-width="510px">
            <v-card>
              <v-card-title class="text-h6">Are you sure you want to delete this job category?</v-card-title>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error" text @click="closeDelete">Cancel</v-btn>
                <v-btn color="error" text @click="deletejobcategory">Delete</v-btn>
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <v-dialog v-model="jobcatMultidelete" max-width="550px">
            <v-card>
              <v-card-title class="text-h6">Are you sure you want to delete these job categories?</v-card-title>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error" text @click="closeDelete1">Cancel</v-btn>
                <v-btn color="error" text @click="deleteMultiplejobcat">Delete</v-btn>
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </template>
        <template v-slot:[`item.action`]="{ item }">
          <v-icon small class="mr-2" @click="editItem(item)">
            mdi-pencil
          </v-icon>
          <v-icon small @click="deleteItem(item)">
            mdi-delete
          </v-icon>
        </template>

        <template v-slot:[`item.job_cat_feature`]="{ item }">
          <div v-if="item.job_cat_feature == 1">
            <p>Yes</p>
          </div>
          <div v-else>
            <p>No</p>
          </div>
        </template>

        <template v-slot:[`item.job_cat_status`]="{ item }">
          <div v-if="item.job_cat_status == '1'">
            <span>Active</span>
          </div>
          <div v-else>
            <span>Inactive</span>
          </div>
        </template>
<!--        <template v-slot:footer>-->
<!--          <div class="text-muted py-3 px-5">-->
<!--            <v-btn color="grey" @click="getSelectedIds">DELETE SELECTED</v-btn>-->
<!--          </div>-->
<!--        </template>-->
      </v-data-table>
    </div>
    <v-dialog v-model="showModel" persistent max-width="800px">
      <v-card>
        <v-card-title style="justify-content:space-between;" class="text-h6 grey lighten-2">Add New Job Category
          <v-icon  @click="showModel = false" class="text-h5">mdi-close</v-icon>
        </v-card-title>
        <v-card-text>
          <v-form ref="form" v-model="valid">
            <v-row>
              <v-col cols="12" sm="6" md="12" class="py-1">
                <v-row>
                  <v-col cols="4">
                    <v-col sm="4" v-for="(file, f) in files" :key="f">
                      <img :ref="'file'" width="64"
                           height="64" style="display:block">
                    </v-col>
                    <div v-if="isHidden"></div>
                    <div v-else>
                      <img src="https://dummyimage.com/64x64/000/fff?text= 64x64">
                    </div>
                  </v-col>
                  <v-col cols="8">
                    <v-file-input accept="image/*" label="Job Category Image" prepend-icon="mdi-camera" multiple
                                  small-chips
                                  v-model="files"
                                  @change="addFiles"
                                  :rules="filesRules"
                                  v-on:click="isHidden = true">
                    </v-file-input>
                  </v-col>
                </v-row>
              </v-col>
              <v-col cols="12" sm="6" md="6" class="py-1">
                <v-text-field v-model="defaultItem.job_cat_name" :rules="job_cat_nameRules" label="Job Category Name" type="text"></v-text-field>
              </v-col>
              <v-col cols="12" sm="6" md="6" class="py-1">
                <v-text-field v-model="defaultItem.job_cat_sort" :rules="job_cat_sortRules" :counter="2" label="Category Sort" type="number"></v-text-field>
              </v-col>
              <v-col cols="12" sm="6" md="6" class="py-1">
                <v-checkbox v-model="defaultItem.job_cat_feature" label="Featured"></v-checkbox>
              </v-col>
              <v-col cols="12" sm="6" md="6" class="py-1">
                <v-checkbox v-model="defaultItem.job_cat_status" label="Active" class="d-none"></v-checkbox>
              </v-col>
              <v-col cols="12" md="12">
                <div class="">
                  <v-btn @click="showModel = false" class="my-4 me-3" color="error">CANCEL</v-btn>
                  <v-btn class="my-4 me-3" color="success" :disabled="!valid" @click="Addjobcategory">SAVE</v-btn>
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
  name: 'JobCategory',
  data(){
    return{
        url:'https://bizlx.s3.ap-south-1.amazonaws.com',
        search: '',
        loading: false,
        showModel: false,
        dialog: false,
        dialogDelete: false,
        jobcatMultidelete: false,
        isHidden: false,
        isHidden2: false,
        files: [],
        filesEdit: [],
        checkedIds: [],
        readers: [],
        headers: [
          { text: 'Image', value: 'job_img_url'},
          { text: 'Job Category', value: 'job_cat_name'},
          { text: 'Sort', value: 'job_cat_sort'},
          { text: 'Featured', value: 'job_cat_feature'},
          { text: 'Status', align: 'end', value: 'job_cat_status', sortable: false },
          { text: 'Actions', align: 'end', value: 'action', sortable: false },
        ],
        jobcategory: [],
        editedIndex: -1,
        editedItem: {
          id : '',
          job_img_url: '',
          job_cat_name : '',
          job_cat_sort : '',
          job_cat_feature: '',
          job_cat_status: true,
        },
        defaultItem: {
          id : '',
          job_img_url: '',
          job_cat_name : '',
          job_cat_sort : '',
          job_cat_feature : '',
          job_cat_status : true,
        },

        snackbar: false,
        text: '',
        timeout: '',

        valid: false,
        filesRules: [
          v => !!v[0] || 'Job category image is required.',
        ],
        job_cat_nameRules: [
          v => !!v || 'Job category Name is required.',
        ],
        job_cat_sortRules: [ // v.toString()
          v => !! v || 'Job sort is required.',
          v => v.length <= 2 || 'Job category sort less than 2 characters.',
          v => v >= 0 || 'Cant use minus.',
        ],
        job_cat_statusRules: [
          v => !!v || 'Job status is required.',
        ],
     }
  },
  watch: {
    dialog(val) {
      val || this.close()
    },
    dialogDelete(val) {
      val || this.closeDelete()
    },
  },
  created() {
    this.getJobcategory();
    this.addFiles();
    this.editFiles();
  },
  methods: {
    openModel() {
      this.showModel = true;
    },
    addFiles(){
      this.files.forEach((file, f) => {
        this.readers[f] = new FileReader();
        this.readers[f].onloadend = () => {
          let fileData = this.readers[f].result
          let imgRef = this.$refs.file[f]
          imgRef.src = fileData,
          this.defaultItem.job_img_url = this.files[0]
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
          this.editedItem.job_img_url = this.filesEdit[0]
        }
        this.readers[f].readAsDataURL(this.filesEdit[f]);
      })
    },
    getJobcategory() {
      axios.get("/api/admin/jobcategory")
          .then((resp) =>{
            this.jobcategory =  resp.data.jobcategories;
          });
    },

    // Addjobcategory(){ // new add
    //   const config = {
    //     headers: { 'content-type': 'multipart/form-data' }
    //   }
    //   axios.post("/api/jobcategory/add",this.defaultItem,config)
    //       .then((resp) => {
    //           this.snackbar = true;
    //           this.text = resp.data.message;
    //           this.showModel = false;
    //           this.timeout = 3000;
    //           this.getJobcategory();
    //           this.$refs.form.reset();
    //         if(resp.data.error){
    //           if(resp.data.error.job_cat_name){
    //             this.snackbar = true;
    //             this.text = 'The job category Name has already been taken.';
    //           }
    //         }
    //       })
    //   this.$refs.form.validate();
    // },
 
    Addjobcategory() {
  const config = { headers: { 'content-type': 'multipart/form-data' } };

  // Check for existing sort
  const existingSort = this.jobcategory.find(cat => cat.job_cat_sort == this.defaultItem.job_cat_sort);
  if (existingSort) {
    this.snackbar = true;
    this.text = `Category Sort ${this.defaultItem.job_cat_sort} already exists. Please choose a unique sort number.`;
    this.timeout = 3000;
    return;
  }
  this.loading = true;
  axios.post("/api/jobcategory/add", this.defaultItem, config)
    .then((resp) => {
      this.snackbar = true;
      this.text = resp.data.message;
      this.showModel = false;
      this.timeout = 3000;
      this.getJobcategory();
      this.$refs.form.reset();
      if(resp.data.error){
              if(resp.data.error.job_cat_name){
                this.snackbar = true;
                this.text = 'The job category Name has already been taken.';
              }
            }
    })
    .catch(error => {
      console.error(error);
    })
    .finally(() => {
      this.loading = false; // ✅ End loading always
    });;

  this.$refs.form.validate();
},

 
 
 
    // editjobcategory() {
    //     const config = {
    //         headers: { 'content-type': 'multipart/form-data' }
    //     }
    //       if (this.editedIndex > -1) { // Edit
    //             axios.post("/api/jobcategory/edit/"+this.editedItem.id, this.editedItem, config)
    //               .then((resp) => {
    //                     this.snackbar = true;
    //                     this.text = resp.data.message;
    //                     this.timeout = 3000;
    //                     this.getJobcategory();
    //                     this.close();
    //                     this.$refs.form.reset();
    //                })
    //                 .catch(error=>{
    //                     this.error = error;
    //                     this.snackbar = true;
    //                     this.text = "Something went wrong";
    //                     this.timeout = 3000;
    //                     this.getJobcategory();
    //                   });
    //         }
    //     this.$refs.form.validate();
    // },
    editjobcategory() {
  const config = { headers: { 'content-type': 'multipart/form-data' } };

  if (this.editedIndex > -1) {
    // Find the old item
    const oldItem = this.jobcategory[this.editedIndex];

    // Only if sort value is changed
    if (oldItem.job_cat_sort !== this.editedItem.job_cat_sort) {
      const conflictingItem = this.jobcategory.find(cat => cat.job_cat_sort == this.editedItem.job_cat_sort);

      if (conflictingItem) {
        // Swap sort values
        let tempSort = conflictingItem.job_cat_sort;
        conflictingItem.job_cat_sort = oldItem.job_cat_sort;
        this.loading = true;
        // Update conflicting item first
        axios.post("/api/jobcategory/edit/" + conflictingItem.id, conflictingItem, config)
          .then(() => {
            // Now update edited item
            this.updateEditedItem(config);
          })
          .catch(error => {
            this.loading = false;
            console.error(error);
          });
      } else {
        // No conflict, update directly
        this.updateEditedItem(config);
      }
    } else {
      // Sort not changed, update directly
      this.updateEditedItem(config);
    }
  }

  this.$refs.form.validate();
},

updateEditedItem(config) {
  this.loading = true; 
  axios.post("/api/jobcategory/edit/" + this.editedItem.id, this.editedItem, config)
    .then((resp) => {
      this.snackbar = true;
      this.text = resp.data.message;
      this.timeout = 3000;
      this.getJobcategory();
      this.close();
      this.$refs.form.reset();
    })
    .catch(error => {
      this.error = error;
      this.snackbar = true;
      this.text = "Something went wrong";
      this.timeout = 3000;
      this.getJobcategory();
    })
    .finally(() => {
      this.loading = false; // ✅ End loading
    });;
},

    deletejobcategory(){
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let data = {
        job_id: this.editedItem.id,
      };
      axios.post('/api/delete/jobcategory',data,config)
          .then((resp) =>{
            this.snackbar = true;
            this.timeout = 3000;
            this.text = resp.data.message;
            this.dialogDelete = false;
            this.getJobcategory();
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

    getSelectedIds() {
      let jobcat = {
        ids:this.checkedIds.map(item => item.id)
      };
      if(jobcat.ids.length > 0){
        this.jobcatMultidelete = true;
      }else{
        this.snackbar = true;
        this.text = 'Please select job category';
        this.timeout = 3000;
      }

      // const selectedIds = this.checkedIds.map(item => item.id);

    },
    deleteMultiplejobcat() {
      var data = {
        ids:this.checkedIds.map(item => item.id)
      };
      axios.post('/api/delete/multijobcategory',data)
          .then((resp) => {
            if( resp.data.success == true){
              this.jobcatMultidelete = false;
              this.getJobcategory();
              this.snackbar = true;
              this.text = resp.data.message;
              this.timeout = 3000;
            }else{
              this.jobcatMultidelete = false;
              this.getJobcategory();
              this.snackbar = true;
              this.text = "Something went wrong";
              this.timeout = 3000;
            }
          });

    },
    closeDelete1() {
      this.jobcatMultidelete = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },
    editItem(item) {
      this.editedIndex = this.jobcategory.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },
    deleteItem(item) {
      this.editedIndex = this.jobcategory.indexOf(item)
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
  },
}
</script>
