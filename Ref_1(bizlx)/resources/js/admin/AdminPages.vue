<template>
  <div>
    <div class="row">
      <div class="col-3">
        <h3 class="fw-bold">Pages</h3>
      </div>
      <div class="col-6">
          <v-text-field v-model="search" clearable dense outlined hide-details></v-text-field>
      </div>
      <div class="col-3 test-decoration-none">
          <v-btn tile color="error" large @click="npageDialog = true">
              <v-icon left>
                  mdi-plus
              </v-icon>
              CREATE PAGE
          </v-btn>
      </div>
    </div>
    <div class=" my-3">
      <v-data-table :headers="headers" :items="desserts" sort-by="page_name" :search="search" class="elevation-1">
        <template v-slot:top>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-dialog v-model="dialogDelete" max-width="500px">
            <v-card>
              <v-card-title class="text-h6">Are you sure you want to delete this
                page?
              </v-card-title>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
                <v-btn color="blue darken-1" text @click="deleteItemConfirm">OK</v-btn>
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
          <v-dialog v-model="deletemultiplepages" max-width="500px">
              <v-card>
                <v-card-title class="text-h6">Are you sure you want to delete these pages?</v-card-title>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn  class="cred" text @click="closeMultiDailoge">Cancel</v-btn>
                  <v-btn v-if="isLoading" class="cred">Loading...</v-btn>
                  <v-btn v-else class="cred" text @click="multiplepageIds" >OK</v-btn>
                  <v-spacer></v-spacer>
                </v-card-actions>
              </v-card>
            </v-dialog>
        </template>
        <template v-slot:[`item.action`]="{ item }">
          <v-icon small class="mr-2" @click="editItem(item)">
            mdi-pencil
          </v-icon>
          <v-icon small @click="deleteItem(item)"> mdi-delete</v-icon>
        </template>
        <template v-slot:[`item.active`]="{ item }">
          <v-icon small class="mr-2" @click="editItem(item)">
            mdi-check
          </v-icon>
        </template>
        <template v-slot:[`item.status`]="{ item }">
          <div v-if="item.status === '1'">
            <span>Active</span>
          </div>
          <div v-else>
            <span>Inactive</span>
          </div>
        </template>
<!--        <template v-slot:footer>-->
<!--          <div class="text-muted p-3">-->
<!--            <v-btn  color="grey" @click="getSelectedIds">DELETE SELECTED</v-btn>-->
<!--          </div>-->
<!--        </template>-->
      </v-data-table>
      <v-snackbar v-model="snackbar" :timeout="timeout">{{ text }}
        <template v-slot:action="{attrs}">
          <v-btn color="blue" text v-bind="attrs" @click="snackbar = false">Close</v-btn>
        </template>
      </v-snackbar>
    </div>
      <v-dialog v-model="dialog" max-width="700">
          <v-card>
              <v-card-title class="error white--text">Edit Page</v-card-title>
              <v-card-text class="pt-5">
                  <v-form ref="form" v-model="valid" lazy-validation>
                    <v-text-field label="Page Title" v-model="editedItem.page_title"></v-text-field>

                            <v-textarea
                              label="Page Description"
                              v-model="editedItem.page_des"
                              rows="3"
                              auto-grow
                            
                            ></v-textarea>    

                            <v-file-input
                              label="Page Image"
                              v-model="editedItem.page_ogimage"
                              accept="image/*"
                              show-size
                            ></v-file-input>

                      <v-autocomplete label="Column" v-model="editedItem.column_id" :items="columns" item-text="columns_name" item-value="id" autocomplete="on" ></v-autocomplete>

                      <v-text-field label="Page Name" v-model="editedItem.page_name"></v-text-field>
                      <!-- <v-textarea
                      v-model="editedItem.content"
                      label="Page Content"
                      auto-grow
                      outlined
                      rows="5"
                    ></v-textarea> -->
                    <v-btn small color="primary" class="mb-2" @click="showSource = !showSource">
                        {{ showSource ? 'Hide Source' : 'View Source' }}
                      </v-btn>

                      <vue-editor
                        v-if="!showSource"
                        v-model="editedItem.content"
                        :editorToolbar="customToolbar"
                        class="mt-3"
                      />

                      <textarea
                        v-if="showSource"
                        v-model="editedItem.content"
                        class="form-control"
                        rows="10"
                        style="font-family: monospace;"
                      ></textarea>


                      <v-switch v-model="editedItem.status" label="Active" value="1"></v-switch>

                      <!-- <v-switch v-model="editedItem.page_status" label="Static" @click="toggleTextField" value="1"></v-switch> -->

                      <!-- <div v-if="editedItem.page_status == '1'"> -->
                      <!-- <v-text-field label="Page Link" v-model="editedItem.page_link" v-if="showTextField"></v-text-field> -->
                      <!-- </div> -->
                      <!-- <div v-if="editedItem.page_status == '0'"> -->
                      <v-text-field label="Page Link" v-model="editedItem.page_link"></v-text-field>
                      <!-- </div> -->

                      <v-btn color="success" class="mr-4" @click="updatePageDetails">
                          UPDATE
                      </v-btn>
                      <v-btn color="error" class="mr-4" @click="close">
                          CANCEL
                      </v-btn>
                  </v-form>
              </v-card-text>
          </v-card>
      </v-dialog>
      <v-dialog v-model="npageDialog" max-width="700">
          <v-card>
              <v-card-title class="error white--text">Add Page</v-card-title>
              <v-card-text class="pt-5">
                  <v-form ref="form" v-model="valid" lazy-validation>

                      <v-autocomplete label="Column" v-model="formData.column" :items="columns"
                                      item-text="columns_name" item-value="id" autocomplete="on" dense clearable></v-autocomplete>

                      <v-text-field label="Page Name" v-model="formData.page_name" dense></v-text-field>
                      <!-- <v-textarea
                          v-model="formData.content"
                          label="Page Content"
                          auto-grow
                          rows="5"
                          dense
                          outlined
                          class="mt-3"
                        /> -->

                        <v-btn small color="primary" class="mb-2" @click="showSource = !showSource">
                            {{ showSource ? 'Hide Source' : 'View Source' }}
                          </v-btn>

                          <vue-editor
                            v-if="!showSource"
                            v-model="formData.content"
                            :editorToolbar="customToolbar"
                            class="mt-3"
                          />

                          <textarea
                            v-if="showSource"
                            v-model="formData.content"
                            class="form-control"
                            rows="10"
                            style="font-family: monospace;"
                          ></textarea>


                      <v-switch v-model="formData.status" label="Active" dense></v-switch>

                      <!-- <v-switch v-model="formData.page_status" label="Static"></v-switch> -->

                      <v-text-field class="mb-2 custom-hint"  label="Page Link" hint="/pagelink"
                                    persistent-hint v-model="formData.page_link" dense></v-text-field>

                      <v-btn color="success" class="mr-4" @click="savepage">
                          SAVE
                      </v-btn>
                      <v-btn color="error" class="mr-4" @click="resetForm">
                          CANCEL
                      </v-btn>
                  </v-form>
              </v-card-text>
          </v-card>
      </v-dialog>
  </div>

</template>

<script>
import { VueEditor } from "vue2-editor";
import axios from 'axios';
export default{
    name: "PagesView",
    components: {
      VueEditor,
      },
      // showSource: false,


    data: () => ({
      showSource: false,
      customToolbar: [
      [{ 'font': [] }],
      [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
      ['bold', 'italic', 'underline', 'strike'],
      [{ 'color': [] }, { 'background': [] }],
      [{ 'script': 'sub' }, { 'script': 'super' }],
      [{ 'list': 'ordered' }, { 'list': 'bullet' }],
      [{ 'indent': '-1' }, { 'indent': '+1' }],
      [{ 'align': ['', 'center', 'right', 'justify'] }],

      ['blockquote', 'code-block'],
      ['link', 'image', 'video'],
      ['clean']
    ],
        valid: false,
        dialog: false,
        dialogDelete: false,
        npageDialog: false,
        checkedIds:[],
        search:'',
        headers: [
            { text: "Title", value: "page_name" },
            { text: "Page Link", value: "page_link" },
            { text: "Status", align: 'end', value: "status" },
            { text: "Actions", align: 'end', value: "action", sortable: false },
        ],
        desserts: [],
        editedIndex: -1,
        editedItem: {
          id: "",
          column_id: "",
          page_name: "",
          page_title: "",
          page_des: "",
          page_ogimage: "",
          status: "",
          page_status: "",
          content:""
        },
      url:'https://bizlx.s3.ap-south-1.amazonaws.com',

        defaultItem: {
            title: "",
        },
        snackbar: false,
        text: '',
        timeout: '',
        isLoading:false,
        deletemultiplepages:false,
        columns:[],
        formData:{
            column:'',
            page_name:'',
            status:'',
            page_status:'',
            content: '',
        },
    }),

    computed: {
        formTitle() {
            return this.editedIndex === -1 ? "New Item" : "Edit Item";
        },
    },
    watch: {
        dialog(val) {
            val || this.close();
        },
        dialogDelete(val) {
            val || this.closeDelete();
        },
    },

    created() {
        this.initialize();
        this.getColumns();
    },
    methods: {
        initialize() {
          axios.get("/api/all/pages").then( (resp) => {
            this.desserts = resp.data.pages;
          });
        },
        // editItem(item) {
        //     this.editedIndex = this.desserts.indexOf(item);
        //     this.editedItem = Object.assign({}, item);
        //     this.dialog = true;
        // },
        editItem(item) {
  this.editedIndex = this.desserts.indexOf(item);
  this.editedItem = Object.assign({}, item); // base values
  this.dialog = true;

  // Fetch page content and explicitly set editedItem again
  axios.get(`/api/page/content?id=${item.id}`).then((resp) => {
    if (resp.data.success) {
      // Merge content back in a reactive way
      this.editedItem = {
        ...this.editedItem,
        content: resp.data.content
      };
    } else {
      this.editedItem = {
        ...this.editedItem,
        content: ''
      };
    }
  });
},
        deleteItem(item) {
            this.editedIndex = this.desserts.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialogDelete = true;
        },
        deleteItemConfirm() {
            this.desserts.splice(this.editedIndex, 1);
            this.closeDelete();
            var data = {
              id : this.editedItem.id
            };

            axios.post("/api/delete/page",data).then( (resp) => {
              this.snackbar = true;
              this.text = resp.data.message;
              this.closeDelete();
              this.initialize();
              this.timeout = 3000;
            });
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
                Object.assign(this.desserts[this.editedIndex], this.editedItem);
            } else {
                this.desserts.push(this.editedItem);
            }
            this.close();
        },
       closeMultiDailoge(){
          this.deletemultiplepages = false;
        },
        multiplepageIds(){
          this.isLoading = true;
          var data = {
            ids:this.checkedIds.map(item => item.id)
          };
          axios.post('/api/delete/multiple/pages', data)
            .then((resp) => {
              if(resp.data.success == true){
                this.isLoading = false;
                this.initialize();
                this.deletemultiplepages = false;
                this.snackbar = true;
                this.text = resp.data.message;
                this.timeout = 3000;
              }else{
                this.isLoading = false;
                this.deletemultiplepages = false;
                this.initialize();
                this.snackbar = true;
                this.text = 'something went wrong';
                this.timeout = 3000;
              }
            });
        },
        validate() {
            this.$refs.form.validate();
        },
        resetForm(){
            this.npageDialog = false;
            this.$refs.form.reset();
        },
        getColumns(){
            axios.get("/api/get/columns").then( (resp) => {
                this.columns = resp.data.columns
            })
        },
        // updatePageDetails(){
        //     var data = {
        //         page_id: this.editedItem.page_id,
        //         column_id : this.editedItem.column_id,
        //         page_name : this.editedItem.page_name,
        //         page_link : this.editedItem.page_link,
        //         status : this.editedItem.status,
        //         page_status : this.editedItem.page_status
        //     };

        //     axios.post("/api/update/page",data).then( (resp)=> {
        //         this.snackbar = true;
        //         this.text = resp.data.message;
        //         this.timeout = 3000;
        //         this.dialog = false;
        //         this.initialize();
        //     })
        // },
       
//         updatePageDetails() {
//   var data = {
//     id: this.editedItem.id,
//     column_id: this.editedItem.column_id,
//     page_name: this.editedItem.page_name,
//     page_link: this.editedItem.page_link,
//     page_title: this.editedItem.page_title,
//     page_des: this.editedItem.page_des,
//     page_ogimage: this.editedItem.page_ogimage,
//     status: this.editedItem.status,
//     page_status: this.editedItem.page_status,
//     content: this.editedItem.content // add this line
//   };

//   axios.post("/api/update/page", data).then((resp) => {
//     this.snackbar = true;
//     this.text = resp.data.message;
//     this.timeout = 3000;
//     this.dialog = false;
//     this.initialize();
//   });
// },
updatePageDetails() {
  const formData = new FormData();
  formData.append("id", this.editedItem.id);
  formData.append("column_id", this.editedItem.column_id);
  formData.append("page_name", this.editedItem.page_name);
  formData.append("page_link", this.editedItem.page_link);
  formData.append("page_title", this.editedItem.page_title);
  formData.append("page_des", this.editedItem.page_des);
  formData.append("status", this.editedItem.status ? 1 : 0);
  formData.append("page_status", this.editedItem.page_status ? 1 : 0);
  formData.append("content", this.editedItem.content);

  // Append image file if selected
  if (this.editedItem.page_ogimage instanceof File) {
    formData.append("page_ogimage", this.editedItem.page_ogimage);
  }

  // Optional: add your token if needed
  formData.append("token", localStorage.getItem("token")); // or however you store your JWT

  axios
    .post("/api/update/page", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    })
    .then((resp) => {
      this.snackbar = true;
      this.text = resp.data.message;
      this.timeout = 3000;
      this.dialog = false;
      this.initialize();
    });
},

savepage() {
  const data = {
    column_id: this.formData.column,
    page_name: this.formData.page_name,
    page_link: this.formData.page_link,
    status: this.formData.status,
    page_status: this.formData.page_status,
    content: this.formData.content // send content
  };

  axios.post("/api/save/page", data).then((resp) => {
    this.snackbar = true;
    this.text = resp.data.message;
    this.timeout = 3000;
    this.$refs.form.reset();
    this.npageDialog = false;
    this.initialize();
  });
},
    }
  }
</script>
