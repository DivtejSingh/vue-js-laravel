<template>
    <v-layout>
        <v-responsive class="mx-auto col-12 col-sm-4">
            <v-card class="my-5 border rounded-3">
                <form @submit.prevent="Login">
                    <v-card-title class="bgred text-white rounded-top">Login to Service Agent Account</v-card-title>
                    <v-card-text>
                        <v-text-field
                            type="email"
                            placeholder="Email Address"
                            :rules="emailRules"
                            v-model="form1.email"
                            prepend-inner-icon="mdi-email"
                            required
                        ></v-text-field>
                        <!-- <v-text-field
                            type="password"
                            placeholder="Password"
                            :rules="passwordRules"
                            v-model="form1.password"
                            prepend-inner-icon="mdi-lock"
                            required
                        ></v-text-field> -->

                        <v-text-field
                        :type="showPassword ? 'text' : 'password'"
                        placeholder="Password"
                        :rules="passwordRules"
                        v-model="form1.password"
                        prepend-inner-icon="mdi-lock"
                        :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                        @click:append-inner="showPassword = !showPassword"
                        required
                        ></v-text-field>

                        <div v-if="error" class="alert alert-danger">{{ error }}</div>
                        <div class="text-md-end text-end">
                            <a href="/password/reset" class="text-decoration-none">Forgot Password</a>
                        </div>
                        <div class="mt-3 justify-content-between d-flex align-center">
                            <v-btn type="submit" color="red" dark>Login</v-btn>
                            <span>Don't have an account?
                                <a href="/reseller/register" class="text-decoration-none">Sign up</a>
                            </span>
                        </div>
                    </v-card-text>
                </form>
            </v-card>
        </v-responsive>
    </v-layout>
</template>

<script>
export default {
    name: "ResellerLogin",
    data() {
        return {
            showPassword: false,
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
                // v => !!v || 'Password is required.',
                // v => /[A-Z]/.test(v) || 'Use upper case.',
                // v => /[a-z]/.test(v) || 'Use lower case.',
                // v => /[0-9]/.test(v) || 'Use number.',
                // v => /[#?!@$%^&*-]/.test(v) || 'Use special character.',
                // v => v.length >= 8 || 'Password must be greater than 8 characters.',
                v => v.length <= 15 || 'Password must be less than 15 characters.',
            ],
        }
    },
    created(){
        this.isAuthenticated();
        this.user_role();
        if( this.isAuthenticated() == true){
            if(this.user_role() == 1){
                window.location.href = '/business/profile';
            }
            if(this.user_role() == 0){
                window.location.href = '/user/login';
            }
            if(this.user_role() == 2){
                window.location.href = '/reseller/business-list';
            }
            if(this.user_role() == 3){
                window.location.href = 'admin/login';
            }
            if(this.user_role() == 4){
                window.location.href = '/admin/login';
            }
        }
    },
    methods: {
        async Login() {
            try {
                const response = await axios.post('/api/user/login', {
                    email: this.form1.email,
                    password: this.form1.password,
                });
                const Authresponse = await axios.post('/login', {
                    email: this.form1.email,
                    password: this.form1.password,
                });

                const { token, user } = response.data;

                // Store token and user data in local storage
                localStorage.setItem('token', token);
                localStorage.setItem('user', JSON.stringify(user));

                // Dispatch the login action (if using Vuex)
                this.$store.dispatch('login', { token, user });

                // Redirect user based on their role
                switch (user.user_role) {
                    case '0':
                        window.location.href = '/home';
                        break;
                    // case '1':
                    //     window.location.href = '/admin-dashboard';
                    //     break;
                    // // Add more cases as needed
                    // default:
                    //     window.location.href = '/login';
                }
            } catch (err) {
                this.error = err.response.data.message;
                console.log('Error:', err);
            }
        },
        isAuthenticated() {
            return this.$store.state.isAuthenticated;
        },
        user_role() {
            return this.$store.state.userData.user_role;
        },
    }
}
</script>

<style scoped>

</style>
