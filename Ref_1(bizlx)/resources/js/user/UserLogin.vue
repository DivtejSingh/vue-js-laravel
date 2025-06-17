<template>
    <v-layout>
        <v-responsive class="mx-auto col-12 col-sm-4 login-form">
            <v-card class="my-5 border rounded-3">
                <form @submit.prevent="Login">
                    <v-radio-group v-model="selectedRole" row dense class="d-none">
                        <v-radio v-for="urole in userroles" :key="urole.id"
                            :id="urole.id"
                            :label="urole.label"
                            :value="urole.user_role"
                        ></v-radio>
                    </v-radio-group>
                    <v-card-title class="bgred text-white rounded-top h6">
                            <span v-for="urole in userroles" :key="urole.id">
                                <span v-if="selectedRole === urole.user_role">
                                    {{urole.title}}
                                </span>
                            </span>
                        </v-card-title>
                    <v-card-text>
                        <div>
                            <v-text-field
                                type="email"
                                placeholder="Email Address"
                                :rules="emailRules"
                                v-model="form1.email"
                                prepend-inner-icon="mdi-email"
                                required
                            ></v-text-field>
              
                            <!-- <v-text-field
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="Password"
                            :rules="passwordRules"
                            v-model="form1.password"
                            prepend-inner-icon="mdi-lock"
                            :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                            @click:append-inner="showPassword = !showPassword"
                            required
                            ></v-text-field> -->
                            
                            <v-text-field                            
                            v-model="form1.password"
                            :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"
                                              :rules="passwordRules"
                                              :type="show1 ? 'text' : 'password'" name="input-10-1"
                                              prepend-inner-icon="mdi-lock"
                                              placeholder="Password"
                                              @click:append="show1 = !show1"
                                ></v-text-field>
                        </div>
                        <div v-if="error" class="alert alert-danger">{{ error }}</div>
                        <div class="text-md-end text-end" v-if="selectedRole !== 4">
                            <a href="/password/reset" class="text-decoration-none">Forgot Password</a>
                        </div>
                        <div class="mt-3 justify-content-between d-flex align-center">
                            <v-btn type="submit" color="red" dark>Login</v-btn>
                            <span v-if="selectedRole === 0">Don't have an account?
                                <a href="/user/register" class="text-decoration-none">Sign up</a>
                            </span>
                            <span v-if="selectedRole === 1">Don't have an account?
                                <a href="/business/register" class="text-decoration-none">Sign up</a>
                            </span>
                            <span v-if="selectedRole === 2">Don't have an account?
                                <a href="/reseller/register" class="text-decoration-none">Sign up</a>
                            </span>
                            <span v-if="selectedRole === 4"></span>
                        </div>
                    </v-card-text>
                </form>
            </v-card>
    </v-responsive>
    </v-layout>
</template>

<script>
import axios from 'axios';

export default {
    name: "UserLogin",
    data() {
        return {
            show1: false,
            selectedRole: 0,
            userroles:[
                {id:1,user_role:0,title:'Login',label:'Login'},
                {id:2,user_role:1,title:'Login to Business Account',label:'Business'},
                {id:3,user_role:2,title:'Login to Service Agent Account',label:'Reseller '},
                {id:4,user_role:4,title:'Admin Login',label:'Admin '},
            ],
            token:'',
            userData:'',
            form1: {
                email: '',
                password: '',
            },
            error: '',
            emailRules: [
                v => !!v || 'E-mail is required.',
                v => /.+@.+/.test(v) || 'E-mail must be valid.',
            ],
            passwordRules: [
                v => !!v || 'Password is required.',
            ],
            wurl:'',
            loggedIn:false,
            user_role:'',
        }
    },
    created(){
        this.loggedIn = this.$store.state.isAuthenticated;
        this.user_role = this.$store.state.userData.user_role;
        if(this.loggedIn === true){
            if(this.user_role == 0){
                window.location.href = '/home'
            }
            if(this.user_role == 1){
                window.location.href = '/business/profile'
            }
            if(this.user_role == 2){
                window.location.href = '/reseller/business-list';
            }
            if(this.user_role == 3){
                window.location.href = '/admin/dashboard';
            }
            if(this.user_role == 4){
                window.location.href = '/admin/dashboard';
            }
        }

             this.wurl = window.location.href;
             if (this.wurl.includes('business')) {
                this.selectedRole = 1;
                } else if (this.wurl.includes('reseller')) {
                 this.selectedRole = 2;
                } else if (this.wurl.includes('admin')) {
                 this.selectedRole = 4;
                } else {
                this.selectedRole = 0;
            }

    },
    methods: {
        async Login() {
            try {
                const config = {
                headers: { 'content-type': 'multipart/form-data' }
            }
                await axios.post('/api/user/login', {
                    email: this.form1.email,
                    password: this.form1.password,
                    role: this.selectedRole
                },config).then((response)=>{
                    const token = response.data.token;
                    this.token = response.data.token;
                    const user = response.data.user;
                    const userData = response.data.user;
                    this.userData = response.data.user;
                    // Store token and user data in local storage
                localStorage.setItem('token', token);
                localStorage.setItem('user', JSON.stringify(user));

                // Dispatch the login action (if using Vuex)
                this.$store.dispatch('login', { token, userData });
                window.location.reload;

                });
                 await axios.post('/login', {
                    email: this.form1.email,
                    password: this.form1.password,
                },config).then((resp)=>{
                    window.location.reload;
                });

                if(this.userData.user_role == 1){
                        window.location.href = '/business/profile'
                    }
                    else if (this.userData.user_role == 2) {
                            window.location.href = '/reseller/business-list';
                        }
                    else if (this.userData.user_role == 4) {
                        window.location.href = '/admin/dashboard';
                    }
                    else {
                        window.location.href = '/home'
                }
            } catch (err) {
                this.error = err.response.data.message;
                console.log('Error:', err);
            }
        },
    }
}
</script>
