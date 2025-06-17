<template>
    <div>
      <div class="row mb-2">
        <div class="col-3">
          <h4 class="fw-bold">Plans</h4>
        </div>
        <div class="col-7">
          <v-text-field v-model="search" clearable  append-icon="mdi-magnify" label="Search" placeholder="Search" single-line hide-details size="lg"></v-text-field>
        </div>
        <div class="col-2">
            <v-btn tile color="error" large @click="addDialog = true">
              <v-icon left>mdi-plus</v-icon>CREATE PLAN
            </v-btn>
        </div>
      </div>
        <v-data-table :search="search" :headers="headers" :items="plans" class="elevation-1">
          <template v-slot:top>
            <v-dialog v-model="dialogDelete" max-width="500px">
              <v-card>
                <v-card-title class="text-h6">Are you sure you want to delete this plan</v-card-title>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
                  <v-btn color="blue darken-1" text @click="deleteplan">OK</v-btn>
                  <v-spacer></v-spacer>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </template>

          <template v-slot:[`item.action`]="{ item }">
              <v-icon small class="mr-2" @click="editItem(item)">mdi-pencil</v-icon>
              <v-icon small class="mr-2" @click="deleteItem(item)"> mdi-delete</v-icon>
          </template>
          <template v-slot:[`item.plan_status`]="{ item }">
            <div v-if="item.plan_status == '1'">
              <span>Active</span>
            </div>
            <div v-else>
              <span>Inactive</span>
            </div>
          </template>
          <template v-slot:[`item.about`]="{ item }">
            <div v-if="item.about === 1">
              <v-icon small class="mr-2">
                mdi-check
              </v-icon>
            </div>
            <div v-else>
              <v-icon small class="mr-2">
                mdi-close
              </v-icon>
            </div>
          </template>
          <template v-slot:[`item.services`]="{ item }">
            <div v-if="item.services === 1">
              <v-icon small class="mr-2">
                mdi-check
              </v-icon>
            </div>
            <div v-else>
              <v-icon small class="mr-2">
                mdi-close
              </v-icon>
            </div>
          </template>
          <template v-slot:[`item.contact`]="{ item }">
            <div v-if="item.contact === 1">
              <v-icon small class="mr-2">
                mdi-check
              </v-icon>
            </div>
            <div v-else>
              <v-icon small class="mr-2">
                mdi-close
              </v-icon>
            </div>
          </template>
          <template v-slot:[`item.reviews`]="{ item }">
            <div v-if="item.reviews === 1">
              <v-icon small class="mr-2">
                mdi-check
              </v-icon>
            </div>
            <div v-else>
              <v-icon small class="mr-2">
                mdi-close
              </v-icon>
            </div>
          </template>
          <template v-slot:[`item.featured_city`]="{ item }">
            <div v-if="item.featured_city === 1">
              <v-icon small class="mr-2">
                mdi-check
              </v-icon>
            </div>
            <div v-else>
              <v-icon small class="mr-2">
                mdi-close
              </v-icon>
            </div>
          </template>
          <template v-slot:[`item.featured_category`]="{ item }">
            <div v-if="item.featured_category === 1">
              <v-icon small class="mr-2">
                mdi-check
              </v-icon>
            </div>
            <div v-else>
              <v-icon small class="mr-2">
                mdi-close
              </v-icon>
            </div>
          </template>
          <template v-slot:[`item.home_page_banner`]="{ item }">
            <div v-if="item.home_page_banner === 1">
              <v-icon small class="mr-2">
                mdi-check
              </v-icon>
            </div>
            <div v-else>
              <v-icon small class="mr-2">
                mdi-close
              </v-icon>
            </div>
          </template>
        </v-data-table>
        <v-dialog v-model="addDialog" max-width="800">
            <v-card>
                <v-card-text class="pt-5">
                    <v-form ref="form" v-model="valid" lazy-validation>
                        <v-card-title class="h6 p-0">GENERAL</v-card-title>

                        <v-text-field label="Name (required)" v-model="plandata.plan_description" :rules="nameRules" required></v-text-field>

                        <v-text-field type="number" label="Plan Expiry (In days)" v-model="plandata.plan_expiry" :rules="expiryRules" required></v-text-field>

                        <v-text-field type="number" label="Price (required)" v-model="plandata.plan_price" :rules="priceRules" required></v-text-field>

                        <v-card-title class="h6 p-0">LIMITATIONS</v-card-title>

                        <v-text-field type="number" label="Images (required)" v-model="plandata.images" :rules="imagesRules" required></v-text-field>

                        <v-row class="mb-2">
                            <v-col cols="12" sm="4" md="3">
                                <v-text-field type="number" label="Hot Deals" v-model="plandata.hot_deals" required></v-text-field>
                                <v-checkbox v-model="plandata.reviews" label="Reviews" hide-details></v-checkbox>
                                <v-checkbox v-model="plandata.services" label="Services" hide-details></v-checkbox>
                            </v-col>
                            <v-col cols="12" sm="4" md="3">
                                <v-text-field type="number" label="Video" v-model="plandata.video" required></v-text-field>
                                <v-checkbox v-model="plandata.contact" label="Contact" hide-details></v-checkbox>
                                <v-checkbox v-model="plandata.about" label="About" hide-details></v-checkbox>
                            </v-col>
                            <v-col cols="12" sm="4" md="3">
                                <v-text-field type="number" label="Sale" v-model="plandata.sale" required></v-text-field>
                                <v-checkbox v-model="plandata.featured_city" label="Featured City" hide-details></v-checkbox>
                                <v-checkbox v-model="plandata.featured_category" label="Featured Category" hide-details></v-checkbox>
                            </v-col>
                            <v-col cols="12" sm="4" md="3">
                                <v-text-field type="number" label="Jobs" v-model="plandata.jobs" required></v-text-field>
                                <v-checkbox v-model="plandata.home_page_banner" label="Home Page Banner" hide-details></v-checkbox>
                            </v-col>
                        </v-row>

                        <v-btn @click="addPlan" :disabled="!valid" color="success" class="mr-4" >
                            SAVE
                        </v-btn>
                        <v-btn color="error" class="mr-4" @click="reset">
                            CANCEL
                        </v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>
        <v-dialog v-model="dialog" max-width="800">
           <v-card>
               <v-card-text class="pt-5">
                   <v-form ref="form" v-model="valid" lazy-validation>
                       <v-card-title class="h6 p-0">GENERAL</v-card-title>
                       <v-text-field label="Name (required)" v-model="editedItem.plan_description" :rules="nameRules" required></v-text-field>
                       <v-row>
                           <v-col cols="12" md="6">
                               <v-text-field type="number" label="Plan Expiry (In days)" v-model="editedItem.plan_expiry" :rules="expiryRules" required></v-text-field>
                           </v-col>
                           <v-col cols="12" md="6">
                               <v-text-field type="number" label="Price (required)" v-model="editedItem.plan_price" color="red" required></v-text-field>
                           </v-col>
                       </v-row>
                       <v-card-title class="h6 p-0">LIMITATIONS</v-card-title>
                       <v-text-field type="number" label="Image (required)" v-model="editedItem.images" :rules="imagesRules" required></v-text-field>
                       <v-row class="mb-2">
                           <v-col cols="12" sm="4" md="3">
                               <v-text-field type="number" label="Hot Deals" v-model="editedItem.hot_deals" required></v-text-field>
                               <v-checkbox v-model="editedItem.reviews" label="Reviews" hide-details></v-checkbox>
                               <v-checkbox v-model="editedItem.services" label="Services" hide-details></v-checkbox>
                           </v-col>
                           <v-col cols="12" sm="4" md="3">
                               <v-text-field type="number" label="Video" v-model="editedItem.video" required></v-text-field>
                               <v-checkbox v-model="editedItem.contact" label="Contact" hide-details></v-checkbox>
                               <v-checkbox v-model="editedItem.about" label="About" hide-details></v-checkbox>
                           </v-col>
                           <v-col cols="12" sm="4" md="3">
                               <v-text-field type="number" label="Sale" v-model="editedItem.sale" required></v-text-field>
                               <v-checkbox v-model="editedItem.featured_city" label="Featured City" hide-details></v-checkbox>
                               <v-checkbox v-model="editedItem.featured_category" label="Featured Category" hide-details></v-checkbox>
                           </v-col>
                           <v-col cols="12" sm="4" md="3">
                               <v-text-field type="number" label="Jobs" v-model="editedItem.jobs" required></v-text-field>
                               <v-checkbox v-model="editedItem.home_page_banner" label="Home Page Banner" hide-details></v-checkbox>
                           </v-col>
                       </v-row>
                       <v-checkbox v-model="editedItem.plan_status" label="Active" hide-details class="mb-4" value="1"></v-checkbox>
                       <v-btn @click="Editplan" :disabled="!valid" color="success" class="mr-4" >UPDATE</v-btn>
                       <v-btn color="error" class="mr-4" @click="close">CANCEL</v-btn>
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

export default{
    name: "AdminPlans",
    data: () => ({
        search: '',
        dialog: false,
        addDialog: false,
        singleSelect: false,
        dialogDelete: false,
        valid: true,
        snackbar: false,
        text: '',
        timeout: -1,
        headers: [
          { text: "Name", align: "start", sortable: false, value: "plan_description", width: 50},
          { text: "Price", align: "start", value: "plan_price", width: 50},
          { text: "Images", align: "start", sortable: false, value: "images", width: 50},
          { text: "Hot Deals", align: "center", sortable: false, value: "hot_deals", width: 100},
          { text: "Sale", align: "center", sortable: false, value: "sale", width: 50},
          { text: "Video", align: "center", sortable: false, value: "video",width: 50},
          { text: "About", align: "center", sortable: false, value: "about",width: 50},
          { text: "Services", align: "center", sortable: false, value: "services",width: 50},
          { text: "Jobs", align: "center", sortable: false, value: "jobs",width: 50},
          { text: "Contact", align: "center", sortable: false, value: "contact",width: 50},
          { text: "Reviews", align: "center", sortable: false, value: "reviews",width: 50},
          { text: "Featured City", sortable: false, align: "center", value: "featured_city",width: 110},
          { text: "Featured Category", sortable: false, align: "center", value: "featured_category",width: 140},
          { text: "Home Page Banner", sortable: false, align: "center", value: "home_page_banner",width: 150},
          { text: "Users", sortable: false, align: "center", value: "users",width: 50},
          { text: "Status", value: "plan_status", sortable: false ,width: 50},
          { text: "Actions", value: "action", sortable: false, width: 100},
        ],
        plans: [],
        editedIndex: -1,
        editedItem: {
              id: '',
              plan_description: '',
              plan_expiry: '',
              plan_price: '',
              plan_status: '1',
              images: '',
              hot_deals: '',
              about: '',
              video: '',
              services: '',
              contact: '',
              reviews: '',
              sale: '',
              jobs: '',
              featured_city: '',
              featured_category: '',
              home_page_banner: '',
          },
        plandata:{
            plan_description: '',
            plan_expiry: '',
            plan_price: '',
            plan_status: '1',
            images: '',
            hot_deals: '',
            about: '',
            video: '',
            services: '',
            contact: '',
            reviews: '',
            sale: '',
            jobs: '',
            featured_city: '',
            featured_category: '',
            home_page_banner: '',
        },
            nameRules: [
                v => !!v || 'The name field is required.',
            ],
            expiryRules: [
                v => !!v || 'The plan expiry field is required.',
            ],
            priceRules: [
                v => !!v || 'The Price field is required.',
            ],
            imagesRules: [
                v => !!v || 'The Images field is required.',
            ],
  }),
  watch: {
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
  },

  created() {
    this.getPlans();
  },

  methods: {
      reset(){
          this.$refs.form.reset();
          this.addDialog = false;
      },
    getPlans(){
      axios.get('/api/admin/plans')
          .then((resp)=>{
            this.plans = resp.data.plans;
          });
    },
      editItem(item){
          this.editedIndex = this.plans.indexOf(item);
          this.editedItem = Object.assign({}, item);
          this.dialog = true;
      },
      Editplan() {
          const config = {
              headers: { 'content-type': 'multipart/form-data' }
          }
          let data = {
              plan_description: this.editedItem.plan_description,
              plan_expiry: this.editedItem.plan_expiry,
              plan_price: this.editedItem.plan_price,
              plan_status: this.editedItem.plan_status,
              images: this.editedItem.images,
              hot_deals: this.editedItem.hot_deals,
              video: this.editedItem.video,
              services: this.editedItem.services,
              contact: this.editedItem.contact,
              reviews: this.editedItem.reviews,
              sale: this.editedItem.sale,
              jobs: this.editedItem.jobs,
              about: this.editedItem.about,
              featured_city: this.editedItem.featured_city,
              featured_category: this.editedItem.featured_category,
              home_page_banner: this.editedItem.home_page_banner,
          };
          axios.post(`/api/admin/edit/plan/${this.editedItem.id}`,data,config)
              .then((resp) => {
                  if(resp.data.message) {
                      this.snackbar = true;
                      this.text = resp.data.message;
                      this.timeout = 3000;
                      this.getPlans();
                  }
              })
              .catch(error => {
                  // Handle the error
                  var data = error.toJSON();
                  if(data.status == 400){
                      this.snackbar = true;
                      this.text = "something went wrong.";
                      this.timeout = 3000;
                  }
              });
          this.$refs.form.validate();
      },
      addPlan() {
          const config = {
              headers: { 'content-type': 'multipart/form-data' }
          }
          let data = {plan_status: 1,};
          axios.post("/api/admin/add/plan",this.plandata,data,config)
              .then((resp) => {
                  if(resp.data.message) {
                      this.snackbar = true;
                      this.text = resp.data.message;
                      this.timeout = 3000;
                      this.$refs.form.reset();
                      this.getPlans();
                  }
              })
              .catch(error => {
                  // Handle the error
                  var data = error.toJSON();
                  if(data.status == 400){
                      this.snackbar = true;
                      this.text = "something went wrong.";
                      this.timeout = 3000;
                  }
              });
          this.$refs.form.validate();
      },
    deleteplan(){
      const config = {
        headers: { 'content-type': 'multipart/form-data' }
      }
      let data = {
        plan_id: this.editedItem.id
      };
      axios.post("/api/admin/delete/plan",data, config)
          .then((resp) =>{
            this.snackbar = true;
            this.timeout = 3000;
            this.text = resp.data.message;
            this.dialogDelete = false;
            this.getPlans();
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
    deleteItem(item) {
      this.editedIndex = this.plans.indexOf(item);
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
