<template>
    <div class="pa-2" >
      <div class="row">
        <div class="col-12 col-md-6 col-sm-12 d-flex">
          <h4>Add Main Cover Image</h4> <span class="mt-1 ms-2">(Max 3 Cover Images Can Be Loaded)</span>
        </div>
      </div>
          <v-snackbar v-model="snackbar" :timeout="timeout">{{text}}
            <template v-slot:action="{ attrs }">
              <v-btn color="blue"  v-bind="attrs" text @click="snackbar = false">Close</v-btn>
            </template>
          </v-snackbar>
  
      <v-form ref="form" v-model="valid" lazy-validation>
            <v-row>
              <v-col cols="12" md="12" class="py-0">
                <v-file-input small-chips v-model="files" accept="image/*" :rules="coverimageRules" @change="addFiles" required show-size label="Upload Image"></v-file-input>
              </v-col>
  <!--            <v-col cols="12" md="12" class="py-0">-->
  <!--              <v-text-field v-model="image_title" class="d-none" label="Title"></v-text-field>-->
  <!--            </v-col>-->
  <!--              <v-col cols="12" md="12" class="py-0">-->
  <!--                <v-textarea v-model="image_description" class="d-none" rows="3" row-height="20" label="Description"></v-textarea>-->
  <!--              </v-col>-->
            </v-row>
            <!-- <v-btn class="my-4 ms-4" color="error" v-if="userdata.id != null"  @click="addcoverImagebyreseller">ADD IMAGE</v-btn> -->
            <v-btn class="my-4 ms-4" color="error" v-if="userdata.id != null" :disabled="!valid || loading" @click="addcoverImagebyreseller">
                      <v-progress-circular v-if="loading" indeterminate color="white" size="20"></v-progress-circular>
                      <span v-else>ADD IMAGE</span>
                      </v-btn>
            <v-btn class="my-4 ms-4" color="error" v-else  @click="addcoverImage">ADD IMAGE</v-btn>
          </v-form>
      <div class="py-4">
        <h4>Cover Images List</h4>
      </div>
      <div class="">
        <v-data-table  :headers="headers" :items="coverimage" hide-default-footer class="elevation-2">
            <template v-slot:[`item.image_url`]="{ item }">
              <img :src=url+(item.image_url) style="width: 150px; height: 100px" class="py-2"/>
          </template> 
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
                        <v-text-field v-model="editedItem.image_title" label="Title" dense></v-text-field>
                      </v-col>
                      <v-col cols="12" sm="6" md="12">
                        <v-textarea rows="3" row-height="30" dense v-model="editedItem.image_description" label="Description"></v-textarea>
                      </v-col>
                      <v-col cols="12" sm="6" md="12">
                      <v-file-input
                        v-model="editedItem.image_file"
                        accept="image/*"
                        label="Update Cover Image"
                        show-size
                        dense
                      ></v-file-input>
                    </v-col>

                    </v-row>
                  </v-container>
                </v-card-text>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="error" text @click="close">
                    Cancel
                  </v-btn>
                  <v-btn color="error" text v-if="userdata.id != null" @click="editcoverImagebyreseller">
                    Update
                  </v-btn>
                  <v-btn color="error" v-else text @click="editcoverImage">
                    Update
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
                  <v-btn class="cred" text @click="closeDelete">Cancel</v-btn>
                  <v-btn class="cred" v-if="userdata.id != null" text @click="deleteCoverimagebyreseller">Delete</v-btn>
                  <v-btn class="cred" v-else text @click="deleteCoverimage">Delete</v-btn>
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
          <template v-slot:[`item.active`]="{ item }">
            <v-icon small class="mr-2" @click="editItem(item)">
              mdi-check
            </v-icon>
          </template>
        </v-data-table>
      </div>
    </div>          
  </template>
  <script>
  import axios from "axios";
  
  export default {
    name: 'AdminBussinessCoverEdit',
    components: {},
    data: () => ({
      loading: false,
      dialogDelete: false,
      dialog: false,
      singleSelect: false,
      image_type: 1,
      selected: [],
      userdata: [],
      snackbar: false,
      valid: false,
      text: '',
      timeout: -1,
      files: [],
      image_title: '',
      image_description: '',
      url:'https://bizlx.s3.ap-south-1.amazonaws.com',
    //   url:'http://127.0.0.1:8000',
      headers: [
        { text: "Image",  value: "image_url", sortable: false },
        { text: "Title", value: "image_title", sortable: false },
        { text: "Description", value: "image_description", sortable: false },
        { text: "Actions", align: 'end', value: "action", sortable: false },
      ],
      coverimage: [],
      editedIndex: -1,
      editedItem: {
        image_file : "",
        image_title: "",
        image_description: "",
      },
      coverimageRules: [
        v => !!v || 'Cover image is required.',
      ],
    }),
    mounted() {
      this.addFiles();
      this.user_id();
        this.userdata = this.user_id();
        this.getBusinessname();
        if(this.userdata.id != null){
        this.getCoverimagesbyreseller();
      }else{
        this.getCoverimages();
      }
    },

    computed:{
      formTitle() {
        return this.editedIndex === -1 ? '' : 'Edit Cover Image Title & Description'
      },
    },
  
    methods: {
      user_id() {
        const uid = localStorage.getItem('userData');
      try {
          return JSON.parse(uid); // Parse JSON if it's a string
      } catch (e) {
        return this.$store.state.userId;
      }
      },
    // user_id() {
    //     const url = window.location.href;
    //     const lastSegment = url.split('/').filter(Boolean).pop();
    //     return lastSegment;
    //   },
      addFiles(){
        this.files.forEach((file, f) => {
          this.readers[f] = new FileReader();
          this.readers[f].onloadend = () => {
            let fileData = this.readers[f].result
            let imgRef = this.$refs.file[f]
            imgRef.src = fileData
            // send to server here...
          }
          this.readers[f].readAsDataURL(this.files[f]);
        })
      },
      getCoverimages() {
        axios.post('/api/cover/coverImagebyadmin',{userId:this.user_id()})
            .then((resp)=>{
              this.coverimage = resp.data.cover_image;
            })
      },

      getBusinessname() {
        axios.get('/api/get/business/namecity')
            .then((resp)=>{
              this.image_title = resp.data.businessdata.business_name + ' ' + resp.data.businessdata.city_name + ' ' + 'Best Deals in' + ' ' + resp.data.businessdata.city_name;
              this.image_description = resp.data.businessdata.business_name + ' ' + resp.data.businessdata.city_name + ' ' + ' offers the latest deals, sales, and jobs for staff on Bizlx';
            })
      },
      getCoverimagesbyreseller() {
        axios.post('/api/reseller/cover/image',{userId:this.userdata.id})
            .then((resp)=>{
              this.coverimage = resp.data.cover_image;
            })
      },
      addcoverImage(){
        const config = {
          headers: { 'content-type': 'multipart/form-data' }
        }
        const userId = this.user_id();
        let data = {
          user_id: userId, 
          image_title: this.image_title,
          image_description: this.image_description,
          image_type: this.image_type,
          image_url: this.files,
        };
        if(this.image_url != ''){
          axios.post('/api/add/business/cover_images1',data, config)
              .then((resp) =>{
                this.snackbar = true;
                this.timeout = 3000;
                this.text = resp.data.message;
                this.getCoverimages();
                this.$refs.form.reset();
              });
          this.$refs.form.validate();
        }
      },
      addcoverImagebyreseller(){
        this.loading= true;
        const config = {
          headers: { 'content-type': 'multipart/form-data' }
        }
        let data = {
          userId:this.userdata.id,
          image_title: this.image_title,
          image_description: this.image_description,
          image_type: this.image_type,
          image_url: this.files,
        };
        if(this.image_url != ''){
          axios.post('/api/reseller/add/business/cover_images',data, config)
              .then((resp) =>{
                this.loading= false,

                this.snackbar = true;
                this.timeout = 3000;
                this.text = resp.data.message;
                this.$refs.form.reset();
                this.getCoverimagesbyreseller();
              })
              .catch(error => {
                // Handle the error
                var data = error.toJSON();
                if(data.status == 400){
                this.loading= false,
                  
                  this.snackbar = true;
                  this.timeout = 3000;
                  this.text = error.data.error.message;
                }
              });
          this.$refs.form.validate();
        }
      },
      // editcoverImage(){
      //   let data = {
      //     title: this.editedItem.image_title,
      //     id: this.editedItem.id,
      //     description: this.editedItem.image_description,
      //   };
      //   if (this.editedIndex > -1) {
      //     axios.post('/api/update/image',data)
      //         .then((resp) =>{
      //           this.snackbar = true;
      //           this.timeout = 3000;
      //           this.text = resp.data.message;
      //           this.getCoverimages();
      //           this.dialog = false;
      //         })
      //         .catch(error => {
      //           // Handle the error
      //           var data = error.toJSON();
      //           if(data.status == 401){
      //             this.snackbar = true;
      //             this.timeout = 3000;
      //             this.text = error.data.error.message;
      //           }
      //         });
      //   }
      // },
      // editcoverImagebyreseller(){
      //   let data = {
      //     userId:this.userdata.id,
      //     title: this.editedItem.image_title,
      //     id: this.editedItem.id,
      //     description: this.editedItem.image_description,
      //   };
      //   if (this.editedIndex > -1) {
      //     axios.post('/api/update/image',data)
      //         .then((resp) =>{
      //           this.snackbar = true;
      //           this.timeout = 3000;
      //           this.text = resp.data.message;
      //           this.getCoverimagesbyreseller();
      //           this.dialog = false;
      //         })
      //         .catch(error => {
      //           // Handle the error
      //           var data = error.toJSON();
      //           if(data.status == 401){
      //             this.snackbar = true;
      //             this.timeout = 3000;
      //             this.text = error.data.error.message;
      //           }
      //         });
      //   }
      // },
      editcoverImage() {
  const formData = new FormData();
  formData.append('id', this.editedItem.id);
  formData.append('title', this.editedItem.image_title);
  formData.append('description', this.editedItem.image_description);
  
  // Append image only if it exists
  if (this.editedItem.image_file) {
    formData.append('image', this.editedItem.image_file);
  }

  axios.post('/api/update/image', formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  })
    .then((resp) => {
      this.snackbar = true;
      this.timeout = 3000;
      this.text = resp.data.message;
      this.getCoverimages();
      this.dialog = false;
    })
    .catch(error => {
      this.snackbar = true;
      this.timeout = 3000;
      this.text = error.response?.data?.error?.message || 'An error occurred';
    });
},

editcoverImagebyreseller() {
  const formData = new FormData();
  formData.append('userId', this.userdata.id);
  formData.append('id', this.editedItem.id);
  formData.append('title', this.editedItem.image_title);
  formData.append('description', this.editedItem.image_description);
  
  // Append image only if it exists
  if (this.editedItem.image_file) {
    formData.append('image', this.editedItem.image_file);
  }

  axios.post('/api/update/image', formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  })
    .then((resp) => {
      this.snackbar = true;
      this.timeout = 3000;
      this.text = resp.data.message;
      this.getCoverimagesbyreseller();
      this.dialog = false;
    })
    .catch(error => {
      this.snackbar = true;
      this.timeout = 3000;
      this.text = error.response?.data?.error?.message || 'An error occurred';
    });
},

      deleteCoverimage(){
        let data = {
          id: this.editedItem.id,
        };
      
        axios.post('/api/delete/image',data)
            .then((resp) =>{
              this.snackbar = true;
              this.timeout = 3000;
              this.text = resp.data.message;
              this.dialogDelete = false;
              this.getCoverimages();
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
      deleteCoverimagebyreseller(){
        let data = {
          userId:this.userdata.id,
          id: this.editedItem.id,
        };
    
        axios.post('/api/delete/image',data)
            .then((resp) =>{
              this.snackbar = true;
              this.timeout = 3000;
              this.text = resp.data.message;
              this.dialogDelete = false;
              this.getCoverimagesbyreseller();
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
      editItem(item) {
        this.editedIndex = this.coverimage.indexOf(item);
        this.editedItem = Object.assign({}, item);
        this.dialog = true;
      },
  
      deleteItem(item) {
        this.editedIndex = this.coverimage.indexOf(item);
        this.editedItem = Object.assign({}, item);
        this.dialogDelete = true;
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
    },
  }
  </script>