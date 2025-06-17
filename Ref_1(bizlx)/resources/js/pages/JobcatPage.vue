 <template>
  <div>
    <v-container class="bg-white">
      <v-banner single-line class="h5">
        <img width="32" height="32" :src="url+alljobs.jobcatid.job_img_url" :alt="alljobs.jobcatid.job_cat_name">
        All {{alljobs.jobcatid.job_cat_name}} Jobs
      </v-banner>
      <div v-if="alljobs.jobs.length > 0">
        <v-row class="py-0 py-md-5">
         <v-col cols="6" md="2" v-for="(job,i) in alljobs.jobs" :key="i">
            <v-card>
              <span class="cursor" @click="sendCity(job.id,job.city)">
                <v-card-text class="text-center h6">
                  <span class="me-1">{{job.city}}</span>
                  <span class="text--white badge bg-dark">{{job.jobcount}}</span>
                </v-card-text>
              </span>
            </v-card>
        </v-col>
          <v-col cols="12" md="12" class="text-center">
            <v-btn small class="primary" @click="sendCat()">
              <v-icon small class="me-1">mdi-eye</v-icon>
              More Jobs
            </v-btn>
          </v-col>
      </v-row>
      </div>
      <div v-else class="text-center">
        <v-row class="py-0 py-md-5">
          <v-col cols="12" md="12" class="text-center">
            <p class="h5">Job not found in {{alljobs.jobcatid.job_cat_name}}</p>
          </v-col>
          <v-col cols="12" md="12" class="text-center">
            <v-btn small class="primary" @click="sendCat">
              <v-icon small class="me-1">mdi-eye</v-icon>
              More Jobs
            </v-btn>
          </v-col>
        </v-row>
      </div>
    </v-container>
  </div>

  </template>
  
<script>
  import axios from "axios";

  export default {
    name: "JobcatPage",
    props: {
         id: Number,
         data1:Object
    },
    data (){
      return{
        alljobs: [],
        job_cat_slug:'',
        jobcatname:'',
        url:'https://bizlx.s3.ap-south-1.amazonaws.com'
      }
    },
    mounted() {
      this.getJobcategory();
    },
    methods:{
      getJobcategory() {
         axios.get(`/api/jobcategory/${this.data1.job_cat_slug}`)
            .then((resp) =>{
              this.alljobs = resp.data;
              this.jobcatname = resp.data.jobcatid.job_cat_name;
            });
      },
      sendCity(cityid,cityname){
        const dealtype = 2
        this.$store.dispatch('setdealtypeid',{dealtype});
        const city_id = cityid;
        this.$store.dispatch('setcityid',{city_id});
        const jobcat_id = this.alljobs.jobcatid.id;
        this.$store.dispatch('setjobcatid',{jobcat_id});
        const subcat_id = '';
        this.$store.dispatch('setsubcatid',{subcat_id});
        let data = {
                selectSubCategoty: this.alljobs.jobcatid.id,
                selectSubCatname: '', // Check if matchedCategory exists
                selectCity: cityid,
                selectCityname: cityname, // Check if matchedCity exists
                jobcat_name:this.jobcatname,
                deal: 'Jobs',
            };
        localStorage.setItem('catSearchedValues', JSON.stringify(data));
        window.location.href = '/searchall';
      },
      sendCat(){
        const dealtype = 2
        this.$store.dispatch('setdealtypeid',{dealtype});
        const city_id = '';
        this.$store.dispatch('setcityid',{city_id});
        const jobcat_id = this.alljobs.jobcatid.id;
        this.$store.dispatch('setjobcatid',{jobcat_id});
        const subcat_id = '';
        this.$store.dispatch('setsubcatid',{subcat_id})
        const cityname = '';
        let data = { 
                selectSubCategoty: this.alljobs.jobcatid.id,
                selectSubCatname: '', // Check if matchedCategory exists
                selectCity: '',
                selectCityname: '', // Check if matchedCity exists
                jobcat_name:this.jobcatname,
                deal: 'Jobs',
                //deal: 'Jobs',
            };
        localStorage.setItem('catSearchedValues', JSON.stringify(data));
        window.location.href = '/searchall';
      }
    }
  }
</script>
  
  <style scoped>
  .v-banner__text {
    display: flex;
}
.cursor{
  cursor: pointer;
}
  </style>