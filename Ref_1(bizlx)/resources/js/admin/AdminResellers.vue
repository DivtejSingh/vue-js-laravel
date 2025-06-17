<template>
  <div>
    <div class="row">
      <div class="col-2">
        <h3 class="fw-bold">Resellers</h3>
      </div>
      <div class="col">
        <v-btn tile color="error" @click="exportData" large>Export Excel</v-btn>
      </div>
      <div class="col-4">
        <v-text-field v-model="search" clearable append-icon="mdi-magnify" label="Search" placeholder="Search" single-line hide-details size="lg"></v-text-field>
      </div>
      <div class="col">
              <!-- The Create Resellers button now opens the modal -->
              <v-btn tile color="error" large @click="createDialog = true">
                  <v-icon left>mdi-plus</v-icon>CREATE RESELLERS
              </v-btn>
          </div>
    </div>
    <v-row class="mt-5">
      <v-col class="d-flex" cols="4" sm="4">
        <v-autocomplete
          clearable
          ref="stateFilter"
          v-model="state_id"
          @change="getSelectedData(); blurField('stateFilter')"
          :items="allstates"
          item-text="state"
          item-value="id"
          label="State"
          dense
        />
      </v-col>
      <v-col class="d-flex" cols="4" sm="4">
        <v-autocomplete
        clearable
        ref="cityFilter"
        v-model="city_id"
        @change="getSelectedData(); blurField('cityFilter')"
        :items="allcities"
        item-text="city"
        item-value="id"
        label="City"
        dense
      />
      </v-col>
      <v-col class="d-flex" cols="4" sm="4">
        <v-autocomplete
        clearable
        ref="statusFilter"
        v-model="user_status"
        @change="getSelectedData(); blurField('statusFilter')"
        :items="Action_status"
        item-text="text"
        item-value="val"
        label="Active/Inactive"
        dense
      />
      </v-col>
    </v-row>
    <div class="">
      <v-data-table :search="search" :headers="headers" :items="resellers" ref="myDataTable" class="elevation-1">
        <template v-slot:[`item.id`]="{ item }">
          <div>RES{{ item.id }}</div>
        </template>
        <template v-slot:top>
          <v-dialog v-model="dialogDelete" max-width="500px">
            <v-card>
              <v-card-title class="text-h6">Are you sure you want to delete this item?</v-card-title>
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
                <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
                <v-btn color="blue darken-1" text @click="toggleStatus">OK</v-btn>
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </template>

        <template v-slot:[`item.avatar`]="{ item }">
          <div v-if="item.user_avatar!=null">
            <v-img :src=(url+item.user_avatar) max-width="40"></v-img>
          </div>
          <div v-else>
            <v-avatar color="primary" size="40">
              <span class="white--text text">{{ item.name.toUpperCase().split(' ', 2).map(word => word.charAt(0)).join('') }}</span>
            </v-avatar>
          </div>
        </template>
        <template v-slot:[`item.reseller_skills`]="{ item }">
          <div v-for="(skills, i) in item.allskill" :key="i">
            <span v-if="item.allskill.length > 0">{{ skills.skill }},</span>
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

        <template v-slot:[`item.action`]="{ item }">
          <div class="button-container">
           <!-- <v-icon small class="mr-2" title="Edit" @click="editItem(item)">mdi-pencil</v-icon> -->
            <v-icon small class="mr-2" @click="deleteItem(item)" title="Delete">mdi-delete</v-icon>

          <!-- <v-icon
              small
              class="mr-2"
              title="Edit"
              @click="editItemreseler(item.id)"
          >
              mdi-pencil
          </v-icon> -->
          <v-icon small class="mr-2" @click="editItemreseler(item.id)" title="View Reseller Profile">mdi-eye</v-icon>

          <!-- <v-icon small class="mr-2" title="Login As Business" @click="resellerbusinessLogin(item.email)"> mdi-login </v-icon> -->
          <v-icon small class="mr-2" title="Login As Business" @click="resellerbusinesslist(item.id)"> mdi-login </v-icon>
          </div>
        </template>
      </v-data-table>
        <v-dialog max-width="500px" v-model="updateDialog">
            <v-card>
                <v-card-text>
                    <v-form @submit.prevent="resellerAddFormSubmit" ref="form" v-model="valid" lazy-validation>
                        <v-container>
                            <v-row>
                                <v-col cols="12" sm="6" md="12">
                                    <v-text-field
                                    clearable
                                        v-model="editedItem.name"
                                        label="Name"
                                        prepend-icon="mdi-account"></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6" md="12">
                                    <v-text-field
                                    clearable
                                        v-model="editedItem.email"
                                        label="E-mail"
                                        prepend-icon="mdi-email"></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6" md="12">
                                    <v-select
                                    clearable
                                        v-model="editedItem.reseller_gender"
                                        :rules="genderRules"
                                        label="Select Gender"
                                        :items="genders"
                                        item-text="gender"
                                        item-value="gender"
                                        prepend-icon="mdi-human"></v-select>
                                </v-col>
                                <v-col cols="12" sm="6" md="12">
                                    <v-text-field
                                    clearable
                                        v-model="editedItem.reseller_dob"
                                        type="date"
                                        label="Date of birth"
                                        prepend-icon="mdi-calendar" multiple>
                                    </v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6" md="12">
                                    <v-text-field clearable prepend-icon="mdi-phone"
                                                  v-model="editedItem.mobile_phone"
                                                  :counter="10"
                                                  :error-messages="errors"
                                                  label="Phone Number"></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6" md="6">
                                  <v-autocomplete
                                  clearable
                                  ref="professionUpdate"
                                  v-model="editedItem.profession_id"
                                  label="Select Profession"
                                  :items="allProfessions"
                                  item-text="profession"
                                  item-value="id"
                                  prepend-icon="mdi-tray-arrow-down"
                                  @change="blurField('professionUpdate')"
                                />
                                </v-col>
                                <v-col cols="12" sm="6" md="6">
                                  <v-autocomplete
                                    clearable
                                    ref="stateUpdate"
                                    v-model="editedItem.state_id"
                                    @change="getCities(); blurField('stateUpdate')"
                                    :items="allstates"
                                    label="Select State"
                                    item-text="state"
                                    item-value="id"
                                    prepend-icon="mdi-account"
                                  />
                                </v-col>
                                <v-col cols="12" sm="6" md="6">
                                    <v-autocomplete
                                    clearable
                                        v-model="editedItem.state_id"
                                        @change="getCities"
                                        :items="allstates"
                                        label="Select State"
                                        item-text="state"
                                        item-value="id"
                                        prepend-icon="mdi-account">
                                    </v-autocomplete>
                                </v-col>
                                <v-col cols="12" sm="6" md="6">
                                    <v-autocomplete
                                    clearable
                                        v-model="editedItem.city"
                                        :items="cities"
                                        label="Select City"
                                        item-text="city"
                                        item-value="city_id"
                                        prepend-icon="mdi-city" >
                                    </v-autocomplete>
                                </v-col>
                                <v-col cols="12" sm="6" md="12">
                                    <v-textarea
                                    clearable
                                        v-model="editedItem.address"
                                        label="Address"
                                        prepend-icon="mdi-download"
                                        rows="2"></v-textarea>
                                </v-col>
                            </v-row>
                            <v-col cols="12" md="12" class="py-0 d-flex" style="justify-content: space-between;">
                                <div>
                                    <v-checkbox v-model="editedItem.user_status" label="Active" value="1"></v-checkbox>
                                </div>
                                <div class="ms-3 pb-3">
                                    <v-btn v-if="isLoading" class="my-2 me-3 cred">Loading...</v-btn>
                                    <v-btn v-else class="my-2 me-3 cred" type="submit" :disabled="!valid">UPDATE</v-btn>
                                </div>
                            </v-col>

                        </v-container>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>
        <!-------------create-------->
        <v-dialog max-width="500px" v-model="createDialog">
  <v-card>
    <v-card-title>Create Reseller</v-card-title>
    <v-card-text>
      <v-form ref="createResellerForm" @submit.prevent="submitCreateReseller" v-model="valid" lazy-validation>
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-text-field clearable v-model="newReseller.name" label="Name" prepend-icon="mdi-account" required></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-text-field clearable v-model="newReseller.email" label="Email" prepend-icon="mdi-email" required></v-text-field>
            </v-col>
            <v-col cols="12">
            <v-text-field
            clearable
              v-model="newReseller.password"
              label="Password"
              prepend-icon="mdi-lock"
              :rules="[v => !!v || 'Password is required']"
              required
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-autocomplete
            clearable
            ref="stateCreate"
            v-model="newReseller.state_id"
            :items="allstates"
            item-text="state"
            item-value="id"
            label="State"
            @change="getCitie(); blurField('stateCreate')"
          />
          </v-col>
          <v-col cols="12">
            <v-autocomplete
            clearable
            ref="cityCreate"
            v-model="newReseller.city_id"
            @change="getSelectedData(); blurField('cityCreate')"
            :items="cities"
            item-text="city"
            item-value="id"
            label="City"
            dense
          />
          </v-col>
            <v-col cols="12">
              <v-select
              clearable
                v-model="newReseller.reseller_gender"
                :items="genders"
                label="Gender"
                item-text="gender"
                item-value="gender"
                prepend-icon="mdi-human"
                required
              ></v-select>
            </v-col>
            <v-col cols="12">
              <v-text-field clearable v-model="newReseller.reseller_dob" label="Date of Birth" type="date" required></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-text-field clearable v-model="newReseller.mobile_phone" label="Phone Number" prepend-icon="mdi-phone" required></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-autocomplete
                clearable
                ref="professionCreate"
                v-model="newReseller.profession_id"
                :items="allProfessions"
                item-text="profession"
                item-value="id"
                label="Profession"
                @change="blurField('professionCreate')"
              />
            </v-col>
            <v-col cols="12">
              <v-autocomplete
              clearable
                v-model="newReseller.skills_id"
                :items="allSkills"
                item-text="skill"
                item-value="id"
                multiple
                chips
                label="Skills"
                required
              ></v-autocomplete>
            </v-col>
            <v-col cols="12">
              <v-textarea clearable v-model="newReseller.address" label="Address" rows="2" required></v-textarea>
            </v-col>
          </v-row>
        </v-container>
      </v-form>
    </v-card-text>
    <v-card-actions>
      <v-btn @click="closeCreateDialog" color="red" dark>Close</v-btn>
      <v-btn @click="submitCreateReseller" color="blue" dark>Create</v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>

<v-snackbar
v-model="snackbar"
:timeout="3000"
color="success"
>
{{ text }}
<template v-slot:action="{ attrs }">
  <v-btn color="white" text v-bind="attrs" @click="snackbar = false">Close</v-btn>
</template>
</v-snackbar>

        <!-------------create-------->
    </div>
  </div>
</template>
<script>
import axios from "axios";
export default {
  name: "ResellersView",
  components: {  },
  data: () => ({
      url: 'https://bizlx.s3.ap-south-1.amazonaws.com',
      search: '',
      state_id: '',
      city_id: '',
      allstates: [],
      allcities: [],
      Action_status: [{ val: 1, text: 'Active' }, { val: "del", text: 'Inactive' }],
      snackbar: { message: '', visible: false, timeout: 3000 },
      cities: [],
      addedSkills: '',
      user_status: '',
      isLoading: false,
      valid: false,
      dialog: false,
      updateDialog: false,
      singleSelect: false,
      dialogDelete: false,
      dialogStatus: false,
      storeItems:[],
      headers: [
          { text: "Avatar", value: "avatar", width: 100 },
          { text: "Name", align: "start", sortable: false, value: "name", width: 100 },
          { text: "E-mail", value: "email", width: 100 },
          { text: "Mobile", value: "mobile_phone", width: 100 },
          { text: "City", value: "city", width: 100 },
          { text: "State", value: "state", width: 100 },
          { text: "Reseller Id", value: "username", width: 150 },
          { text: "Reseller Skill's", value: "reseller_skills", width: 200 },
          { text: "Status", value: "active", sortable: false, width: 100 },
          { text: "Actions", value: "action", sortable: false, width: 100 },
      ],
      resellers: [],
      editedIndex: -1,
      editedItem: {
          id: '',
          name: "",
          email: "",
          mobile_phone: "",
          city: "",
          state_id: "",
          reseller_id: "",
          reseller_skills: "",
      },
      defaultItem: {
          id: '',
          name: "",
          email: "",
          mobile_phone: "",
          city: "",
          state: "",
          reseller_id: "",
          reseller_skills: "",
      },
      genders: [{ id: 1, gender: 'Male' }, { id: 2, gender: 'Female' }],
      allProfessions: [],
      allSkills: [],
      allAddedSkills: [],
      password: '',
      passrules: {
          required: value => !!value || 'Required.',
          min: v => v.length >= 8 || 'Min 8 characters',
      },
      snackbar: false,
      text: '',
      timeout: '',
      createDialog: false,
      valid: false, 
      newReseller: {
          name: "",
          email: "",
          password: "",
          reseller_gender: "",
          reseller_dob: "",
          mobile_phone: "",
          profession_id: null,
          skills_id: [],
          address: "",
          state_id: null,
          city_id: null,
          user_status: true,
      },
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
      this.All_States();
      this.getCities();
      this.getCitie();
      this.all_Cities();
      this.getResellers();
      this.All_Professions();
      this.All_skills();
      this.loadProfessions();
      this.loadSkills();
  },

  methods: {
    blurField(refName) {
  this.$nextTick(() => {
    const refEl = this.$refs[refName];
    if (refEl?.blur) refEl.blur();
    else if (refEl?.$el?.querySelector?.('input')) {
      refEl.$el.querySelector('input').blur();
    }
  });
},
     
      getResellers() {
          axios.get('/api/resellers')
              .then((resp) => {
                  this.resellers = resp.data.resellers;
              });
      },

      openCreateDialog() {
          this.resetNewReseller();
          this.createDialog = true;
      },

      resetNewReseller() {
          this.newReseller = {
              name: "",
              email: "",
              password: "",
              reseller_gender: "",
              reseller_dob: "",
              mobile_phone: "",
              profession_id: null,
              skills_id: [],
              address: "",
              state_id: null,
              city_id: null,
              user_status: true,
          };
      },
      checkConfirmationForStatus(item){
        this.dialogStatus = true;
        this.storeItems = item;
      },
      toggleStatus() {
        const item = this.storeItems;
          item.user_status = item.user_status === '1' ? '0' : '1';
          this.closeDelete();
          this.updateUserStatus(item.id, item.user_status)
              .then(() => {
                  this.snackbar.message = `User is now ${item.user_status === '1' ? 'Active' : 'Inactive'}`;
                  this.snackbar.visible = true;
                  this.closeDelete();
              })
              .catch(() => {
                  this.snackbar.message = 'Failed to update user status.';
                  this.snackbar.visible = true;
                  item.user_status = item.user_status === '1' ? '0' : '1';
                  this.closeDelete();
              });
      },

      updateUserStatus(id, status) {
          axios.post('/api/resellers/userstatus', { id, user_status: status })
              .then(response => {
                  console.log('Status updated:', response.data);

              })
              .catch(error => {
                  console.error('Error updating status:', error);
              });
      },

      submitCreateReseller() {
          this.isLoading = true;
          axios.post("/api/admin/reseller/add", this.newReseller)
              .then((response) => {
                  this.snackbar = true;
                  this.text = "Reseller created successfully!";
                  this.createDialog = false;
                  this.resetNewReseller();
                  this.$emit("refreshResellers");
                  this.isLoading = false;
              })
              .catch((error) => {
                  this.snackbar = true;
                  this.text = "Failed to create reseller!";
                  this.isLoading = false;
              });
      },

      closeCreateDialog() {
          this.createDialog = false;
          this.resetNewReseller();
      },
      editItemreseler(reseller_id) {
          window.location.href = `/admin/reseller/edit/${reseller_id}`;
      },
      resellerbusinesslist(reseller_id) {
          window.location.href = `/admin/reseller/businesslist/${reseller_id}`;
      },
      loadProfessions() {
          axios.get("/api/professions")
              .then((response) => {
                  this.allProfessions = response.data.professions;
              })
              .catch((error) => {
                  console.error("Error fetching professions:", error);
              });
      },

      loadSkills() {
          axios.get("/api/skills")
              .then((response) => {
                  this.allSkills = response.data.skills;
              })
              .catch((error) => {
                  console.error("Error fetching skills:", error);
              });
      },

      all_Cities() {
          axios.get('/api/allcities')
              .then((resp) => {
                  this.allCities = resp.data.locations;
              });
      },

      getCities() {
          if (this.editedItem.state_id) {
              axios.get('/api/states/' + this.editedItem.state_id)
                  .then(response => {
                      this.cities = response.data;
                  })
          } else {
              this.cities = [];
          }
      },

      getCitie() {
          if (this.newReseller.state_id) {
              axios.get('/api/states/' + this.newReseller.state_id)
                  .then(response => {
                      this.cities = response.data;
                  })
                  .catch(error => {
                      console.error('Error fetching cities:', error);
                  });
          } else {
              this.cities = [];
          }
      },

      All_States() {
          axios.get('/api/allstates')
              .then((resp) => {
                  this.allstates = resp.data.locations;
              });
      },

      All_Professions() {
          axios.get('/api/professions')
              .then((resp) => {
                  this.allProfessions = resp.data.professions;
              });
      },

      All_skills() {
          axios.get('/api/skills')
              .then((resp) => {
                  this.allSkills = resp.data.skills;
              });
      },

      resellerAddFormSubmit() {
          this.isLoading = true;
          const config = { headers: { 'content-type': 'multipart/form-data' } };
          let data = {
              user_id: this.editedItem.id,
              name: this.editedItem.name,
              reseller_gender: this.editedItem.reseller_gender,
              email: this.editedItem.email,
              password: this.editedItem.password,
              reseller_dob: this.editedItem.reseller_dob,
              mobile_phone: this.editedItem.mobile_phone,
              profession_id: this.editedItem.profession_id,
              skills_id: this.allAddedSkills,
              state_id: this.editedItem.state_id,
              city_id: this.editedItem.city_id,
              address: this.editedItem.address,
              user_status: this.editedItem.user_status,
          };
          axios.post('/api/admin/reseller/update', data, config)
              .then((resp) => {
                  this.snackbar = true;
                  this.text = resp.data.message;
                  this.timeout = 3000;
                  this.isLoading = false;
              });
      },

      getSelectedData() {
          if (this.state_id) {
              axios.get('/api/states/' + this.state_id)
                  .then((resp) => {
                      this.allcities = resp.data;
                  });
          }
          let searchData = {
              state_id: this.state_id,
              city_id: this.city_id,
              user_status: this.user_status,
          };
          axios.post('/api/admin/reseller/search', searchData)
              .then((resp) => {
                  this.resellers = resp.data.resellers;
              });
      },

      editItem(item) {
          this.editedIndex = this.resellers.indexOf(item);
          this.editedItem = Object.assign({}, item);
          this.updateDialog = true;
      },

      deleteItem(item) {
          this.editedIndex = this.resellers.indexOf(item);
          this.editedItem = Object.assign({}, item);
          this.dialogDelete = true;
      },

      deleteItemConfirm() {
          this.resellers.splice(this.editedIndex, 1);
          this.closeDelete();
      },

      close() {
          this.updateDialog = false;
          this.resetNewReseller();
      },

      closeDelete() {
          this.dialogDelete = false;
          this.dialogStatus = false;
      },

      exportData() {
        // Export data table
        const data = this.$refs.myDataTable.items;
        const exportedData = [];
        data.forEach((item) => {
          // added skills
          this.addedSkills = '';
          for(var i = 0; i < item.allskill.length; i++){
              this.addedSkills += item.allskill[i].skill+' | ';
          }
          const exportedItem = {
            id: item.id,
            ResellersName: item.name,
            Email: item.email,
            Mobile: item.mobile_phone,
            City: item.city,
            State: item.state,
            ResellerId: "RES"+item.id,
            ResellerSkill: this.addedSkills,
            Created: item.added_date,
            // Add more properties as needed
          };
          exportedData.push(exportedItem);
        });
        console.warn(exportedData);
        const csvContent = this.convertToCSV(exportedData);
        const link = document.createElement('a');
        link.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvContent));
        link.setAttribute('download', 'reseller_exported_data.csv');
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
  },
};
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
