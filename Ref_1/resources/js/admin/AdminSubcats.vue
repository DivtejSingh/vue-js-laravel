<template>
  <div>
    <div class="row">
      <div class="col-3">
        <h3 class="fw-bold">Sub Category</h3>
      </div>
      <div class="col-6">
        <v-text-field v-model="search" clearable append-icon="mdi-magnify" label="Search" single-line hide-details>
        </v-text-field>
      </div>
      <div class="col">
          <v-btn tile color="error" large @click="addDialog = true">
            <v-icon left>mdi-plus</v-icon>CREATE SUB CATEGORY</v-btn>
      </div>
    </div>
    <div class="my-5">
      <v-data-table :search="search" :headers="headers" :items="subcategories" class="elevation-1">
        <template v-slot:[`item.subcat_img_url`]="{ item }">
          <img :src=(url+item.subcat_img_url) style="width: 50px; height: 50px"/>
        </template>
        <template v-slot:top>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-dialog @closed="resetForm" v-model="dialog" max-width="700px" persistent>
            <v-card>
              <v-card-title class="text-h5 grey lighten-2" style="justify-content:space-between;">{{ formTitle }}
                  <v-icon  @click="close" class="text-h5">mdi-close</v-icon>
              </v-card-title>
              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12" sm="6" md="12" class="py-0">
                      <v-text-field dense
                            v-model="editedItem.subcat_name"
                            label="Sub category Name"></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="6" class="py-0">
                        <v-autocomplete dense
                          label="Select category"
                          v-model="editedItem.cat_id"
                          :items="mainCatSelect"
                          item-text="cat_name"
                          item-value="id">
                        </v-autocomplete>
                    </v-col>
                    <v-col cols="12" sm="6" md="6" class="py-0">
                      <v-file-input accept="image/*" dense
                            label="Category Image" prepend-icon="mdi-camera"
                            multiple small-chips
                            v-model="files"
                            @change="addFiles" v-on:click="isHidden = true">
                      </v-file-input>
                      <v-row>
                        <v-col sm="4" v-for="(file, f) in files" :key="f">
                          <img :ref="'file'" width="100" height="100" style="display:block">
                        </v-col>
                        <div v-if="!isHidden">
                          <img :src=(url+editedItem.subcat_img_url) width="100" height="100">
                        </div>
                      </v-row>
                    </v-col>
                    <v-col cols="12" sm="6" md="12">
                      <v-select
                          v-model="editedItem.subcat_feature"
                          label="Select Featured"
                          :items="feature" dense
                          item-value="status"
                          item-text="feature"
                      ></v-select>
                    </v-col>
                    <v-col cols="12" sm="6" md="12" class="py-0">
                        <div>
                          <v-checkbox v-model="editedItem.subcat_status" label="Active" dense></v-checkbox>
                        </div>
                        <div class="pb-2">
                          <v-btn v-if="isLoading" class="my-2 me-3" color="success">Loading...</v-btn>
                          <v-btn v-else @click="updateSubCategory" class="my-4 me-3" color="success">UPDATE</v-btn>
                          <v-btn class="my-4 me-3" color="error" @click="close">CANCEL</v-btn>
                        </div>
                    </v-col>
                  </v-row>
                </v-container>
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
          <v-dialog v-model="SubCategoryMultidelete" max-width="500px">
            <v-card>
              <v-card-title class="text-h6">Are you sure you want to delete this item?</v-card-title>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error" @click="closeDelete">Cancel</v-btn>
                <v-btn v-if="isLoading" class="my-2 me-3" color="success">Loading...</v-btn>
                <v-btn v-else color="error" @click="deleteMultipleSubCategories">OK</v-btn>
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </template>
        <template v-slot:[`item.action`]="{ item }">
          <v-icon small class="mr-2" @click="editItem(item)">mdi-pencil</v-icon>
          <v-icon small @click="deleteItem(item)">mdi-delete</v-icon>
        </template>
        <template v-slot:[`item.subcat_status`]="{ item }">
          <div v-if="item.subcat_status === 1">
            <span>Active</span>
          </div>
          <div v-else>
            <span>Inactive</span>
          </div>
        </template>
        <template v-slot:[`item.subcat_feature`]="{ item }">
          <div v-if="item.subcat_feature === 1" style="color:green">Yes</div>
          <div v-else class="mr-2 cred">No</div>
        </template>
<!--        <template v-slot:footer>-->
<!--          <div class="text-muted p-3">-->
<!--            <v-btn color="grey" @click="getSelectedIds">DELETE SELECTED</v-btn>-->
<!--          </div>-->
<!--        </template>-->
      </v-data-table>
    </div>
      <v-dialog v-model="addDialog" max-width="700px">
         <v-card>
             <v-card-title class="text-h5 grey lighten-2" style="justify-content:space-between;">{{ formTitle }}
                 <v-icon  @click="close" class="text-h5">mdi-close</v-icon>
             </v-card-title>
             <v-card-text class="pt-3">
                 <v-form ref="form" v-model="valid" lazy-validation>
                     <v-row>
                         <v-col cols="12" sm="6" md="12" class="py-0">
                             <v-text-field dense
                                 v-model="formData.subcat_name"
                                 label="Sub category Name"></v-text-field>
                         </v-col>
                         <v-col cols="12" sm="6" md="12" class="py-0">
                             <v-autocomplete dense
                                 label="Select category"
                                 v-model="formData.cat_id"
                                 :items="mainCatSelect"
                                 item-text="cat_name"
                                 item-value="id">
                             </v-autocomplete>
                         </v-col>
                         <v-col cols="12" sm="6" md="12" class="py-0">
                             <v-file-input dense
                                 v-model="formData.selectedFile"
                                 :rules="filesRules"
                                 label="Category Image"
                                 accept="image/*"
                                 @change="handleFileChange"
                                 v-on:click="isHidden = true"
                             ></v-file-input>
                             <v-img v-if="previewImage" :src="previewImage" class="img-fluid" width="100" height="100"></v-img>

                         </v-col>
                         <v-col cols="12" sm="6" md="12">
                             <v-select dense
                                 v-model="formData.subcat_feature"
                                 label="Select Featured"
                                 :items="feature"
                                 item-value="status"
                                 item-text="feature"
                             ></v-select>
                         </v-col>
                         <v-col cols="12" sm="6" md="12" class="py-0 d-flex" style="justify-content: space-between;">
                             <div class="ms-3 pb-3">
                                 <v-btn class="my-4 me-3" color="error" @click="close">CANCEL</v-btn>
                                 <v-btn v-if="isLoading" class="my-2 me-3" color="error">Loading...</v-btn>
                                 <v-btn v-else @click="AddSubCategory" class="my-4 me-3" color="success">SAVE</v-btn>
                             </div>
                         </v-col>
                     </v-row>
                 </v-form>
             </v-card-text>
         </v-card>
      </v-dialog>
    <v-snackbar v-model="snackbar" :timeout="timeout">{{ text }}
      <template v-slot:action="{}">
        <v-btn color="blue" text v-bind="attrs" @click="snackbar = false">Close</v-btn>
      </template>
    </v-snackbar>
  </div>
</template>
<script>
import axios from 'axios';
export default{
    name: "SubCategories",
    components: {  },
    data: () => ({
            url:'https://bizlx.s3.ap-south-1.amazonaws.com',
            search: '',
            dialog: false,
            addDialog: false,
            dialogDelete: false,
            isHidden: false,
            files: [],
            readers: [],
            headers: [
                { text: 'Image', value: 'subcat_img_url'},
                { text: 'Main Category', value: 'cat_name', },
                { text: 'Sub Category', value: 'subcat_name' },
                { text: 'Featured', value: 'subcat_feature' },
                { text: 'Status', align: 'end', value: 'subcat_status', sortable: false },
                { text: 'Actions', align: 'end', value: 'action', sortable: false },
            ],
            checkedIds: [],
            subcategories: [],
            mainCatSelect: [],
            editedIndex: -1,
            editedItem: {
                subCat_id:'',
                cat_id:'',
                subcat_name: '',
                subcat_img_url: '',
                subcat_status: '',
                subcat_feature: '',
            },
            defaultItem: {
                subcat_name: '',
                subcat_img_url: '',
                subcat_status: ''
            },
            formData: {
                subcat_name: '',
                cat_id: '',
                subcat_feature: '',
                subcat_status: '',
                selectedFile: ''
            },
            feature:[{status:1, feature:'Popular'},{status:0, feature:'Not Popular'}],
            items: '',
            SubCategoryMultidelete: false,
            snackbar: false,
            text: '',
            timeout: '',
            isLoading: false,
        }),
    computed: {
        formTitle() {
            return this.editedIndex === -1 ? 'Add New Category' : 'Edit Subcategory'
        },
    },
    watch: {
        dialog(val) {
            val || this.close()
        },
        dialogDelete(val) {
            val || this.closeDelete()
        },
        SubCategoryMultidelete(val) {
            val || this.closeDelete();
        },
    },
    created() {
        this.getSubcategory();
        this.mainCategoriesForSelect();
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
                this.readers[f].readAsDataURL(this.files[f]);
            })
        },
        getSubcategory() {
            axios.get("/api/subcategory")
            .then((resp) =>{
                this.subcategories =  resp.data.subcategories
            });
        },
        mainCategoriesForSelect() {
            axios.get("/api/admin/allcat/drop")
            .then((resp) =>{
                this.mainCatSelect = resp.data.maincategories_drop;
            });
        },
        editItem(item) {
            this.editedIndex = this.subcategories.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialog = true;
        },

        deleteItem(item) {
            this.editedIndex = this.subcategories.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialogDelete = true
        },
        close() {
            this.dialog = false;
            this.addDialog = false;
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
        },
        resetForm(){
            this.subcategories = {};
        },
        closeDelete() {
            this.dialogDelete = false;
            this.SubCategoryMultidelete = false;
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
        },
        deleteItemConfirm() { // Single delete
            this.isLoading = true;
            axios.get("/api/admin/subcategory/delete/"+this.editedItem.id)
                .then((resp) => {
                  this.closeDelete();
                    this.isLoading = false;
                    this.snackbar = true;
                    this.text = resp.data.message;
                    this.timeout = 3000;
                }
            );
            this.subcategories.splice(this.editedIndex, 1)
        },
        getSelectedIds() { // multiple delete modal open
          var deletedID = {
            ids:this.checkedIds.map(item => item.id)
          };
          if(deletedID.ids.length>0){
            this.SubCategoryMultidelete = true;
          }else{
            this.snackbar = true;
            this.text = "Please select sub-category.";
            this.timeout = 3000;
          }
        },
        deleteMultipleSubCategories() { // multiple delete here
          this.isLoading = true;
          var deletedID = {
            ids:this.checkedIds.map(item => item.id)
          };
            axios.post('/api/admin/multiple/subcategory/delete', deletedID)
              .then((resp) => {
                this.isLoading = false;
                this.SubCategoryMultidelete = false;
                  this.snackbar = true;
                  this.text = resp.data.message;
                  this.timeout = 3000;
                  this.getSubcategory();
            });
        },
        updateSubCategory() {
          this.isLoading = true;
          const config = {
              headers: { 'content-type': 'multipart/form-data' }
          }
          let data = {
              subCat_id: this.editedItem.id,
              cat_id: this.editedItem.cat_id,
              subcat_name: this.editedItem.subcat_name,
              subcat_feature: this.editedItem.subcat_feature,
              subcat_status: this.editedItem.subcat_status,
              new_subCat_img_url : this.files[0],
          };

          if (this.editedIndex > -1) {
              axios.post("/api/subcategory/edit/"+this.editedItem.id, data, config)
              .then((resp) => {
                this.isLoading = false;
                  this.snackbar = true;
                  this.text = resp.data.message;
                  this.timeout = 3000;
                this.getSubcategory();
                this.close();
              });
          } else {
              this.category.push(this.editedItem);
          }
        },
        AddSubCategory(){
            this.isLoading = true;
            const config = {
                headers: { 'content-type': 'multipart/form-data' }
            }
            let data = {
                subcat_name: this.formData.subcat_name,
                cat_id: this.formData.cat_id,
                subcat_feature: this.formData.subcat_feature,
                subcat_status: 1,
                new_subCat_img_url : this.formData.selectedFile,
            };
            if(this.formData.subcat_name!='' && this.formData.cat_id!=='' && this.formData.selectedFile){
                axios.post("/api/admin/subcategory/add", data, config)
                    .then((resp) =>{
                        this.isLoading = false;
                        this.snackbar = true;
                        this.text = resp.data.message;
                        this.timeout = 3000;
                        this.previewImage = null;
                        this.$refs.form.reset();
                        this.getSubcategory();
                    });
            }else{
                this.snackbar = true;
                this.text = "Please fill all inputs.";
                this.timeout = 3000;
                this.isLoading = false;
            }
        }
    },
}
</script>
