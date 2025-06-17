<template>
  <div class="pa-2">
    <v-tabs v-model="tab">
            <v-tab cols="6">WALLET</v-tab>
            <v-tab cols="6">EARNINGS</v-tab>
          </v-tabs>
    <v-tabs-items v-model="tab"> 
            <v-tab-item >
              <v-card color="basil" flat >
                <v-form>
                  <div class="row py-2">
                    <div class="col-12 col-md-8">
                      <h5 class="ms-4">Credits Available : 0 INR</h5>
                    </div>
                    <div class="col-12 col-md-2 py-1">
                      <v-text-field type="number" label="Amount"></v-text-field>
                    </div>
                    <div class="col-12 col-md-2 py-1">
                        <v-btn tile color="error" large>
                          ADD AMOUNT
                        </v-btn>
                    </div>
                  </div>
                </v-form>
              </v-card>
                <div class="mt-4">
                  <h5>Transaction details</h5>
                  <v-card>
                    <v-data-table :headers="headers" :items="businesslists" sort-by="calories"
                                  hide-header class="elevation-1">
                      <template v-slot:top>
                      </template>
                    </v-data-table>
                  </v-card>
                </div>
            </v-tab-item>
            <v-tab-item >
              <div class="mt-4">
                <h5 class="ms-4">Total Earnings: 0 INR</h5>
              </div>
              <div class="mt-4">
                <v-card>
                  <v-data-table :headers="headers" :items="businesslists" sort-by="calories"
                                hide-header class="elevation-1">
                    <template v-slot:top>
                    </template>
                  </v-data-table>
                </v-card>
              </div>
            </v-tab-item>
          </v-tabs-items>
  </div>
</template>
<script>
export default {
  name: 'ResellerPayment',
  components: {},
  data: () => ({
    tab: '',

    headers: [
      {text: "Business Name", align: "start", value: "business_name"},
      {text: "Plan", value: "plan"},
      {text: "Amount(INR)", value: "amount"},
      {text: "Discount(%)", value: "discount"},
      {text: "Discounted Price(%)", value: "discounted_price"},
      {text: "Earning(INR)", value: "earning"},
    ],
    businesslists: [],
  })
}
</script>