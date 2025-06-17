<template>
  <div>
    <div class="row">
      <div class="col-4 mt-2">
        <h3 class="fw-bold">Users</h3>
      </div>
      <div class="col-8">
        <v-text-field v-model="search" append-icon="mdi-magnify" label="Search" single-line hide-details>
        </v-text-field>
      </div>
    </div>
    <div class="my-5">
      <v-data-table :search="search" :headers="headers" :items="usersdata" sort-by="calories"
                    :single-select="singleSelect" show-select class="elevation-1" items-per-page="10" v-model="checkedIds">
        <template v-slot:top>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-dialog v-model="dialog" max-width="500px">
            <v-card>
              <v-card-title class="text-h5 grey lighten-2" style="justify-content:space-between;">{{ formTitle }}
                  <v-icon  @click="close" class="text-h5">mdi-close</v-icon>
              </v-card-title>
              <v-card-text>
                <v-form ref="form" @submit.prevent="submitUserUpdate" v-model="valid" lazy-validation>
                  <v-row>
                    <v-col cols="12" sm="6" md="12">
                        <v-text-field
                          v-model="editedItem.name"
                          :rules="nameRules"
                          :counter="50"
                          type="text"
                          label="Name" dense></v-text-field>
                    </v-col>

                    <v-col cols="12" sm="6" md="12">
                        <v-text-field
                            v-model="editedItem.email"
                            :rules="emailRules"
                            @keyup="emailVerify"
                            :error-messages="emailVerifyMessage"
                            type="email"
                            label="E-mail address" dense></v-text-field>
                    </v-col>

                    <v-col cols="12" sm="6" md="12">
                        <v-text-field
                            v-model="editedItem.username"
                            :rules="usernameRules"
                            @keyup="userNameVerify"
                            :error-messages="userNameVerifyMessage"
                            :counter="50"
                            type="text"
                            label="Username" dense></v-text-field>
                    </v-col>

                    <v-col cols="12" sm="6" md="12">
                        <v-checkbox
                            v-model="editedItem.user_status"
                            label="Active"
                            value="1"></v-checkbox>
                    </v-col>
                    <v-card-actions>
                        <v-btn color="red" dark @click="close">CANCEL</v-btn>
                        <v-btn type="submit" color="primary" :disabled="!valid">SAVE</v-btn>
                    </v-card-actions>
                  </v-row>
                </v-form>

              </v-card-text>
            </v-card>
          </v-dialog>
          <v-dialog v-model="dialogDelete" width="500px">
            <v-card>
              <v-card-title class="text-h5">Are you sure you want to delete this item?
              </v-card-title>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
                <v-btn color="blue darken-1" text @click="deleteItemConfirm">OK</v-btn>
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
          <v-dialog v-model="deletemultipleusers" max-width="500px">
              <v-card>
                <v-card-title class="text-h6">Are you sure you want to delete these users?</v-card-title>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="red" dark text @click="closeMultiDailoge">Cancel</v-btn>
                  <v-btn v-if="isLoading" class="cred">Loading...</v-btn>
                  <v-btn v-else color="red" dark text @click="multipleUsersIds" >OK</v-btn>
                  <v-spacer></v-spacer>
                </v-card-actions>
              </v-card>
            </v-dialog>
        </template>

        <template v-slot:[`item.action`]="{ item }">
          <v-icon small class="mr-2" @click="editItem(item)">
            mdi-pencil
          </v-icon>
          <v-icon small @click="deleteItem(item)">
            mdi-delete
          </v-icon>
        </template>
        <template v-slot:[`item.user_status`]="{ item }">
          <div v-if="item.user_status === '1'">
            <span small class="mr-2">
              Active
            </span>
          </div>
          <div v-else>
            <span small class="mr-2">
              Inactive
            </span>
          </div>
        </template>
        <template v-slot:footer>
          <div class="text-muted py-3 px-5">
            <v-btn @click="getSelectedIds" color="grey">DELETE SELECTED</v-btn>
          </div>
        </template>
      </v-data-table>
    </div>

    <v-snackbar v-model="snackbar" :timeout="timeout">{{ text }}
        <template v-slot:action="{}">
            <v-btn color="blue" text v-bind="attrs" @click="snackbar = false">Close</v-btn>
        </template>
    </v-snackbar>

  </div>
</template>

<script>
import axios from 'axios';
export default {
    name: "PublicUsers",
    props:{
        users:Array
    },
    components: {  },
    data: () => ({
        search: '',
        checkbox1: true,
        checkbox2: false,
        dialog: false,
        dialogDelete: false,
        deletemultipleusers:false,
        headers: [
            {
                text: 'Name', align: 'start', sortable: false, value: 'name',
            },
            { text: 'E-mail', value: 'email' },
            { text: 'Phone Number', value: 'mobile_phone' },
            { text: 'City', value: 'city' },
            { text: 'Username', value: 'username' },
            { text: 'Created At', value: 'created_date' },
            { text: 'Status', value: 'user_status', sortable: false },
            { text: 'Actions', value: 'action', sortable: false },
        ],
        usersdata: [],
        checkedIds: [],
        isLoading: false,
        editedIndex: -1,
        editedItem: {
            id: '',
            name: '',
            email: '',
            username: '',
            user_status: '',
        },
        defaultItem: {
            name: '',
            email: '',
            username: '',
            user_status: '',
        },
        snackbar: false,
        text: '',
        timeout: 300,
        valid: false,
        userNameVerifyMessage:'',
        emailVerifyMessage:'',
        nameRules: [
          v => !!v || 'Name is required.',
          v => v.length <= 50 || 'Name must be less than 10 characters.',
        ],
        emailRules: [
          v => !!v || 'E-mail is required.',
          v => /.+@.+/.test(v) || 'E-mail must be valid.',
        ],
        usernameRules: [
          v => !!v || 'Username is required.',
          v => v.length >= 8 || 'Username must be greater than 8 characters.',
          v => v.length <= 50 || 'Username must be less than 50 characters.',
          v => (v.split(' ').length <= 1) || 'Please cant use white space.',
        ],
    }),

    computed: {
        formTitle() {
            return this.editedIndex === -1 ? 'New Item' : 'Edit User'
        },
    },

    watch: {
        dialog(val) {
            val || this.close()
        },
        dialogDelete(val) {
            val || this.closeDelete()
        },
    },

    created() {
        this.getUsers()
    },

    methods: {
        getUsers() {
            axios.get("/api/user")
                .then((resp) => {
                    // this.usersdata = resp.data.user;
                    this.usersdata = this.users;
                });
        },
        getSelectedIds() {
          var data = {
          ids:this.checkedIds.map(item => item.id)
        };
        if(data.ids.length == 0){
          this.snackbar = true;
          this.text = "Please select users!";
          this.timeout = 3000;
        }else{
        this.deletemultipleusers = true;
        }
      },

      closeMultiDailoge(){
        this.deletemultipleusers = false;
      },

        editItem(item) {
            this.editedIndex = this.usersdata.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialog = true
        },

        deleteItem(item) {
            this.editedIndex = this.usersdata.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialogDelete = true
        },

        deleteItemConfirm() {

          let data = {
            id:this.editedItem.id
          };
            axios.post('/api/admin/user/delete',data)
            .then((resp) => {
              if( resp.data.success == true){
                this.snackbar = true;
                this.text = resp.data.message;
                this.timeout = 3000;
              }else{
                this.snackbar = true;
                this.text = "Something went wrong";
                this.timeout = 3000;
              }
            });
            this.usersdata.splice(this.editedIndex, 1)
            this.closeDelete()
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
        userNameVerify(){ // verify username
            if(this.editedItem.username.length >= 8){
              axios.post('/api/user/verify', {'username':this.editedItem.username})
                  .then((resp) => {
                    if(resp.data.status==400){
                        this.userNameVerifyMessage = resp.data.message;
                    }else{
                        this.userNameVerifyMessage = "";
                    }
              });
            }
        },
        emailVerify(){ // verify email
          if(/.+@.+/.test(this.editedItem.email)){
              axios.post('/api/user/verify', {'email':this.editedItem.email})
                  .then((resp) => {
                    if(resp.data.status==400){
                        this.emailVerifyMessage = resp.data.message;
                    }else{
                        this.emailVerifyMessage = "";
                    }
              });
            }
        },

        submitUserUpdate() {
            const config = {
                headers: { 'content-type': 'multipart/form-data' }
            }
            let data = {
                user_id: this.editedItem.id,
                name: this.editedItem.name,
                email: this.editedItem.email,
                username: this.editedItem.username,
                user_status: this.editedItem.user_status,
            }
            if (this.editedIndex > -1) {

                axios.post("admin/user/update", data, config)
                  .then((resp) => {
                      this.snackbar = true;
                      this.text = resp.data.message;
                      this.timeout = 3000;

                      this.getUsers();
                      this.close();
                  }).catch(error=>{
                    this.error=error;
                  });

                this.$refs.form.validate();
                Object.assign(this.usersdata[this.editedIndex], this.editedItem)
            } else {
                this.usersdata.push(this.editedItem);
                this.close();
            }

        },

        // Delete multiple users ( code start )
        multipleUsersIds(){
          this.isLoading = true;
          var data = {
            ids:this.checkedIds.map(item => item.id)
          };
          axios.post('delete/multiple/users', data)
            .then((resp) => {
              if(resp.data.success == true){
                this.isLoading = false;
                this.getUsers();
                this.deletemultipleusers = false;
                this.snackbar = true;
                this.text = resp.data.message;
                this.timeout = 3000;
              }else{
                this.isLoading = false;
                this.deletemultipleusers = false;
                this.getUsers();
                this.snackbar = true;
                this.text = 'something went wrong';
                this.timeout = 3000;
              }
            });
        }
        // Delete multiple users ( code end )
    },
}
</script>
