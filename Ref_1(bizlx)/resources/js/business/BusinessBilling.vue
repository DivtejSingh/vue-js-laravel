<template>
    <div class="pa-2">
      <h4 class="bg-red">Billing</h4>
      <h4 class="mt-3">You are currently on the {{ active_user_plan.plan_description }} plan.</h4>
      <p>Next billing date is {{expiration_date}}</p>
      <div class="row">
        <business-plans/>
      </div>
    </div>
  </template>
  <script>

  import axios from "axios";
  export default {
    name: 'BusinessBilling',
    data(){
      return{
        active_user_plan: [],
        expiration_date: [],
      }
    },
    mounted() {
      this.user_id();
      this.userdata = this.user_id();
      if(this.userdata.id != null){
        this.getPlansbyreseller();
      }else{
        this.getPlans();
      }

    },
    methods:{
      user_id() {
        const uid = localStorage.getItem('userId');
        try{
             return JSON.parse(uid);
        }
        catch(e)
        {
          return this.$store.state.userId;
        }
      },
      getPlans(){
        axios.get('/api/businesses/active/plan')
            .then((resp) =>{
              this.active_user_plan = resp.data.active_plan;
              this.expiration_date = resp.data.expiration_date;
            })
      },
      getPlansbyreseller(){
        axios.post('/api/businesses/active/planbyreseller',{userId:this.userdata.id})
            .then((resp) =>{
              this.active_user_plan = resp.data.active_plan;
              this.expiration_date = resp.data.expiration_date;
            })
      },
    }
  }
  </script>
