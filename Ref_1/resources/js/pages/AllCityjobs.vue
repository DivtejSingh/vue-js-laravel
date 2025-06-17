<template>
  <v-container class="bg-white">
    <v-banner><h2 class="h5"><span class="cblu bbred">All City Jobs</span></h2></v-banner>
    <v-row class="py-0 py-md-5">
      <v-col cols="6" md="2" v-for="(job,i) in cities" :key="i">
        <v-card>
          <span class="cursor" @click="sendCity(job.id,job.city)">
            <v-card-text class="text-center h6">
              <span class="me-1">{{job.city}}</span>
              <span class="text--white badge bg-dark">{{job.jobcount}}</span>
            </v-card-text>
          </span>
        </v-card>
      </v-col>

    </v-row>
  </v-container>
</template>

<script>
import axios from "axios";

export default {
  name: "AllCityjobs",
  props: {
    data1:Object
  },
  data (){
    return{
      cities:[],
    }
  },
  mounted() {
    this.getAllcities();
  },
  methods:{
    getAllcities(){
      axios.get('/api/jobcategory/allcities')
          .then((resp) =>{
            this.cities = resp.data.allcities;
          })
    },
    sendCity(cityid,cityname){
        const dealtype = 2
        this.$store.dispatch('setdealtypeid',{dealtype});
        const city_id = cityid;
        this.$store.dispatch('setcityid',{city_id});
        const jobcat_id = '';
        this.$store.dispatch('setjobcatid',{jobcat_id});
        let data = {
                selectSubCategoty: '',
                selectSubCatname: '', // Check if matchedCategory exists
                selectCity: cityid,
                selectCityname: cityname, // Check if matchedCity exists
                jobcat_name:'',
                deal: 'Jobs',
            };
        localStorage.setItem('catSearchedValues', JSON.stringify(data));
        window.location.href = '/searchall';
      }
  }
}
</script>
<style scoped>
.cursor{
  cursor: pointer;
}
</style>
