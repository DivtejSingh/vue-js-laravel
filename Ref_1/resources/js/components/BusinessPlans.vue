<template>
  <div class="cmsp py-3">
      <div class="row cmsplan g-5 text-center justify-content-center">
        <div class="col-12 col-md-4" v-for="(plan,i) in plans" :key="i">
        <div class="base shadow rounded-3 px-1 p2" :id="plan.id">
          <div class="mtitle">{{plan.plan_description}}</div>
          <b-list-group flush>
            <div>
              <b-list-group-item v-if="plan.plan_price == 0" class="fw-bold mprice">FREE 0</b-list-group-item>
              <b-list-group-item v-else class="fw-bold mprice">INR : {{plan.plan_price}}</b-list-group-item>
            </div>
            
            <div>
              <b-list-group-item v-if="plan.images > 0">
                <b-icon icon="check2-square"></b-icon>Images : <b>{{ plan.images }}</b>
              </b-list-group-item>
              <b-list-group-item v-else>
                <b-icon icon="x-circle"></b-icon>Images : <b>No</b>
              </b-list-group-item>
            </div>

            <div>
              <b-list-group-item v-if="plan.hot_deals > 0">
                <b-icon icon="check2-square"></b-icon>Hot Deals : <b>{{ plan.hot_deals }}</b>
              </b-list-group-item>
              <b-list-group-item v-else>
                <b-icon icon="x-circle"></b-icon>Hot Deals : <b>No</b>
              </b-list-group-item>
            </div>

            <div>
              <b-list-group-item v-if="plan.sale > 0">
                <b-icon icon="check2-square"></b-icon>Sales : <b>{{ plan.sale }}</b>
              </b-list-group-item>
              <b-list-group-item v-else>
                <b-icon icon="x-circle"></b-icon>Sales : <b>No</b>
              </b-list-group-item>
            </div>

            <div>
              <b-list-group-item v-if="plan.jobs > 0">
                <b-icon icon="check2-square"></b-icon>Jobs : <b>{{ plan.jobs }}</b>
              </b-list-group-item>
              <b-list-group-item v-else>
                <b-icon icon="x-circle"></b-icon>Jobs : <b>No</b>
              </b-list-group-item>
            </div>
            
            <div>
              <b-list-group-item v-if="plan.video > 0">
                <b-icon icon="check2-square"></b-icon>Videos : <b>{{ plan.video }}</b>
              </b-list-group-item>
              <b-list-group-item v-else>
                <b-icon icon="x-circle"></b-icon>Videos : <b>No</b>
              </b-list-group-item>
            </div>

            <div>
              <b-list-group-item v-if="plan.about > 0">
                <b-icon icon="check2-square"></b-icon>About : <b>{{ plan.about }}</b>
              </b-list-group-item>
              <b-list-group-item v-else>
                <b-icon icon="x-circle"></b-icon>About : <b>No</b>
              </b-list-group-item>
            </div>

            <div>
              <b-list-group-item v-if="plan.home_page_banner > 0">
                <b-icon icon="check2-square"></b-icon>Home Page Banner : <b>{{ plan.home_page_banner }}</b>
              </b-list-group-item>
              <b-list-group-item v-else>
                <b-icon icon="x-circle"></b-icon>Home Page Banner : <b>No</b>
              </b-list-group-item>
            </div>

            <div>
              <b-list-group-item v-if="plan.services == 1">
                <b-icon icon="check2-square"></b-icon>Services : <b>{{ plan.services }}</b>
              </b-list-group-item>
              <b-list-group-item v-else>
                <b-icon icon="x-circle"></b-icon>Services : <b>No</b>
              </b-list-group-item>
            </div>

            <div>
              <b-list-group-item v-if="plan.contact > 0">
                <b-icon icon="check2-square"></b-icon>Contact : <b>Yes</b>
              </b-list-group-item>
              <b-list-group-item v-else>
                <b-icon icon="x-circle"></b-icon>Contact : <b>No</b>
              </b-list-group-item>
            </div>

            <div>
              <b-list-group-item v-if="plan.reviews == 1">
                <b-icon icon="check2-square"></b-icon>Reviews : <b>Yes</b>
              </b-list-group-item>
              <b-list-group-item v-else>
                <b-icon icon="x-circle"></b-icon>Reviews : <b>No</b>
              </b-list-group-item>
            </div>

            <div>
              <b-list-group-item v-if="plan.featured_city > 1">
                <b-icon icon="check2-square"></b-icon>Featured City : <b>{{ plan.featured_city }}</b>
              </b-list-group-item>
              <b-list-group-item v-else>
                <b-icon icon="x-circle"></b-icon>Featured City : <b>No</b>
              </b-list-group-item>
            </div>

            <div v-if="plan.featured_category > 1">
              <b-list-group-item>
                <b-icon icon="check2-square"></b-icon>Featured Category : <b>{{ plan.featured_category }}</b>
              </b-list-group-item>
            </div>
            <div v-else>
              <b-list-group-item>
                <b-icon icon="x-circle"></b-icon>Featured Category : <b>No</b>
              </b-list-group-item>
            </div>
          </b-list-group>
            <div v-if="ulogged == 1">
              <div class="my-3 btn btn-outline-light">BUY NOW</div>
            </div>
            <div v-else-if="ulogged > 1">
                <div v-if="active_user_plan.plan_id==null" class="my-3 btn btn-outline-light">BUY NOW</div>
                <div v-else-if="active_user_plan.plan_id==plan.id" class="my-3 btn btn-outline-light white" style="color: black;">ACTIVE PLAN</div>
                <div v-else class="my-3 btn btn-outline-light">BUY NOW</div>
            </div>

        </div>
        </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "BusinessPlans",
  data(){
    return{
      plans: [],
      active_user_plan: [],
      ulogged :'',
    }
  },
  mounted() {
    this.user_id();
    this.userdata = this.user_id();
    if(this.userdata.id != null){
      this.getPlansbyreseller();
      this.ulogged = 2;
    }else{
      this.ulogged = 3;
      this.getPlans();
    }
    this.allPlans();
  },
  methods:{
    user_id() {
      return this.$store.state.userId;
    },
    allPlans(){
      axios.get('/api/plans')
          .then((resp)=>{
            this.plans = resp.data.plans;
            this.ulogged = 1;
          })
    },
    getPlans(){
      axios.get('/api/businesses/billing/plan')
          .then((resp) =>{
            this.plans = resp.data.plans;
            this.active_user_plan = resp.data.active_user_plan;
          })
    },
    getPlansbyreseller(){
      axios.post('/api/businesses/billing/planbyreseller',{userId:this.userdata.id})
          .then((resp) =>{
            this.plans = resp.data.plans;
            this.active_user_plan = resp.data.active_user_plan;
          })
    },
  },
}
</script>

<style scoped>
.base#\31 {border: 1px solid var(--bs-primary);background-color: var(--bs-primary);}
.base#\32 {border: 1px solid var(--bs-secondary);background-color: var(--bs-secondary);}
.base#\33 {border: 1px solid var(--bs-warning);background-color: var(--bs-warning);}
.base#\34{border: 1px solid var(--bs-success);background-color: var(--bs-success);}
.base#\35 {border: 1px solid var(--bs-info);background-color: var(--bs-info);}
.base#\36 {border: 1px solid var(--bs-danger);background-color: var(--bs-danger);}
.base#\37 {border: 1px solid var(--bs-dark);background-color: var(--bs-dark);}
.base#\38 {border: 1px solid var(--bs-warning);background-color: var(--bs-warning);}
.base#\31 .mtitle {background-color: var(--bs-primary);}
.base#\32 .mtitle {background-color: var(--bs-secondary);}
.base#\33 .mtitle {background-color: var(--bs-warning);}
.base#\34 .mtitle {background-color: var(--bs-success);}
.base#\35 .mtitle {background-color: var(--bs-info);}
.base#\36 .mtitle {background-color: var(--bs-danger);}
.base#\37 .mtitle {background-color: var(--bs-dark);}
.base#\38 .mtitle {background-color: var(--bs-warning);}
.base .bi-check2-square.b-icon.bi {color: var(--bs-success);margin-right: 3px;}
.base .bi-x-circle.b-icon.bi {color: var(--bs-danger);margin-right: 3px;}
.base svg {font-size: 150%;}
.mtitle {
  /*background: red;*/
  position: relative;
  width: calc(100% + 16px);
  padding: 10px;
  left: -8px;
  font-weight: 700;
  color: #fff;
  border-radius: 8px 8px 0 0;
  font-size: 1.5rem;
}
.list-group.list-group-flush > .list-group-item {
  border-width: 0 0 var(--bs-list-group-border-width);
  }
.base .mprice {font-size: 1.5rem;}
.month{font-weight: 400;}
.fp1, .fp2, .fp3 {font-weight: 700;}
.fp1 {color: var(--bs-primary);}
.fp2 {color: red;}
.fp3 {color: blueviolet;}
.fptext{display: none;}
.base .list-group-item {font-size: 85%;}
</style>