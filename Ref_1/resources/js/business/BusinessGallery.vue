<template>
  <div class="pa-2">
    <div class="row">
      <div class="col-12">
        <h4>Add Gallery Images</h4>
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
              <v-file-input small-chips v-model="files" accept="image/*" :rules="galleryimageRules" @change="addFiles" show-size label="Upload Gallery Image"></v-file-input>
            </v-col>
            <v-col cols="12" md="12" class="py-0">
              <v-text-field v-model="image_title" label="Title" :rules="titleRule" ></v-text-field>
            </v-col>
            <v-col cols="12" md="12" class="py-0">
              <v-text-field type="number" v-model="price" label="Price" :rules="priceRule" ></v-text-field>
            </v-col>
              <v-col cols="12" md="12" class="py-0">
                <v-textarea v-model="image_description" rows="3" row-height="20" label="Description" class="d-none"></v-textarea>
              </v-col>
          </v-row>
          <v-btn class="my-2 ms-4" color="error" v-if="userdata.id != null" :disabled="!valid" @click="addgalleryImagebyreseller">ADD IMAGE</v-btn>
          <v-btn class="my-2 ms-4" color="error" v-else :disabled="!valid" @click="addgalleryImage">ADD IMAGE</v-btn>
    </v-form>
    <div class="py-4">
          <h4>Gallery Images List</h4>
        </div>
    <div class="justify-center">
      <v-data-table v-model="selected" :headers="headers" :items="galleryimage" hide-default-footer class="elevation-2">
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
                          <v-text-field type="number" v-model="editedItem.price" label="Price" dense></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="12">
                          <v-textarea rows="3" row-height="30" v-model="editedItem.image_description" dense label="Description" class="d-none"></v-textarea>
                        </v-col>
                        <v-col cols="12" sm="6" md="12">
                          <v-file-input 
                            v-model="editedItem.new_image" 
                            accept="image/*" 
                            label="Upload New Image" 
                            show-size
                          ></v-file-input>
                        </v-col>

                      </v-row>
                    </v-container>
                  </v-card-text>
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn class="cred" text @click="close">
                      Cancel
                    </v-btn>
                    <v-btn class="cred" v-if="userdata.id != null" text @click="editgalleryImagebyreseller">
                      Update
                    </v-btn>
                    <v-btn class="cred" text v-else @click="editgalleryImage">
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
                    <v-btn  class="cred" v-if="userdata.id != null" text @click="deleteGalleryimagebyreseller">Delete</v-btn>
                    <v-btn  class="cred" v-else text @click="deleteGalleryimage">Delete</v-btn>
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
          </v-data-table>
        </div>
  </div>
</template>
<script>
import axios from "axios";

export default {
  name: 'BussinessGallery',
  components: {},
  data: () => ({
    
    dialog: false,
    dialogDelete: false,
    singleSelect: false,
    selected: [],
    userdata: [],
    image_type: 0,
    snackbar: false,
    valid: false,
    text: '',
    timeout: '',
    files: [],
    image_title: '',
    image_description: '',
    price: '',
    url:'https://bizlx.s3.ap-south-1.amazonaws.com',
    // url: 'http://127.0.0.1:8000',
    headers: [
      {text: "Image", value: "image_url", sortable: false},
      {text: "Title", value: "image_title", sortable: false},
      {text: "Price", value: "price", sortable: false},
      // {text: "Description", value: "image_description", sortable: false },
      {text: "Actions", align: 'end', value: "action", sortable: false},
    ],
    galleryimage: [],
    editedIndex: -1,
    editedItem: {
      image_title: "",
      image_description: "",
      price: "",
      new_image:"",
    },
    galleryimageRules: [
      v => !!v || 'Gallery image is required.',
    ],
    titleRule: [
      v => !!v || 'Title is required.',
    ],
    priceRule: [
      v => !!v || 'Price is required.',
    ],
  }),
  mounted() {

    this.addFiles();
    this.user_id();
    this.userdata = this.user_id();
    if(this.userdata.id != null){
      this.getGalleryimagesbyreseller();
    }else{
      this.getGalleryimages();
    }
  },
  computed:{
    formTitle() {
      return this.editedIndex === -1 ? '' : 'Edit Gallery Image Title & Description'
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
    getGalleryimages() {
      axios.get('/api/gallery/images')
          .then((resp) => {
            this.galleryimage = resp.data.gallery_images;
          })
    },
    getGalleryimagesbyreseller() {
      axios.post('/api/reseller/gallery/images',{userId:this.userdata.id})
          .then((resp) => {
            this.galleryimage = resp.data.gallery_images;
          })
    },
    addgalleryImage(){
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let data = {
        image_title: this.image_title,
        image_description: this.image_description,
        image_type: this.image_type,
        price: this.price,
        image_url: this.files,
      };
      if(this.image_url != ''){
        axios.post('/api/add/business/gallery_images',data, config)
            .then((resp) =>{
              this.snackbar = true;
              this.timeout = 3000;
              this.text = resp.data.message;
              this.$refs.form.reset();
              this.getGalleryimages();
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
      }
      this.$refs.form.validate();
    },
    addgalleryImagebyreseller(){
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let data = {
        userId:this.userdata.id,
        image_title: this.image_title,
        image_description: this.image_description,
        image_type: this.image_type,
        price: this.price,
        image_url: this.files,
      };
      if(this.image_url != ''){
        axios.post('/api/reseller/add/business/gallery_images',data, config)
            .then((resp) =>{
              this.snackbar = true;
              this.timeout = 3000;
              this.text = resp.data.message;
              this.$refs.form.reset();
              this.getGalleryimagesbyreseller();
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
      }
      this.$refs.form.validate();
    },
    // editgalleryImage(){
    //   const config = {
    //     headers: { 'content-type': 'multipart/form-data' }
    //   }
    //   let data = {
    //     id: this.editedItem.id,
    //     title: this.editedItem.image_title,
    //     price: this.editedItem.price,
    //     description: this.editedItem.image_description,
    //   };
    //   if (this.editedIndex > -1) {
    //     axios.post('/api/update/image',data,config)
    //         .then((resp) =>{
    //           this.snackbar = true;
    //           this.timeout = 3000;
    //           this.text = resp.data.message;
    //           this.getGalleryimages();
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
    // editgalleryImagebyreseller(){
    //   const config = {
    //     headers: { 'content-type': 'multipart/form-data' }
    //   }
    //   let data = {
    //     userId:this.userdata.id,
    //     id: this.editedItem.id,
    //     title: this.editedItem.image_title,
    //     price: this.editedItem.price,
    //     description: this.editedItem.image_description,
    //   };
    //   if (this.editedIndex > -1) {
    //     axios.post('/api/update/image',data,config)
    //         .then((resp) =>{
    //           this.snackbar = true;
    //           this.timeout = 3000;
    //           this.text = resp.data.message;
    //           this.getGalleryimagesbyreseller();
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
    
    editgalleryImage() {
  const config = {
    headers: { 'Content-Type': 'multipart/form-data' }
  };

  let formData = new FormData();
  formData.append("id", this.editedItem.id);
  formData.append("title", this.editedItem.image_title);
  formData.append("price", this.editedItem.price);
  formData.append("description", this.editedItem.image_description);

  // Append image only if a new one is selected
  if (this.editedItem.new_image) {
    formData.append('image', this.editedItem.new_image);
  }

  axios.post('/api/update/image', formData, config)
    .then((resp) => {
      this.snackbar = true;
      this.timeout = 3000;
      this.text = resp.data.message;
      this.getGalleryimages(); // Refresh gallery images
      this.dialog = false;
    })
    .catch(error => {
      let data = error.toJSON();
      if (data.status == 401) {
        this.snackbar = true;
        this.timeout = 3000;
        this.text = error.data.error.message;
      }
    });
},

editgalleryImagebyreseller() {
  const config = {
    headers: { 'Content-Type': 'multipart/form-data' }
  };

  let formData = new FormData();
  formData.append("userId", this.userdata.id);
  formData.append("id", this.editedItem.id);
  formData.append("title", this.editedItem.image_title);
  formData.append("price", this.editedItem.price);
  formData.append("description", this.editedItem.image_description);

  if (this.editedItem.new_image) {
    formData.append('image', this.editedItem.new_image);
  }

  axios.post('/api/update/image', formData, config)
    .then((resp) => {
      this.snackbar = true;
      this.timeout = 3000;
      this.text = resp.data.message;
      this.getGalleryimagesbyreseller();
      this.dialog = false;
    })
    .catch(error => {
      let data = error.toJSON();
      if (data.status == 401) {
        this.snackbar = true;
        this.timeout = 3000;
        this.text = error.data.error.message;
      }
    });
},
    
    deleteGalleryimage(){
      let data = {
        id: this.editedItem.id,
      };
      axios.post('/api/delete/image',data)
          .then((resp) =>{
            this.snackbar = true;
            this.timeout = 3000;
            this.text = resp.data.message;
            this.dialogDelete = false;
            this.getGalleryimages();
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
    deleteGalleryimagebyreseller(){
      let data = {
        id: this.editedItem.id,
        userId:this.userdata.id,
      };
      axios.post('/api/delete/image',data)
          .then((resp) =>{
            this.snackbar = true;
            this.timeout = 3000;
            this.text = resp.data.message;
            this.dialogDelete = false;
            this.getGalleryimagesbyreseller();
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
      this.editedIndex = this.galleryimage.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    deleteItem(item) {
      this.editedIndex = this.galleryimage.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
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