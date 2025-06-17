<template>
  <div>
    <div class="row">
      <div class="col-3">
        <h3 class="fw-bold">Category</h3>
      </div>
      <div class="col-6">
        <v-text-field v-model="search" clearable append-icon="mdi-magnify" label="Search" single-line hide-details>
        </v-text-field>
      </div>
      <div class="col">
          <v-btn tile color="error" large @click="addDialog = true">
            <v-icon left>mdi-plus</v-icon>CREATE CATEGORY</v-btn>
      </div>
    </div>
    <div class="my-5">
      <v-data-table :search="search" :headers="headers" :items="category" class="elevation-1">
        <template v-slot:[`item.cat_img_url`]="{ item }">
          <img :src=(url+item.cat_img_url) style="width: 50px; height: 50px"/>
        </template>
        <template v-slot:top>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-dialog v-model="dialog" max-width="500px" persistent>
            <v-card>
              <v-card-title class="text-h5 grey lighten-2" style="justify-content:space-between;">{{ formTitle }}
                  <v-icon  @click="close" class="text-h5">mdi-close</v-icon>
              </v-card-title>
              <v-card-text>
                  <v-row>
                    <v-col cols="12" sm="6" md="6">
                      <v-file-input accept="image/*" label="Category Image" prepend-icon="mdi-camera" multiple
                                    small-chips v-model="files"
                                    @change="addFiles" v-on:click="isHidden = true"></v-file-input>
                      <v-row>
                        <v-col sm="4" md="6" v-for="(file, f) in files" :key="f">
                          <img :ref="'file'" class="img-fluid" width="100"
                               height="100" style="display:block">
                        </v-col>
                        <div v-if="!isHidden">
                          <img :src=(url+editedItem.cat_img_url) style="width: 100px;">
                        </div>
                      </v-row>
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field v-model="editedItem.cat_name" label="Category Name"></v-text-field>
                    </v-col>

                    <!-- <v-col cols="12" sm="6" md="6">
                      <v-select
                          v-model="editedItem.cat_feature"
                          :items="features"
                          item-text="text"
                          item-value="id"
                          label="Category Feature"></v-select>
                    </v-col> -->
                    <!-- <v-col cols="12" sm="6" md="6">
                      <v-text-field type="number" v-model="editedItem.cat_sort" label="Category Sort"></v-text-field>
                    </v-col> -->
                    <v-col cols="12" md="12" class="py-0">
                      <div>
                        <v-checkbox v-model="editedItem.cat_status" label="Active" value="1"></v-checkbox>
                      </div>
                      <div class="pb-2">
                        <v-btn v-if="isLoading" class="my-2 me-3" color="success">Loading...</v-btn>
                        <v-btn v-else @click="updateCategory" class="my-4 me-3" color="success">UPDATE</v-btn>
                        <v-btn class="my-4 me-3" color="error" @click="close">CANCEL</v-btn>
                      </div>
                    </v-col>
                  </v-row>
              </v-card-text>
            </v-card>
          </v-dialog>
          <v-dialog v-model="dialogDelete" max-width="500px">
            <v-card>
              <v-card-title class="text-h6">Are you sure you want to delete this item?</v-card-title>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error" @click="closeDelete">Cancel</v-btn>
                <v-btn v-if="isLoading" class="my-2 me-3" color="error">Loading...</v-btn>
                <v-btn v-else color="error" @click="deleteItemConfirm">OK</v-btn>
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
          <v-dialog v-model="mainCategoryMultidelete" max-width="500px">
            <v-card>
              <v-card-title class="text-h6">Are you sure you want to delete this item?</v-card-title>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="cred" @click="closeDelete">Cancel</v-btn>
                <v-btn v-if="isLoading" class="my-2 me-3 cred">Loading...</v-btn>
                <v-btn v-else class="cred" @click="deleteMultipleCategories">OK</v-btn>
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </template>
        <!-- <template v-slot:[`item.cat_feature`]="{ item }">
            <div v-if="item.cat_feature ==0 ">Normal</div>
            <div v-else-if="item.cat_feature == 1">Featured</div>
            <div v-else-if="item.cat_feature == 2">Top add</div>
        </template> -->
        <template v-slot:[`item.action`]="{ item }">
          <v-icon small class="mr-2" @click="editItem(item)">mdi-pencil</v-icon>
          <v-icon small @click="deleteItem(item)">mdi-delete</v-icon>
        </template>
        <template v-slot:[`item.cat_status`]="{ item }">
          <div v-if="item.cat_status === '1'">
            <span>Active</span>
          </div>
          <div v-else>
            <span>Inactive</span>
          </div>
        </template>
<!--        <template v-slot:footer>-->
<!--          <div class="text-muted p-3">-->
<!--            <v-btn color="grey" @click="getSelectedIds">DELETE SELECTED</v-btn>-->
<!--          </div>-->
<!--        </template>-->
      </v-data-table>
        <v-dialog v-model="addDialog" max-width="500px">
            <v-card>
                <v-card-title class="text-h5 grey lighten-2" style="justify-content:space-between;">{{ formTitle }}
                    <v-icon  @click="close" class="text-h5">mdi-close</v-icon>
                </v-card-title>
                <v-card-text class="pt-3">
                    <v-form @submit.prevent="formSubmit" ref="form" v-model="valid" lazy-validation>
                        <v-text-field
                            v-model="formData.cat_name"
                            :rules="catRules"
                            label="Category name"></v-text-field>
                        <v-file-input
                            v-model="formData.selectedFile"
                            :rules="filesRules"
                            label="Category Image"
                            accept="image/*"
                            @change="handleFileChange"
                        ></v-file-input>
                        <v-img v-if="previewImage" :src="previewImage" class="img-fluid" width="100" height="100"></v-img>
                        <!-- <v-select
                            v-model="formData.cat_feature"
                            :items="features"
                            item-text="text"
                            item-value="id"
                            label="Category Feature"></v-select> -->
                        <!-- <v-text-field
                            type="number"
                            v-model="formData.cat_sort"
                            label="Category Sort"></v-text-field> -->

                        <!-- <v-btn color="error" class="mr-4" @click="reset">CANCEL</v-btn>
                        <v-btn v-if="isLoading" class="mr-4 cred">Loading...</v-btn>
                        <v-btn v-else type="submit" :disabled="!valid" color="success" class="mr-4">SAVE</v-btn> -->
                        <v-col cols="12" sm="6" md="12" class="py-0 d-flex" style="justify-content: space-between;">
                            <div class="ms-3 pb-3">
                                <v-btn class="my-4 me-3" color="error" @click="reset">CANCEL</v-btn>
                                <v-btn v-if="isLoading" class="my-2 me-3" color="error">Loading...</v-btn>
                                <v-btn v-else type="submit" :disabled="!valid" class="my-4 me-3" color="success">SAVE</v-btn>
                            </div>
                        </v-col>
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
  </div>
</template>
<script>
import axios from 'axios';
export default {
    name: "MainCategories",
    components: {  },
    data: () => ({
        url:'https://bizlx.s3.ap-south-1.amazonaws.com',
        search: '',
        files: [],
        readers: [],
        dialog: false,
        valid: false,
        previewImage: null,
        addDialog: false,
        dialogDelete: false,
        headers: [
            { text: 'Image', value: 'cat_img_url'},
            { text: 'Title', value: 'cat_name', },
            // { text: 'features', value: 'cat_feature', },
            // { text: 'Sorting', value: 'cat_sort' },
            { text: 'Status', align: 'end', value: 'cat_status', sortable: false },
            { text: 'Actions', align: 'end', value: 'action', sortable: false },
        ],
        items: '',
        mainCategoryMultidelete: false,
        checkedIds: [],
        category: [],
        features: [
          {'id':'0', 'text':"Normal"},
          {'id':'1', 'text':"Feature"},
          {'id':'2', 'text':"Top add"},
        ],
        editedIndex: -1,
        editedItem: {
            cat_name: '',
            cat_img_url: '',
            cat_slug: '',
            cat_feature: '0',
            cat_sort: '1',
            cat_status: ''
        },
        defaultItem: {
            cat_name: '',
            cat_img_url: '',
            cat_slug: '',
            // cat_feature: '',
            // cat_sort: '',
            cat_status: ''
        },
        formData: {
            cat_name: '',
            selectedFile: ''  ,
            cat_feature: '0',
            cat_sort: '1'
        },
        catRules: [
            v => !!v || 'Category name is required.',
            v => v.length <= 50 || 'Name must be less than 50 characters.',
        ],
        filesRules: [
            v => !!v || 'Category image is required.',
        ],
        snackbar: false,
        text: '',
        timeout: '',
        isLoading: false,
        isHidden: false
    }),
    computed: {
        formTitle() {
            return this.editedIndex === -1 ? 'New Item' : 'Edit category'
        },
    },
    watch: {
        dialog(val) {
            val || this.close()
        },
        dialogDelete(val) {
            val || this.closeDelete()
        },
        mainCategoryMultidelete(val) {
            val || this.closeDelete();
        },
    },
    mounted() {
        this.getcategory();
    },
    methods: {
        addFiles(){
            this.files.forEach((file, f) => {
                this.readers[f] = new FileReader();
                this.readers[f].onloadend = () => {
                    let fileData = this.readers[f].result
                    let imgRef = this.$refs.file[f]
                    imgRef.src = fileData
                }
                if(this.files[f]){
                    this.readers[f].readAsDataURL(this.files[f]);
                }
            });
        },
        getcategory() { // all cat get
            axios.get("/api/category")
                .then((resp) => {
                    this.category = resp.data.categories
                }
            );
        },
        editItem(item) {
            this.editedIndex = this.category.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialog = true;
        },
        deleteItem(item) {
            this.editedIndex = this.category.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialogDelete = true
        },
        getSelectedIds() { // multiple delete modal open
          var deletedID = {
            ids:this.checkedIds.map(item => item.id)
          };
          if(deletedID.ids.length>0){
            this.mainCategoryMultidelete = true;
          }else{
            this.snackbar = true;
            this.text = "Please select category.";
            this.timeout = 3000;
          }
        },
        deleteMultipleCategories() { // multiple delete here
          this.isLoading = true;
          var deletedID = {
            ids:this.checkedIds.map(item => item.id)
          };
            axios.post('/api/admin/multiple/category/delete',deletedID)
              .then((resp) => {
                this.isLoading = false;
                this.mainCategoryMultidelete = false;
                  this.snackbar = true;
                  this.text = resp.data.message;
                  this.timeout = 3000;
                this.getcategory();
            });
        },
        deleteItemConfirm() {
    this.isLoading = true;
    axios.delete("/api/category/delete?id=" + this.editedItem.id)
        .then((resp) => {
            this.category.splice(this.editedIndex, 1); // move here
            this.closeDelete();
            this.isLoading = false;
            this.snackbar = true;
            this.text = resp.data.message;
            this.timeout = 3000;
        })
        .catch(() => {
            this.isLoading = false;
            this.snackbar = true;
            this.text = 'Failed to delete category.';
        });
},
        close() {
            this.dialog = false;
            this.addDialog = false;
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
        },
        closeDelete() {
            this.dialogDelete = false;
            this.mainCategoryMultidelete = false;
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
        },
        updateCategory() { // for update
            this.isLoading = true;
            const config = {
                headers: { 'content-type': 'multipart/form-data' }
            }
            let data = {
                cat_id: this.editedItem.id,
                cat_name: this.editedItem.cat_name,
                cat_img_url: this.files[0],
                cat_feature: this.editedItem.cat_feature,
                cat_sort: this.editedItem.cat_sort,
                cat_status: this.editedItem.cat_status
            };
            if (this.editedIndex > -1) {
                axios.post("/api/categoryedit", data, config)
                .then((resp) => {
                    this.isLoading = false;
                    this.close();
                    this.snackbar = true;
                    this.text = resp.data.message;
                    this.timeout = 2000;
                    this.getcategory();
                });
            } else {
                this.category.push(this.editedItem);
            }
        },
        formSubmit() {
            const config = {
                headers: { 'content-type': 'multipart/form-data' }
            }
            let data = {
                cat_name: this.formData.cat_name,
                selectedFile: this.formData.selectedFile,
                cat_feature: this.formData.cat_feature,
                cat_sort: this.formData.cat_sort,
            };
            if(this.formData.cat_name!='' && this.formData.selectedFile!=''){
                this.isLoading = true;
                axios.post("/api/category/add", data, config)
                    .then((resp) => {
                            this.isLoading = false;
                            this.snackbar = true;
                            this.text = resp.data.message;
                            this.timeout = 3000;
                            this.previewImage = null;
                            this.$refs.form.reset();
                        }
                    );
            }
            this.$refs.form.validate();
        },
        reset(){
            this.addDialog = false;
            this.previewImage = null;
            this.$refs.form.reset();
        },
        handleFileChange() {
            this.previewImage = URL.createObjectURL(this.formData.selectedFile);
        },
    },
}
</script>
