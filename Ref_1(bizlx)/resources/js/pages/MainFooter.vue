<template>
    <div class="footer">
        <div class="topfooter bg-light py-3" >
            <div class="container">
                <div class="row gy-2 row-cols-2 row-cols-md-2 row-cols-lg-3 row-cols-xl-5 row-cols-xxl-6">
                    <div v-for="(footerdetail,i) in footerData" :key="i" class="col">
                        <h6>{{footerdetail.columns_name}}</h6>
                        <ul class="list-unstyled" v-for="(pages,i) in footerdetail.pages" :key="i">
                            <li><a :href="pages.page_link">{{pages.page_name}}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid bg-dark d-none d-sm-block">
            <v-row class="text-center">
                <v-col class="text-white copyright">Powered By : Bizlx.com™ - Daily Market Update, Hot Deals, Shopping, Sales ©All Rights Reserved 2023</v-col>
            </v-row>
        </div>
    </div>
</template>

<script>
export default {
    name: "MainFooter",
    data() {
        return {
            footerData:[]
        }
    },
    created(){
        this.getFooter();
    },
    methods:{
        async getFooter(){
            await axios.get("/api/footer/details")
                .then((resp) => {
                this.footerData = resp.data.footer_details;
            });
        }
    }
}
</script>

<style lang="scss" scoped>
.copyright {font-size: 12px;}
.topfooter a {
    color: rgba(0,0,0,.87);
    text-decoration: none;
}
</style>
