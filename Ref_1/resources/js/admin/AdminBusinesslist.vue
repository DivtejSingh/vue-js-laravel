<template>
  <div>
    <div class="row">
      <div class="col-4">
        <h3 class="fw-bold">Business</h3>
      </div>
      <div class="col-2">
        <div class="my-2">
          <v-btn color="error" dark large @click="exportData">Export Excel</v-btn>
        </div>
      </div>
      <div class="col-6">
        <v-text-field clearable v-model="search" append-icon="mdi-magnify" label="Search" single-line hide-details></v-text-field>
      </div>
      <v-row>
        <v-col class="d-flex" cols="4" sm="4">
          <v-autocomplete
  ref="stateRef"
  clearable
  v-model="state_id"
  @change="onStateChange"
  :items="allstates"
  item-text="state"
  item-value="id"
  label="State"
  dense
/>

        </v-col>
        <v-col class="d-flex" cols="4" sm="4">
          <v-autocomplete
          ref="cityRef"
          clearable
          v-model="city_id"
          @change="onCityChange"
          :items="allcities"
          item-text="city"
          item-value="id"
          label="City"
          dense
        ></v-autocomplete>

        </v-col>
        <v-col class="d-flex" cols="4" sm="4">
          <v-autocomplete clearable ref="statusRef" v-model="user_status" @change="onStatusChange"
                          :items="Action_status" item-text="text" item-value="val"
                          label="Active/Inactive" dense>
          </v-autocomplete>
        </v-col>
      </v-row>
      <v-row>
        <v-col class="d-flex" cols="4" sm="4">
          <v-autocomplete clearable ref="planRef" v-model="plans_id" @change="onPlanChange"
                          :items="allplans" item-text="plan_description" item-value="id"
                          label="Plan" dense>
          </v-autocomplete>
        </v-col>
        <v-col class="d-flex" cols="4" sm="4">
          <v-autocomplete clearable ref="subcatRef" v-model="subcat_id" @change="onSubcatChange"
                          :items="allcategories" item-text="subcat_name" item-value="id"
                          label="Sub-Category" dense>
          </v-autocomplete>
        </v-col>

        <v-col cols="4" sm="6" md="4">
          <v-menu v-model="expiredDate" :close-on-content-click="false" :nudge-right="40" transition="scale-transition" min-width="auto">
            <template v-slot:activator="{ on, attrs }">
              <v-text-field clearable dense v-model="date" label="Expired date" prepend-icon="mdi-calendar"
                            v-bind="attrs" v-on="on"></v-text-field>
            </template>
            <v-date-picker v-model="date" @change="getSelectedData" @input="expiredDate = false"></v-date-picker>
          </v-menu>
        </v-col>
      </v-row>
      <div class="">
        <v-data-table dense :headers="headers" :search="search" :items="businesslists" ref="myDataTable" class="elevation-1">
          <template v-slot:top>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-spacer></v-spacer>
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
            <v-dialog v-model="dialogStatus" max-width="500px">
              <v-card>
                  <v-card-title class="text-h6">Are you sure you want to change status?</v-card-title>
                  <v-card-actions>
                      <v-spacer></v-spacer>
                      <!-- Cancel Button -->
                      <v-btn color="blue darken-1" text @click="closeStatus">Cancel</v-btn>
                      <!-- OK Button -->
                      <v-btn color="blue darken-1" text @click="toggleStatus">OK</v-btn>
                      <v-spacer></v-spacer>
                  </v-card-actions>
              </v-card>
          </v-dialog>


          </template>
          <template v-slot:[`item.listedby`]="{ item }">
            <div v-if="item.listedby === 1">
              <span v-if="item.listedby_reseller_id!=''">{{ item.reseller_name }}</span>
              <span v-else class="text-center">---</span>
            </div>
            <div v-else class="text-center">---</div>
          </template>
          <template v-slot:[`item.added_date`]="{ item }">
            <div v-if="item.added_date!='0000-00-00'">{{ item.added_date }}</div>
            <div v-else class="text-center">---</div>
          </template>
          <template v-slot:[`item.expires`]="{ item }">
            <div v-if="item.added_date!='0000-00-00'">
              {{ addedExpiredDate(item.added_date, item.plan_expiry) }}
            </div>
            <div v-else class="text-center">---</div>
          </template>
          <template v-slot:[`item.action`]="{ item }">
            <div class="button-container">
<!--            <router-link :to="{name:'businesslistupdate', params:{id:item.id}}" >-->
<!--              <v-icon small title="Edit" class="mr-2">mdi-pencil</v-icon>-->
<!--            </router-link>-->
              <!-- <v-icon small class="mr-2" @click="editItem(item)" title="Edit city State">mdi-pencil</v-icon> -->
              <!-- <v-icon small class="mr-2" @click="businessdeals(item.business_id)" title="Businessdeals">mdi-login</v-icon> -->
              <!-- <v-icon small class="mr-2" @click="editItem(item)" title="Edit city State">mdi-pencil</v-icon> -->
              <v-icon small class="mr-2" @click="deleteItem(item)" title="Delete">mdi-delete</v-icon>
              <!-- <v-icon
                small
                class="mr-2"
                title="Edit Business Profile"
                @click="goToEditProfile(item.business_id)"
              >mdi-pencil</v-icon> -->
              <v-icon small class="mr-2" @click="goToEditProfile(item.business_id)" title="View Business Profile">mdi-eye</v-icon>

            <!-- <v-icon small class="mr-2" @click="businesslogin(item)" title="Login as business">mdi-logout</v-icon> -->
            <!-- <v-icon small @click="allreview(item)">mdi-message-text</v-icon> -->
            </div>
          </template>
          <template v-slot:[`item.active`]="{ item }">
            <div 
              @click="checkConfirmationForStatus(item)"
              :style="{
                color: item.user_status === '1' ? 'green' : 'red',
                cursor: 'pointer'
              }"
            >
              {{ item.user_status === '1' ? 'Active' : 'Inactive' }}
              <v-snackbar v-model="snackbar.visible" :timeout="snackbar.timeout">
                {{ snackbar.message }}
              </v-snackbar>
            </div>
        </template>
<!--          <template v-slot:footer>-->
<!--            <div class="text-muted py-3 px-5">-->
<!--              <v-btn color="grey">DELETE SELECTED</v-btn>-->
<!--            </div>-->
<!--          </template>-->
        </v-data-table>
        <v-dialog v-model="dialogedit" max-width="500px" persistent>
             <v-card>
              <v-card-title class="text-h5 grey lighten-2" style="justify-content:space-between;">Edit State City Sub-Category
                  <v-icon  @click="closeedit" class="text-h5">mdi-close</v-icon>
              </v-card-title>
               <v-card-text>
                <v-form @submit.prevent="updatestatecity" ref="form" v-model="valid" lazy-validation>
                  <v-row>
                    <v-col class="d-flex" cols="4" sm="6">
                      <v-autocomplete
                        v-model="editedItem.state_id"
                        @change="getstatecitydata"
                        :items="allstates"
                        item-text="state"
                        item-value="id"
                        label="State" dense>
                      </v-autocomplete>
                    </v-col>
                    <v-col class="d-flex" cols="4" sm="6">
                      <v-autocomplete
                          v-model="editedItem.city_id"
                          @change="getstatecitydata"
                          :items="allcity"
                          item-text="city"
                          item-value="id"
                          label="City" dense>
                        </v-autocomplete>
                     </v-col>

                     <v-col class="d-flex" cols="4" sm="12">
                      <v-autocomplete
                        v-model="editedItem.sub_cat_id"
                        :items="allcategoriesWithStringId"
                        item-text="subcat_name"
                        item-value="id"
                        label="Sub-Category"
                        dense
                      ></v-autocomplete>
                      </v-col>
                    <v-col cols="12" md="12" class="py-0">
                      <div class="pb-2">
                        <v-btn class="my-4 me-3 cred" type="submit" :disabled="!valid">UPDATE</v-btn>
                      </div>
                    </v-col>
                  </v-row>
                </v-form>
              </v-card-text>
            </v-card>
          </v-dialog>
      </div>
    </div>
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
  name: "BusinessList",
  components: {  },
  data: () => ({
      allstates: [],
      allcities: [],
      allcity:[],
      Action_status: [{val:1, text:'Active'}, {val:"del", text:'Inactive'}],
      snackbar: { message: '', visible: false, timeout: 3000 },
      allplans: [],
      allcategories:[],
      menu: false,
      search: '',
      state_id: '',
      subcat_id:'',
      city_id: '',
      valid: false,
      user_status: '',
      plans_id: '',
      date: '',
      expiredDate: false,
      modal: false,
      dialogedit: false,
      dialogDelete: false,
      dialogStatus: false,
      storeItems:[],
      headers: [
          {text: "Name", align: "start", sortable: false, value: "name", width:100},
          { text: "E-mail", value: "email", width:100},
          { text: "Mobile number", value: "mobile_phone", width:150 },
          { text: "Plan", value: "plan_description", width:100 },
          { text: "Reseller", value: "listedby", width:100 },
          { text: "Listing date", value: "added_date", width:150 },
          { text: "Expires", value: "expires", width:100 },
          { text: "State", value: "state", width:100 },
          { text: "City", value: "city", width:100 },
          { text: "Sub category", value: "subcatsname", width:150 },
          { text: "Status", value: "active", sortable: false, width:50 },
          { text: "Actions", value: "action", sortable: false, width:100 },
      ],
      businesslists: [],
      editedIndex: -1,
      editedItem: {
        profile_id:'',
          id: '',
          city_id: '',
          city:'',
          category_id: '',
          name: "",
          email: "",
          mobile_phone: "",
          username: "",
          reviewcount: "",
          created_at: "",
          state_id:"",
          sub_cat_id:"",

      },
      defaultItem: {
        profile_id:"",
          name: "",
          email: "",
          mobile_phone: "",
          city: "",
          city_id: '',
          state_id:"",
          username: "",
          reviewcount: "",
          created_at: "",
          sub_cat_id:"",
      },
      snackbar: false,
      text: '',
      timeout: '',
  }),
  watch: {
    dialogedit(val) {
          val || this.closeedit();
      },
      dialogDelete(val) {
          val || this.closeDelete();
      },
  },
  computed: {
  allcategoriesWithStringId() {
    return this.allcategories.map(category => ({
      ...category,
      id: category.id.toString()
    }));
  }
},
  created() {
      this.getBusiness();
      this.All_States();
      this.All_plans();
      this.All_subCats();
      this.getstatecitydata();
  },
  methods: {
    handleBlur(refName) {
  this.$nextTick(() => {
    const inputEl = this.$refs[refName]?.$el?.querySelector('input');
    if (inputEl) inputEl.blur();
  });
},
      getBusiness(){
        axios.get('/api/businesses')
            .then((resp)=>{

              this.businesslists = resp.data.businesses;
              // const uniqueItems = Array.from(new Set(this.businesslists.map(item => item.id)))
              //     .map(id => this.businesslists.find(item => item.id === id));
              // this.businesslists = uniqueItems;
            });
      },
      checkConfirmationForStatus(item){
        this.dialogStatus = true;
        this.storeItems = item;
      },
      toggleStatus() {
        const item = this.storeItems;
          item.user_status = item.user_status === '1' ? '0' : '1';
          this.closeStatus();
          this.updateUserStatus(item.id, item.user_status)
              .then(() => {
                  this.snackbar.message = `User is now ${item.user_status === '1' ? 'Active' : 'Inactive'}`;
                  this.snackbar.visible = true;
                  this.closeStatus();
              })
              .catch(() => {
                  this.snackbar.message = 'Failed to update user status.';
                  this.snackbar.visible = true;
                  item.user_status = item.user_status === '1' ? '0' : '1';
                  this.closeStatus();
              });
      },

      updateUserStatus(id, status) {
          axios.post('/api/business/userstatus', { id, user_status: status })
              .then(response => {
                  console.log('Status updated:', response.data);
                 
              })
              .catch(error => {
                  console.error('Error updating status:', error);
              });
      },

      goToEditProfile(id) {
        // Using window.location for navigation
        window.location.href = `/admin/businesses/edit/${id}`;
      },
      editItem(item) {
            this.editedIndex = this.businesslists.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialogedit = true;
            this.getstatecitydata();
        },
      addedExpiredDate(added_date, plan_expiry){ // expired date
          var date = new Date(added_date);
          date.setDate(date.getDate() + plan_expiry);
          const currentDate = date.getFullYear()+"-"+date.getMonth()+"-"+date.getDate();
          return currentDate;
      },
      deleteItem(item) {
          this.editedIndex = this.businesslists.indexOf(item);
          this.editedItem = Object.assign({}, item);
          this.dialogDelete = true;
      },
      deleteItemConfirm() {
          this.businesslists.splice(this.editedIndex, 1);
          this.closeDelete();
      },
      businessdeals(plan_id) {
        window.location.href = `/admin/businesses-deals/${plan_id}`;
      },
      closeedit() {
          this.dialogedit = false;
          this.$nextTick(() => {
              this.editedItem = Object.assign({}, this.defaultItem);
              this.editedIndex = -1;
          });
      },
      closeStatus() {
          this.dialogDelete = false;
          this.dialogStatus = false;
      },
      closeDelete() {
          this.dialogDelete = false;
          this.$nextTick(() => {
              this.editedItem = Object.assign({}, this.defaultItem);
              this.editedIndex = -1;
          });
      },
      All_States() {
          axios.get('/api/allstates')
              .then((resp) => {
                  this.allstates = resp.data.locations;
              });
      },

      All_plans(){
        axios.get('/api/plans')
          .then((resp)=>{
            this.allplans = resp.data.plans;
          });
      },
      All_subCats(){
        axios.get('/api/subcategory')
          .then((resp) => {
              this.allcategories = resp.data.subcategories;
        });
      },

      getstatecitydata(){
        axios.get('/api/allcities')
              .then((resp) => {
                  this.allCities = resp.data.locations;
              });
        },
      getSelectedData(){
        if (this.state_id) {
          axios.get('/api/states/'+this.state_id)
          .then((resp)=>{

            this.allcities = resp.data;
          });
        }
        // Search muiltiple
        var searchData = {
          state_id : this.state_id,
          city_id : this.city_id,
          user_status : this.user_status,
          plans_id : this.plans_id,
          sub_cat_id : this.sub_cat_id,
          expired_date : this.date,
        };
        axios.post('/api/admin/business/search', searchData )
        .then((resp)=>{
          this.businesslists = resp.data.businesses;
        });
      },
    
      updatestatecity() {
          const config = {
              headers: { 'content-type': 'multipart/form-data' }
          }
          let data = {
            profile_id: this.editedItem.profile_id,
            business_id:this.editedItem.id,
            state_id: this.editedItem.state_id,
            city_id: this.editedItem.city_id,
            subcatid: this.editedItem.sub_cat_id,
          };
            axios.post('/api/admin/businessesstatecity/update', data, config)
              .then((resp) => {
                  this.snackbar = true;
                  this.text = resp.data.message;
                  this.timeout = 3000;
                  this.getBusiness();
                  this.dialogedit = false;
            });

        },
      exportData() {
          // Export data table
          const data = this.$refs.myDataTable.items;
          const exportedData = [];
          data.forEach((item) => {
            // added skills
            if(item.added_date!='0000-00-00'){ // cteated date
              var listing_date = item.added_date;
            }else{
              listing_date = "---";
            }
            if(item.added_date!='0000-00-00'){ // expired date
              var expires_date = this.addedExpiredDate(item.added_date, item.plan_expiry);
            }else{
              expires_date = "---";
            }
            if(item.reseller_name!=''){ // reseller name
              var resellerName = item.reseller_name;
            }else{
              resellerName = "---";
            }
            const exportedItem = {
              id: item.id,
              Business_name: item.name,
              Email: item.email,
              Mobile: item.mobile_phone,
              Plan: item.plan_description,
              Reseller: resellerName,
              State: item.state,
              City: item.city,
              Category_name: item.cat_name,
              Subcat_name: item.subcat_name,
              Listing_date: listing_date,
              Expired_date: expires_date,
              // Add more properties as needed
            };
            exportedData.push(exportedItem);
          });
          const csvContent = this.convertToCSV(exportedData);
          const link = document.createElement('a');
          link.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvContent));
          link.setAttribute('download', 'business_exported_data.csv');
          link.style.display = 'none';
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        },
        convertToCSV(data) {
          // Convert the data array to a CSV string
          const header = Object.keys(data[0]).join(',') + '\n';
          const rows = data.map((item) => Object.values(item).join(',')).join('\n');
          return header + rows;
        },
        onStateChange () {
    this.handleBlur('stateRef');
    this.getSelectedData();   // ← actual call
  },
  onCityChange () {
    this.handleBlur('cityRef');
    this.getSelectedData();
  },
  onStatusChange () {
    this.handleBlur('statusRef');
    this.getSelectedData();
  },
  onPlanChange () {
    this.handleBlur('planRef');
    this.getSelectedData();
  },
  onSubcatChange () {
    this.handleBlur('subcatRef');
    this.getSelectedData();
  },
  },
}
</script>

<style scoped>
.button-container {
    display: flex;
    gap: 10px;
}

@media (max-width: 768px) {
    .button-container {
        flex-direction: row;
        gap: 15px;
    }
}
</style>
