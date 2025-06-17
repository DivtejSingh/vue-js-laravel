<template>
    <div>
        <div class="row">
            <div class="col-3">
                <h3 class="fw-bold">Sub-admins</h3>
            </div>
            <div class="col-6">
                <v-text-field v-model="search" append-icon="mdi-magnify" label="Search" placeholder="Search" single-line hide-details size="lg"></v-text-field>
            </div>
            <div class="col-3 test-decoration-none">
                <router-link to="admin/add/subadmin">
                    <v-btn tile color="error" large @click="createDialog = true">
                        <v-icon left>mdi-plus</v-icon>CREATE SUBADMIN
                    </v-btn>
                </router-link>
            </div>
        </div>
        <div class=" my-5">
            <v-data-table :search="search" :headers="headers" :items="subadmins" sort-by="calories" class="elevation-1">
                <template v-slot:top>
                    <v-divider class="mx-4" inset vertical></v-divider>
                    <v-spacer></v-spacer>
                    <v-dialog v-model="dialog" max-width="700px">
                        <v-card>
                            <v-card-title class="text-h6 grey lighten-2" style="justify-content:space-between;">{{ formTitle }}
                                <v-icon  @click="close" class="text-h5">mdi-close</v-icon>
                            </v-card-title>
                            <v-form @submit.prevent="subAdminUpdateSubmit" ref="form" v-model="valid" lazy-validation>
                                <v-card-text class="py-0">
                                    <v-container>
                                        <v-row>
                                            <v-col cols="12" sm="6" md="4">
                                                <v-text-field
                                                    v-model="editedItem.name"
                                                    :rules="nameRules"
                                                    type="text"
                                                    label="Name" dense></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="6" md="4">
                                                <v-text-field
                                                    v-model="editedItem.email"
                                                    :rules="emailRules"
                                                    @keyup="emailVerify"
                                                    :error-messages="emailVerifyMessage"
                                                    label="E-mail" dense></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="6" md="4">
                                                <v-text-field
                                                    v-model="editedItem.mobile_phone"
                                                    :rules="phoneRules"
                                                    :counter="10"
                                                    @keyup="phoneVerify"
                                                    :error-messages="phoneVerifyMessage"
                                                    label="Phone Number" dense></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="6" md="6">
                                                <v-autocomplete label="Select City"
                                                                v-model="editedItem.city_id"
                                                                :rules="cityRules"
                                                                :items="allCities"
                                                                item-value="city_id"
                                                                item-text="city"
                                                                dense></v-autocomplete>
                                            </v-col>
                                            <v-col cols="12" sm="6" md="6">
                                                <v-text-field
                                                    v-model="editedItem.username"
                                                    :rules="usernameRules"
                                                    @keyup="userNameVerify"
                                                    :error-messages="userNameVerifyMessage"
                                                    :counter="50"
                                                    label="Username" dense></v-text-field>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col cols="12" sm="6" md="1" class="py-0">
                                                <v-checkbox v-model="editedItem.user_status" label="Active" value="1"></v-checkbox>
                                            </v-col>
                                        </v-row>
                                    </v-container>
                                </v-card-text>
                                <v-col cols="12" sm="6" md="12" class="py-0">
                                    <div class="ms-3 pb-3">
                                        <v-btn v-if="isLoading" class="my-2 me-3 cred">Loading...</v-btn>
                                        <v-btn v-else type="submit" class="my-4 me-3 cred">UPDATE</v-btn>
                                        <v-btn class="my-4 me-3 cred" @click="close">CANCEL</v-btn>
                                    </div>
                                </v-col>
                            </v-form>
                        </v-card>
                    </v-dialog>
                    <v-dialog v-model="dialogDelete" max-width="500px">
                        <v-card>
                            <v-card-title class="text-h6">Are you sure you want to delete this item?</v-card-title>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn class="cred" @click="closeDelete">Cancel</v-btn>
                                <v-btn v-if="isLoading" class="my-2 me-3 cred">Loading...</v-btn>
                                <v-btn v-else class="cred" @click="deleteItemConfirm">OK</v-btn>
                                <v-spacer></v-spacer>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                    <v-dialog v-model="createDialog" max-width="700px">
                        <v-card>
                            <v-card-title class="text-h6 grey lighten-2" style="justify-content:space-between;">
                                Create Sub-admin
                                <v-icon @click="createDialog = false" class="text-h5">mdi-close</v-icon>
                            </v-card-title>
                            <v-form @submit.prevent="submitCreateSubAdmin" ref="createForm" v-model="valid" lazy-validation>
                                <v-card-text class="py-0">
                                    <v-container>
                                        <v-row>
                                            <v-col cols="12" sm="6" md="4">
                                                <v-text-field v-model="newSubAdmin.name" :rules="nameRules" label="Name" dense></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="6" md="4">
                                                <v-text-field v-model="newSubAdmin.email" :rules="emailRules" label="Email" dense></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="6" md="4">
                                                <v-text-field v-model="newSubAdmin.mobile_phone" :rules="phoneRules" label="Phone Number" dense></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="6" md="6">
                                                <v-autocomplete v-model="newSubAdmin.city_id" :items="allCities" item-value="city_id" item-text="city" label="City" dense></v-autocomplete>
                                            </v-col>
                                            <v-col cols="12" sm="6" md="6">
                                                <v-text-field v-model="newSubAdmin.username" :rules="usernameRules" label="Username" dense></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="6" md="6">
                                                <v-text-field v-model="newSubAdmin.password" :rules="passwordRules" type="password" label="Password" dense></v-text-field>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col cols="12" sm="6" md="1">
                                                <v-checkbox v-model="newSubAdmin.user_status" label="Active" value="1"></v-checkbox>
                                            </v-col>
                                        </v-row>
                                    </v-container>
                                </v-card-text>
                                <v-card-actions>
                                    <v-btn color="error" type="submit">Create</v-btn>
                                    <v-btn @click="createDialog = false">Cancel</v-btn>
                                </v-card-actions>
                            </v-form>
                        </v-card>
                    </v-dialog>
                </template>
                <template v-slot:[`item.action`]="{ item }">
                    <v-icon small class="mr-2" @click="editItem(item)">mdi-pencil</v-icon>
                    <v-icon small @click="deleteItem(item)"> mdi-delete</v-icon>
                </template>
                <template v-slot:[`item.active`]="{ item }">
                    <v-icon small class="mr-2" @click="editItem(item)">mdi-check</v-icon>
                </template>
                <template v-slot:[`item.user_status`]="{ item }">
                    <div v-if="item.user_status === '1'">
                        <span>Active</span>
                    </div>
                    <div v-else>
                        <span>Inactive</span>
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
import axios from "axios";
export default {
    name: "SubAdmins",
    components: {  },
    data: () => ({
        createDialog: false,
        valid: true,
        search: "",
        dialog: false,
        dialogDelete: false,
        headers: [
            {text: "Name", align: "start",sortable: false, value: "name",},
            { text: "E-mail", value: "email" },
            { text: "Phone Number", value: "mobile_phone" },
            { text: "City", value: "city" },
            { text: "Username", value: "username" },
            { text: "Created At", value: "created_date" },
            { text: "Status", value: "user_status", sortable: false },
            { text: "Actions", value: "action", sortable: false },
        ],
        subadmins: [],
        editedIndex: -1,
        editedItem: {
            name: "",
            email: "",
            username: "",
            mobile_phone: "",
            city_id: "",
            created_at: "",
        },
        newSubAdmin: {
            name: '',
            email: '',
            mobile_phone: '',
            city_id: null,
            username: '',
            password: '',
            user_status: 1,
        },
        defaultItem: {
            name: "",
            email: "",
            city_id: "",
            username: "",
            mobile_phone: "",
            created_at: "",
        },
        snackbar: false,
        isLoading: false,
        text: '',
        timeout: '',
        valid: false,
        allCities: [],
        emailVerifyMessage:'',
        userNameVerifyMessage:'',
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
            v => (v.split(' ').length <= 1) || 'Please cant use white space.',
        ],
        cityRules: [
            v => !!v || 'City is required.',
        ],
        phoneRules: [
            v => !!v || 'Phone is required.',
            v => v.length == 10 || 'Phone number must be 10 characters.',
        ],
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
        this.getSubadmins();
        this.all_Cities();
    },
    methods: {
        getSubadmins(){
            axios.get('/api/subadmins')
                .then((resp)=>{
                    this.subadmins = resp.data.subadmins;
                });
        },
        all_Cities() { // get city
            axios.get('/api/allcities')
                .then((resp) => {
                    this.allCities = resp.data.locations;
                });
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
        phoneVerify(){ // verify phone
            if(this.form1.mobile_phone.length == 10){
                axios.post('/api/user/verify', {'mobile_phone':this.form1.mobile_phone})
                    .then((resp) => {
                        if(resp.data.status==400){
                            this.phoneVerifyMessage = resp.data.message;
                        }else{
                            this.phoneVerifyMessage = "";
                        }
                    });
            }
        },
        editItem(item) {
            this.editedIndex = this.subadmins.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialog = true;
        },
        deleteItem(item) {
            this.editedIndex = this.subadmins.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialogDelete = true;
        },
        deleteItemConfirm() {
            this.isLoading = true;
            axios.get("/api/admin/delete/subadmin/"+this.editedItem.id)
                .then((resp) => {
                    this.isLoading = false;
                    this.snackbar = true;
                    this.text = resp.data.message;
                    this.timeout = 3000;
                    this.closeDelete();
                    this.getSubadmins();
                });
            this.subadmins.splice(this.editedIndex, 1);
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
        subAdminUpdateSubmit() {
            if (this.editedIndex > -1) {
                this.isLoading = true;
                const config = {
                    headers: { 'content-type': 'multipart/form-data' }
                }
                let data = {
                    user_id: this.editedItem.id,
                    name: this.editedItem.name,
                    email: this.editedItem.email,
                    username: this.editedItem.username,
                    mobile_phone: this.editedItem.mobile_phone,
                    city_id: this.editedItem.city_id,
                    user_status: this.editedItem.user_status,
                }
                axios.post("/api/admin/subadmin/update", data, config)
                    .then((resp) => {
                        this.isLoading = false;
                        this.snackbar = true;
                        this.text = resp.data.message;
                        this.timeout = 3000;
                        this.getSubadmins();
                    }).catch(error=>{
                    this.error=error;
                });
                this.$refs.form.validate();
                Object.assign(this.subadmins[this.editedIndex], this.editedItem);
            } else {
                this.subadmins.push(this.editedItem);
            }
            this.close();
        },
        async submitCreateSubAdmin() {
            try {
                this.isLoading = true;

                // Debug log to verify data before sending it
                console.log('Submitting Sub-Admin Data:', this.newSubAdmin);

                const data = {
                    name: this.newSubAdmin.name,
                    email: this.newSubAdmin.email,
                    mobile_phone: this.newSubAdmin.mobile_phone, // Assuming mobile_phone is already validated
                    city_id: this.newSubAdmin.city_id,
                    username: this.newSubAdmin.username,
                    password: this.newSubAdmin.password,
                    user_status: this.newSubAdmin.user_status,
                    user_role: 4, // Assign sub-admin role
                    token: localStorage.getItem('token') || '', // Ensure token is retrieved correctly
                };

                // Debug log for the payload
                console.log('Payload being sent:', data);

                const response = await axios.post('/api/admin/add/subadmin', data);

                this.isLoading = false;

                // Check for success response
                if (response.data.status === 200) {
                    this.snackbar = true;
                    this.text = response.data.message || 'Sub-admin created successfully!';
                    this.createDialog = false;
                    this.getSubadmins(); // Refresh the list of sub-admins
                } else {
                    // Log failure response
                    console.error('Error response from API:', response.data);

                    this.snackbar = true;
                    this.text = response.data.message || 'Failed to create sub-admin.';
                }
            } catch (error) {
                this.isLoading = false;

                // Debug the error response
                console.error('Error creating sub-admin:', error.response ? error.response.data : error);

                this.snackbar = true;
                this.text = error.response?.data?.message || 'An error occurred while creating the sub-admin.';
            }
        }

    },
}
</script>
