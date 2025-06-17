<template>
    <v-list-item @click="logout">
        <v-list-item-icon>
            <v-icon size="20">mdi-logout</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
            <v-list-item-title>
                Logout
            </v-list-item-title>
        </v-list-item-content>
    </v-list-item>
</template>

<script>
export default {
    name: "UserLogout",
    methods: {
        logout() {
            // Dispatch the Vuex action if needed
            this.$store.dispatch('logout').then(() => {
                // Submit the form to log out
                document.getElementById('logout-form').submit();
            }).catch(() => {
                // If there's no Vuex action or you handle logout differently
                document.getElementById('logout-form').submit();
            });
        }
    }
}
</script>

<style scoped>

</style>
