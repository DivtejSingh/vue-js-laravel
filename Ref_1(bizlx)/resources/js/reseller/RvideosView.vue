<template>
    <div>
        <div class="row pb-3">
          <div class="col-12 col-sm-4">
            <v-text-field v-model="search4" append-icon="mdi-magnify" label="Search" single-line hide-details>
            </v-text-field>
          </div>
          <div class="col-12 col-sm-4 test-decoration-none">
            <div v-if="videoscount === userData.video">
              <v-btn @click="MessageModel5 = true" class="error"  tile large>
                <v-icon left>mdi-plus</v-icon>ADD VIDEO
              </v-btn>
            </div>
            <div v-else>
              <v-btn @click="openvideo" class="error"  tile large>
                <v-icon left>mdi-plus</v-icon>ADD VIDEO
              </v-btn>
            </div>
          </div>
        </div>
        <div class="">
          <v-data-table :search="search4" :headers="headers4" :items="itemsWithSno" class="elevation-1">
            <template v-slot:top>
              <v-dialog v-model="dialog4" max-width="900px">
                <v-card>
                  <v-form @submit.prevent="videoFormSubmit" ref="form" v-model="valid" lazy-validation>
                      <v-card-title class="text-h5 grey lighten-2" style="justify-content:space-between;">{{ formTitle4 }}
                        <v-icon  @click="close" class="text-h5">mdi-close</v-icon>
                      </v-card-title>
                      <v-card-text>
                        <v-row>
                          <v-col cols="12" md="12" class="py-0">
                            <v-text-field 
                                label="Video Title"
                                v-model="editedItem.video_title"
                                :rules="video_titleRules"
                                :counter="100"></v-text-field>
                          </v-col>
                          <v-col cols="12" md="12" class="py-0">
                            <v-text-field 
                                type="url"
                                label="Youtube link"
                                v-model="editedItem.video_url"
                                :rules="video_urlRules"
                                placeholder="https://www.youtube.com/watch?v=t0Q2otsqC4I"></v-text-field>
                            
                          </v-col>
                          <!-- <v-col cols="12" md="6" class="py-0">
                            <v-text-field 
                                type="url"
                                label="Youtube link"
                                v-model="editedItem.youtube_id"
                                :rules="youtube_linkRules"
                                placeholder="t0Q2otsqC4I"></v-text-field>
                          </v-col> -->
                          <v-col cols="12" md="12" class="py-0">
                            <v-textarea 
                                rows="2" 
                                row-height="20" 
                                label="Description"
                                v-model="editedItem.video_description"
                                :rules="video_descriptionRules"></v-textarea>
                          </v-col>
                        </v-row>
                        <v-col cols="12" md="12" class="py-0 d-flex" style="justify-content: space-between;">
                          <div>
                            <v-checkbox v-model="editedItem.video_status" label="Active"></v-checkbox>
                          </div>
                          <div class="ms-3 pb-3">
                            <v-btn v-if="isLoading" class="my-2 me-3 cred">Loading...</v-btn>
                            <v-btn v-else class="my-2 me-3" color="success" type="submit" :disabled="!valid">UPDATE</v-btn>
                            <v-btn @click="close" class="my-2 me-3" color="error">CLOSE</v-btn>
                          </div>
                        </v-col>
                      </v-card-text>
                  </v-form>
                </v-card>
              </v-dialog>
              <v-dialog v-model="dialogDelete" max-width="500px">
                <v-card>
                  <v-card-title class="text-h5">Are you sure you want to delete this item?</v-card-title>
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
                    <v-btn color="blue darken-1" text @click="deleteItemConfirm">OK</v-btn>
                    <v-spacer></v-spacer>
                  </v-card-actions>
                </v-card>
              </v-dialog>
            </template>
            <template v-slot:[`item.description`]="{ item }">
                <div>{{ item.video_description.slice(0, descMaxLength)+'...' }}</div>
            </template>
            <template v-slot:[`item.eye`]="{ item }">
              <a :href= "'/videos/detail/'+item.video_slug"  target="_blank" class="link-dark">
                <v-icon small class="mr-2">mdi-eye </v-icon>
              </a>
            </template>
            <template v-slot:[`item.action4`]="{ item }">
              <v-icon small class="mr-2" @click="editItem(item)">mdi-pencil</v-icon>
              <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
            </template>

            <template v-slot:[`item.active4`]="{ item }">
                <div v-if="item.video_status === 1">
                  <span>Active</span>
                </div>
                <div v-else>
                  <span>Inactive</span>
                </div>
            </template>
            
          </v-data-table>
        </div>
        <v-dialog v-model="showModel4" persistent max-width="900">
          <v-card>
              <v-form @submit.prevent="videoFormSubmit" ref="form" v-model="valid" lazy-validation>
                  <v-card-title class="text-h5 grey lighten-2 d-flex" style="justify-content:space-between;">{{ formTitle4 }}
                      <v-icon  @click="showModel4 = false" class="text-h5">mdi-close</v-icon>
                  </v-card-title>
                  <v-card-text>
                    <v-row>
                      <v-col cols="12" md="12" class="py-0">
                        <v-text-field 
                            label="Video Title"
                            v-model="formData.video_title"
                            :rules="video_titleRules"
                            :counter="100"></v-text-field>
                      </v-col>
                      <v-col cols="12" md="12" class="py-0">
                        <v-text-field 
                            type="url"
                            label="Youtube link"
                            v-model="formData.video_url"
                            :rules="video_urlRules"
                            placeholder="https://www.youtube.com/watch?v=t0Q2otsqC4I"></v-text-field>
                        
                      </v-col>
                      <!-- <v-col cols="12" md="6" class="py-0">
                        <v-text-field 
                            type="url"
                            label="Youtube link"
                            v-model="formData.youtube_link"
                            :rules="youtube_linkRules"
                            placeholder="https://www.youtube.com/watch?v=t0Q2otsqC4I"></v-text-field>
                      </v-col> -->
                      <v-col cols="12" md="12" class="py-0">
                        <v-textarea 
                            rows="2" 
                            row-height="20" 
                            label="Description"
                            v-model="formData.video_description"
                            :rules="video_descriptionRules"></v-textarea>
                      </v-col>
                    </v-row>
                  </v-card-text>
                  <div class="ms-3 pb-3">
                    <v-btn v-if="isLoading" class="my-2 me-3 cred">Loading...</v-btn>
                    <v-btn v-else class="my-2 me-3" color="success" type="submit" :disabled="!valid">SAVE</v-btn>
                    <v-btn @click="showModel4 = false" class="my-2 me-3" color="error" >CLOSE</v-btn>
                  </div>
              </v-form>
          </v-card>
        </v-dialog>
        <v-dialog v-model="MessageModel4" persistent max-width="500">
          <v-card>
            <v-col cols="12" md="12" class="d-flex" style="justify-content: space-between;">
              <div class="mt-1 text-h6">{{ text }}</div>
              <div @click="closeDelete" class="my-2">
                 <v-icon>mdi-close</v-icon>
              </div>
            </v-col>
          </v-card>
        </v-dialog>
       <v-dialog v-model="MessageModel5" persistent max-width="500">
          <v-card>
            <v-col cols="12" md="12" class="d-flex" style="justify-content: space-between;">
              <div class="mt-1 text-h6">Your present plan has limit of {{userData.video}} videos</div>
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
    videosData: [],
    videoscount: '',
    userData: [],
    search4: '',
    MessageModel4: false,
    MessageModel5: false,
    showModel4: false,
    dialog4: false,
    dialogDelete: false,
    headers4: [
      { text: "Serial #", value: 'sno', sortable: false },
      { text: "Title", value: "video_title", sortable: false },
      { text: "Video", value: "video_url", sortable: false },
      { text: "Description", value: "description", sortable: false },
      { text: "Status", align: 'end', value: "active4", sortable: false },
      { text: "View Link", align: 'end', value: "eye", sortable: false },
      { text: "Actions", align: 'end', value: "action4", sortable: false },
    ],
    valid: false,
    formData:{
        video_title: '',
        video_description: '',
        video_url: '',
        youtube_link: '',
        video_status: '',
    },
    video_titleRules: [
    v => !!v || 'Video title is required.',
    v => (v && v.length >= 8) || 'Video title must be greater than 8 characters.',
    ],
    video_descriptionRules: [
        v => !!v || 'Description is required.',
    ],
    video_urlRules: [
        v => !!v || 'Video url is required.',
        // v => !!v!=/^(http|https)/ || ' url is required.',
    ],
    youtube_linkRules: [
        v => !!v || 'Youtube link is required.',
    ],
    
    editedIndex: -1,
    editedItem: {
        video_id: '',
        video_title: '',
        video_description: '',
        video_url: '',
        youtube_link: '',
        video_status: 1,
    },
    defaultItem: {
        title: '',
        video: '',
        shortdescription: '',
    },
    isLoading: false,
    text: '',

  }),
  created() {
    this.user_id();
    this.userdata = this.user_id();
    if(this.userdata.id != null){
      this.getvideoByReseller();
    }else{
      this.getvideo();
    }
  },
  computed: {
    itemsWithSno() {
      return this.videosData.map((d, index) => ({ ...d, sno: index + 1 }))
    },
    formTitle4() {
      return this.editedIndex === -1 ? 'Add Video' : 'Edit Video'
    },
  },
  methods:{
      user_id() {
        const uid = localStorage.getItem('userId');
            try{
              return JSON.parse(uid);
            }
            catch(e){
              return this.$store.state.userId;  
            }
      },
      openvideo() {
        this.showModel4 = true;
        this.$refs.form.reset();
      },
      getvideoByReseller() {
        axios.post("/api/businesses/videos/getbyReseller", {userId:this.userdata.id})
          .then((resp) =>{
                this.videosData = resp.data.allVideos;
                this.videoscount = resp.data.allVideos.length;
                this.userData = resp.data.user;
        });
      },
      // getvideo() {
      //   axios.get("/api/businesses/videos/get")
      //     .then((resp) =>{
      //           this.videosData = resp.data.allVideos;
      //           this.videoscount = resp.data.allVideos.length;
      //           this.userData = resp.data.user;
      //   });
      // },
      editItem(item) {
        this.editedIndex = this.itemsWithSno.indexOf(item);
        this.editedItem = Object.assign({}, item);
        this.dialog4 = true;
      },

    deleteItem(item) {
      this.editedIndex = this.itemsWithSno.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },

    deleteItemConfirm() {
      let data = { 
        video_id: this.editedItem.video_id,
      };
      if(this.userdata.id != null){
        axios.post("/api/delete/videobyReseller", data)
          .then((resp) => { // delete
            this.closeDelete();
              this.MessageModel4 = true,
              this.text = resp.data.message;
            this.getvideoByReseller();
          }
        );
      }else{
        axios.post("/api/delete/video", data)
          .then((resp) => { // delete
            this.closeDelete();
              this.MessageModel4 = true,
              this.text = resp.data.message;
            this.getvideo();
          }
        );
      }
      this.videosData.splice(this.editedIndex, 1);
    },

    close() {
      this.dialog4 = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
        this.$refs.form.reset();
      });
    },

    closeDelete() {
      this.dialogDelete = false;
      this.MessageModel4 = false;
      this.MessageModel5 = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    videoFormSubmit(){
      if (this.editedIndex > -1) { // edit sale
        const config = {
            headers: { 'content-type': 'multipart/form-data' }
        }
        let data = { 
          user_id: this.userData.user_id,
          video_id: this.editedItem.video_id,
          video_title: this.editedItem.video_title,
          video_description: this.editedItem.video_description,
          video_url: this.editedItem.video_url,
          youtube_link: this.editedItem.video_url,
          video_status: this.editedItem.video_status,
        };
        this.isLoading = true;
        if(this.userdata.id != null){
          axios.post("/api/update/videobyReseller", data, config)
            .then((resp) => {
              this.close();
                this.isLoading = false;
                this.MessageModel4 = true;
                this.text = resp.data.message;
              this.getvideoByReseller();
              this.$refs.form.reset();
          });
        }else{
          axios.post("/api/update/video", data, config)
            .then((resp) => {
              this.close();
                this.isLoading = false;
                this.MessageModel4 = true;
                this.text = resp.data.message;
              this.getvideo();
              this.$refs.form.reset();
          });
        }
        
      }else { // Add new sale
        const config = {
            headers: { 'content-type': 'multipart/form-data' }
        }
        let data = { 
          user_id: this.userData.user_id,
          plan_id: this.userData.plan_id,
          video_title: this.formData.video_title,
          video_description: this.formData.video_description,
          video_url: this.formData.video_url,
          youtube_link: this.formData.video_url,
          video_status: 1,
        };
        if(this.formData.video_title != '' && this.formData.video_description != '' && this.formData.video_url != ''){
          this.isLoading = true;
          if(this.userdata.id != null){
            axios.post("/api/add/videobyReseller", data, config)
              .then((resp) => {
                this.showModel4 = false;
                  this.MessageModel4 = true,
                  this.isLoading = false;
                  this.text = resp.data.message;
                this.getvideoByReseller();
                this.$refs.form.reset();
              }
            );
          }else{
            axios.post("/api/add/video", data, config)
              .then((resp) => {
                this.showModel4 = false;
                  this.MessageModel4 = true,
                  this.isLoading = false;
                  this.text = resp.data.message;
                this.getvideo();
                this.$refs.form.reset();
              }
            );
          }
        }
        this.$refs.form.validate();
      }
    }
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