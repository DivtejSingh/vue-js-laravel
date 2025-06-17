<template>
<div class="allcats">
  <v-container>
    <div class="accordion mobile" role="tablist">
      <b-card no-body class="mb-1" v-for="cat in cats" :key="cat.id">
        <b-card-header header-tag="header" class="p-0" role="tab">
           <b-button class="catsy fw-bold cblu d-flex justify-content-between" block v-b-toggle:accordion="('accordion-'+cat.id)" variant="light w-100"
                     :aria-controls="('accordion-'+cat.id)">
             <img :src=(url+cat.cat_img_url) :alt="cat.cat_name" width="24">
            {{ cat.cat_name }}
          </b-button>
        </b-card-header>
        <b-collapse :id="('accordion-'+cat.id)" visible accordion="my-accordion" role="tabpanel">
          <b-card-body class="p-0">
            <b-card-text class="w-100">
              <b-list-group>
              <b-list-group-item v-for="(subcat,i) in cat.subcats" :key="i">
              <span class="clicked_cat" @click="ClickSearch(subcat.id,subcat.cat_name)">
                <img :src="(url+subcat.subcat_img_url)" alt="" width="18"> {{subcat.subcat_name}}
              </span>
              </b-list-group-item>
              </b-list-group>
            </b-card-text>
          </b-card-body>
        </b-collapse>
      </b-card>
    </div>
    <div class="card-columns mt-6 desktop">
      <v-col class="card-body mb-3" cols="12" md="12" v-for="cat in cats" :key="cat.id" >
        <b-list-group>
          <b-list-group-item class="bg-light font-weight-bold">
            <img :src=(url+cat.cat_img_url) :alt="cat.cat_name" width="32">
             {{ cat.cat_name }}
          </b-list-group-item>
              <b-list-group-item v-for="(subcat,i) in cat.subcats" :key="i">
              <span class="clicked_cat" @click="ClickSearch(subcat.id,subcat.subcat_name)">
                <img :src="(url+subcat.subcat_img_url)" alt="" width="24"> {{subcat.subcat_name}}
              </span>
              </b-list-group-item>
        </b-list-group>
      </v-col>
    </div>
  </v-container>

</div>
</template>

<script>
import axios from "axios";
export default {
  name: "AllCategories",
  metaInfo: {title: 'All Categories'},
  data() {
    return{
      cats:[],
      url:'https://bizlx.s3.ap-south-1.amazonaws.com'
    }
  },
  methods:{
    AllCats(){
      axios.get('/api/category')
          .then((resp)=>{
            this.cats = resp.data.categories.filter(cat => cat.cat_status === "1");
          });
    },

    // ClickSearch(subcatId,subcat_name){
    //       this.loggedUser = this.$store.state.userData;
    //         const config = {
    //           headers: { 'content-type': 'multipart/form-data' }
    //         }
    //         let data = {
    //             selectSubCategoty: subcatId,
    //             selectCity: '',
    //             deal: '',
    //             loggedUser_id: this.loggedUser.id,
    //         };
    //         axios.post("/api/search/category", data, config)
    //             .then((resp) => {
    //               localStorage.removeItem('catSearchedValues');
    //               localStorage.removeItem('jobSearchedValues');
    //                   if(resp.data.searched_data.length>0){
    //                     console.log("resp.data.searched_data", resp.data.searched_data);
    //                     const city_id = '';
    //                     this.$store.dispatch('setcityid',{city_id});
    //                     const subcat_id = subcatId;
    //                     this.$store.dispatch('setsubcatid',{subcat_id});
    //                     var catSearchedValues =  {
    //                         selectSubCategoty:subcatId,
    //                         selectSubCatname:resp.data.searched_data[0].subcat_name,
    //                         selectCity:'',
    //                         selectCityname:'',
    //                         jobcat_name:'',
    //                         deal:'',
    //                         loggedUser_id: this.loggedUser.id,
    //                     }
    //                     //localStorage.setItem('subcat_id',subcat_id);
    //                     window.location.href = '/searchall'
    //                     localStorage.setItem("catSearchedValues", JSON.stringify(catSearchedValues));
    //                   }else{
    //                     this.snackbar = true;
    //                     this.text = "Searched data not found.";
    //                   }
    //             },
    //         );
    // }

    ClickSearch(subcatId, subcat_name) {
  this.loggedUser = this.$store.state.userData;
  const config = {
    headers: { 'content-type': 'multipart/form-data' }
  };
  let data = {
    selectSubCategoty: subcatId,
    selectCity: '',
    deal: '',
    loggedUser_id: this.loggedUser.id,
  };

  axios.post("/api/search/category", data, config)
    .then((resp) => {
      localStorage.removeItem('catSearchedValues');
      localStorage.removeItem('jobSearchedValues');

      const city_id = '';
      this.$store.dispatch('setcityid', { city_id });
      const subcat_id = subcatId;
      this.$store.dispatch('setsubcatid', { subcat_id });

      var catSearchedValues = {
        selectSubCategoty: subcatId,
        selectSubCatname: subcat_name,
        selectCity: '',
        selectCityname: '',
        jobcat_name: '',
        deal: '',
        loggedUser_id: this.loggedUser.id,
      };

      localStorage.setItem("catSearchedValues", JSON.stringify(catSearchedValues));

      // Redirect to search page even if no data is found
      window.location.href = '/searchall';
    })
    .catch((error) => {
      this.snackbar = true;
      this.text = "An error occurred while searching.";
    });
}

  },
  mounted() {
    this.AllCats();
  }
}
</script>

<style scoped>
.clicked_cat {
  cursor: pointer;
}
@media(min-width: 576px){
  .card-columns {column-count: 3}
  .card-columns .card-body {display: inline-flex;width: 100%;}
  .card-columns .card-body .list-group{width: 100%}
  .mobile{display: none;}
}
@media (max-width: 576px) {
  .desktop{display: none;}
  .btn.catsy.collapsed::after {
    content: '+';
    color: var(--cblu);
  }
  .btn.catsy.not-collapsed::after {
    content: '-';
    color: var(--cblu);
  }
}


</style>
