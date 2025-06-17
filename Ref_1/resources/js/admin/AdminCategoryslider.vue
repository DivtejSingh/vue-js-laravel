<template>
  <div>
    <div class="row">
      <div class="col-12 col-md-4"><h4>Category Slider</h4></div>
      <div class="col-12 col-md-6">
        <v-text-field v-model="search" clearable append-icon="mdi-magnify" label="Search" single-line hide-details></v-text-field>
      </div>
      <div class="col-12 col-md-2">
        <v-btn @click="openModel" tile color="error" large>
          <v-icon left>mdi-plus</v-icon>
          ADD NEW SLIDER
        </v-btn>
      </div>
    </div>
    <div class="row mt-10">
      <v-data-table :headers="headers" :search="search" :items="catslider" class="elevation-1">
        <template v-slot:top>
          <v-dialog v-model="dialog" persistent max-width="500px">
            <v-card>
              <v-card-title style="justify-content:space-between;" class="text-h6 grey lighten-2">Edit Category Slider
                <v-icon  @click="dialog = false" class="text-h5">mdi-close</v-icon>
              </v-card-title>
              <v-card-text class="p-0">
                <v-container>
                  <v-form ref="form" v-model="valid">
                    <v-row>
                      <v-col cols="12" md="12">
                        <v-autocomplete dense v-model="editedItem.subcat_id" item-value="id" item-text="subcat_name" :rules="catslide" :items="subcat"
                                        label="Select Sub Category"></v-autocomplete>
                        <div>
                          <v-checkbox dense v-model="editedItem.catslider_status" label="Active"></v-checkbox>
                        </div>
                        <v-card-actions>
                          <v-btn @click="editcatSlider" class="my-4 me-3" color="error" :disabled="!valid">UPDATE</v-btn>
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
                <v-btn color="error" text @click="deletecatSlider">OK</v-btn>
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
        <template v-slot:[`item.catslider_status`]="{ item }">
          <div v-if="item.catslider_status === 1">
            <span>Active</span>
          </div>
          <div v-else>
            <span>Inactive</span>
          </div>
        </template>
      </v-data-table>
    </div>
    <v-dialog v-model="showModel" persistent max-width="500px">
      <v-card>
        <v-card-title style="justify-content:space-between;" class="text-h6 grey lighten-2"> Add New Slider
          <v-icon  @click="showModel = false" class="text-h5">mdi-close</v-icon>
        </v-card-title>
        <v-card-text>
          <v-form ref="form" v-model="valid">
            <v-row>
              <v-col cols="12" md="12">
                <v-autocomplete dense :items="subcat" :rules="catslide" v-model="subcat_id" item-text="subcat_name"
                                item-value="id" label="Select Sub Category" clearable></v-autocomplete>
              </v-col>
              <div>
                <v-btn class="my-4 me-3" color="error" @click="AddcatSlider" :disabled="!valid">SAVE</v-btn>
                <v-btn @click="showModel = false" class="my-4 me-3" color="error">CLOSE</v-btn>
              </div>
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
  name: "CategorySlider",
  components: {},
  data: () => ({
    search: '',
    subcat_id: '',
    valid: false,
    snackbar: false,
    showModel: false,
    text: '',
    timeout: -1,
    dialog: false,
    dialogDelete: false,
    catslide: [
      v => !!v || 'Sub Category is required.',
    ],
    headers: [
      { text: 'Subcategory Name', align: 'start', value: 'subcat_name'},
      { text: "Status", value: "catslider_status", align: 'end', sortable: false },
      { text: 'Actions', value: 'actions', align: 'end', sortable: false},
    ],
    catslider: [],
    subcat: [],
    editedIndex: -1,
    editedItem: {
      id: '',
      subcat_id: '',
      catslider_status: '',
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

  mounted() {
    this.getSlider();
    this.getSubcategory();
  },

  methods: {
    getSlider() {
      axios.get('/api/admin/catsliders')
          .then((resp)=>{
            this.catslider = resp.data.catsliders;
          });
    },
    getSubcategory() {
      axios.get("/api/admin/subcategories")
          .then((resp) =>{
            this.subcat = resp.data.subcategories;
          });
    },
    AddcatSlider(){ // new add
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let data = {
        subcat_id: this.subcat_id,
        catslider_status: 1,
      };
      axios.post("/api/admin/add/catsliders",data,config)
          .then((resp) => {
            if(resp.data.status == 200){
              this.snackbar = true;
              this.text = resp.data.message;
              this.showModel = false;
              this.timeout = 3000;
              this.getSlider();
              this.$refs.form.reset();
            }else{
              this.snackbar = true;
              this.showModel = false;
              this.timeout = 3000;
              this.text = 'The job category Name has already been taken.';
            }
          })
      this.$refs.form.validate();
    },
    editcatSlider() {
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let data = {
        subcat_id: this.editedItem.subcat_id,
        catslider_status: this.editedItem.catslider_status,
      };
      if (this.editedIndex > -1) { // Edit
        axios.post("/api/admin/edit/catsliders/"+this.editedItem.id, data, config)
            .then((resp) => {
              this.snackbar = true;
              this.text = resp.data.message;
              this.timeout = 3000;
              this.getSlider();
              this.close();
              this.$refs.form.reset();
            })
            .catch(error=>{
              this.error = error;
              this.snackbar = true;
              this.text = "Something went wrong";
              this.timeout = 3000;
              this.getSlider();
            });
      }
      this.$refs.form.validate();
    },
    deletecatSlider(){
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let data = {
        catid: this.editedItem.id,
      };
      axios.post('/api/admin/delete/catsliders',data,config)
          .then((resp) =>{
            this.snackbar = true;
            this.timeout = 3000;
            this.text = resp.data.message;
            this.dialogDelete = false;
            this.getSlider();
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
      this.editedIndex = this.catslider.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },

    deleteItem(item) {
      this.editedIndex = this.catslider.indexOf(item)
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
<style scoped>
/*.v-data-table {*/
/*  line-height: 1.5;*/
/*  max-width: 60% !important;*/
/*  }*/
</style>
