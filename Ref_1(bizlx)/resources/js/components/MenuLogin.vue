<template>
    <div>
        <v-menu offset-y>
            <template v-slot:activator="{ on, attrs }">
                <v-icon v-if="getUserlogged === ''" class="cblu" v-bind="attrs" v-on="on">mdi-account-tie</v-icon>
                <v-icon v-else class="cblu" v-bind="attrs" v-on="on">mdi-account-tie</v-icon>
            </template>
            <v-list class="py-0">
                <div v-if="getUser === '0'">
                    <v-list-item ><a href="/home">User Dashboard</a></v-list-item>
                    <user-logout></user-logout>
                </div>
                <div v-else-if="getUser === '1'">
                    <v-list-item ><a href="/business/profile">Business Dashboard</a></v-list-item>
                    <user-logout></user-logout>
                </div>
                <div v-else-if="getUser === '2'">
                    <v-list-item ><a href="/reseller/business-list">Reseller Dashboard</a></v-list-item>
                    <user-logout></user-logout>
                </div>
                <div v-else-if="getUser === '3'">
                    <v-list-item ><a href="/subadmin/dashboard">SubAdmin Dashboard</a></v-list-item>
                    <user-logout></user-logout>
                </div>
                <div v-else-if="getUser === '4'">
                    <v-list-item ><a href="/admin/dashboard">Admin Dashboard</a></v-list-item>
                    <user-logout></user-logout>
                </div>
                <div v-else>
                    <v-list-item @click="loginPopup">Login</v-list-item>
                </div>
            </v-list>
        </v-menu>
        <v-dialog v-model="loginDialog" max-width="300px" content-class="rounded-xl">
            <v-card>
                <v-card-text>
                    <div class="mb-0 d-flex align-items-center">
                        <v-col sm="11" cols="11" class="text-center h3 pb-0 text-dark">Login As</v-col>
                        <v-col sm="1" cols="1" class="pb-0">
                            <v-icon class="float-end" @click="closeDialog">mdi-close</v-icon>
                        </v-col>
                    </div>
                    <v-row class="align-items-center justify-content-between">
                        <v-col cols="8" class="py-0">
                            <v-radio-group v-model="selectedLogin" class="mt-0" hide-details>
                                <v-radio
                                    v-for="n in logintype"
                                    :key="n.id"
                                    :label="n.name"
                                    hide-details
                                    :value="n.id"
                                ></v-radio>
                            </v-radio-group>
                        </v-col>
                        <v-col cols="4" class="text-end py-0">
                            <v-btn class="go" color="red" small dark @click="goToRoute">Go</v-btn>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-actions>
                    <span>Don't Have Account</span>
                    <v-spacer></v-spacer>
                     <v-btn small text color="red" @click="switchRegdialog">Register</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="regDialog" max-width="300px" content-class="rounded-xl">
            <v-card>
                <v-card-text>
                    <div class="mb-0 d-flex align-items-center">
                        <v-col sm="11" cols="11" class="text-center h3 pb-0 text-dark">Register As</v-col>
                        <v-col sm="1" cols="1" class="pb-0">
                            <v-icon class="float-end" @click="closeDialog">mdi-close</v-icon>
                        </v-col>
                    </div>
                    <v-row class="align-items-center justify-content-between">
                        <v-col cols="8" class="py-0">
                            <v-radio-group v-model="selectedRegister" class="mt-0" hide-details>
                                <v-radio
                                    v-for="n in logintype"
                                    :key="n.id"
                                    :label="n.name"
                                    hide-details
                                    :value="n.id"
                                ></v-radio>
                            </v-radio-group>
                        </v-col>
                        <v-col cols="4" class="text-end py-0">
                            <v-btn class="go" color="red" small dark @click="goToReg">Go</v-btn>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-actions>
                    <span>Already Have Account</span>
                    <v-spacer></v-spacer>
                    <v-btn small text color="red" @click="switchLogindialog">Login</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
export default {
    name: "MenuLogin",
    data() {
        return {
            drawer: false,
            group: null,
            token: '',
            user: '',
            loginDialog: false,
            regDialog: false,
            selectedLogin: 1,
            selectedRegister: 1,
            logintype:[{id:1,name:"Public"},{id:2,name:"Business"},{id:3,name:"Service Agent"}],
            loggedUser: [],
            options:[{name:"Android"},{name:"IOS"}]
        }
    },
    computed: {
        getUser(){
            return this.$store.state.userData.user_role;
        },
        getUserlogged(){
            return this.$store.state.userData;
        }
    },
    methods:{
        switchRegdialog(){
            this.loginDialog = false;
            this.regDialog = true;
        },
        switchLogindialog(){
            this.loginDialog = true;
            this.regDialog = false;
        },
        userLogout() {
            this.$store.dispatch('logout');
            window.location.href = '/user/login';
        },
        businessLogout() {
            this.$store.dispatch('logout');
            window.location.href = '/business/login';
        },
        resellerLogout() {
            this.$store.dispatch('logout');
            window.location.href = '/reseller/login';
        },
        subadminLogout() {
            this.$store.dispatch('logout');
            window.location.href = '/admin/login';
        },
        adminLogout() {
            this.$store.dispatch('logout');
            window.location.href = '/admin/login';
        },
        loginPopup(){
            this.loginDialog = true;
        },
        closeDialog(){
            this.loginDialog = false;
            this.regDialog = false;
        },
        goToRoute() {
            if (this.selectedLogin == 1) {
                window.location.href = '/user/login';
            } else if (this.selectedLogin == 2) {
                window.location.href = '/business/login';
            } else if (this.selectedLogin == 3) {
                window.location.href = '/reseller/login';
            }
            this.loginDialog = false;
        },
        goToReg() {
            if (this.selectedRegister == 1) {
                window.location.href = '/user/register';
            } else if (this.selectedRegister == 2) {
                window.location.href = '/business/register';
            } else if (this.selectedRegister == 3) {
                window.location.href = '/reseller/register';
            }
            this.regDialog = false;
        }
    }
}
</script>

<style scoped>

</style>
