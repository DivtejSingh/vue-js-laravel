<template>
  <div>
    <h2>Deal Reports</h2>
    <v-container>
      <!-- (1) LOCATION-BASED FILTERS -->
      <!-- FILTERS SECTION -->

  <v-row dense>
    <!-- Location Filters -->
    <v-col cols="12" md="3">
      <v-autocomplete
        v-model="selCountry"
        :items="countries"
        label="Country"
        dense
        clearable
        item-text="country"
        @change="onCountryChange"
      />
    </v-col>

    <v-col cols="12" md="3">
<v-autocomplete
  v-model="selState"
  :search-input.sync="stateSearch"
  :items="(stateSearch || '').length > 0 ? filteredStates : []"
  label="State"
  dense
  clearable
  item-text="state"
  @change="onStateChange"
/>
    </v-col>

    <v-col cols="12" md="3">
<v-autocomplete
  v-model="selCity"
  :search-input.sync="citySearch"
  :items="(citySearch || '').length > 0 ? filteredCities : []"
  label="City"
  dense
  clearable
  item-text="city"
  @change="onCityChange"
/>
    </v-col>

    <v-col cols="12" md="3">
<v-autocomplete
  v-model="selSubcat"
  :search-input.sync="subcatSearch"
:items="(subcatSearch || '').length > 0 ? subcats : []"
  label="Category"
  dense
  clearable
  item-text="subcat_name"
  @change="filterAllData"
/>
    </v-col>

    <!-- Deal Type & Dates -->
    <v-col cols="12" md="3">
      <v-autocomplete
        v-model="selectedDealType"
        :items="dealTypes"
        label="Deal Type"
        dense
        clearable
        item-text="label"
        @change="onDealTypeChange"
      />
    </v-col>

    <!-- <v-col cols="12" md="3">
      <v-menu v-model="fromDateMenu" :close-on-content-click="false" transition="scale-transition" offset-y>
        <template v-slot:activator="{ on, attrs }">
          <v-text-field
            v-model="fromDate"
            label="From Date"
            prepend-icon="mdi-calendar"
            readonly
            dense
            clearable
            v-bind="attrs"
            v-on="on"
          />
        </template>
        <v-date-picker v-model="fromDate" @change="fromDateMenu = false" :max="toDate || today" />
      </v-menu>
    </v-col>

    <v-col cols="12" md="3">
      <v-menu v-model="toDateMenu" :close-on-content-click="false" transition="scale-transition" offset-y>
        <template v-slot:activator="{ on, attrs }">
          <v-text-field
            v-model="toDate"
            label="To Date"
            prepend-icon="mdi-calendar"
            readonly
            dense
            clearable
            v-bind="attrs"
            v-on="on"
          />
        </template>
        <v-date-picker v-model="toDate" @change="toDateMenu = false" :min="fromDate" :max="today" />
      </v-menu>
    </v-col> -->
<!-- FROM DATE -->
<v-col cols="12" md="3">
  <v-dialog v-model="fromDateDialog" width="280PX">
    <template v-slot:activator="{ on, attrs }">
      <v-text-field
        v-bind="attrs"
        v-on="on"
        :value="formatDate(fromDate)"
        label="From Date"
        prepend-icon="mdi-calendar"
        readonly
        dense
        clearable
        @click:clear="fromDate = null"
      />
    </template>
    <v-card>
      <v-date-picker
        v-model="fromDate"
        :max="toDate || today"
        scrollable
        show-current
        color="primary"
        @change="fromDateDialog = false"
      />
    </v-card>
  </v-dialog>
</v-col>

<!-- TO DATE -->
<v-col cols="12" md="3">
  <v-dialog v-model="toDateDialog"  width="280PX">
    <template v-slot:activator="{ on, attrs }">
      <v-text-field
        v-bind="attrs"
        v-on="on"
        :value="formatDate(toDate)"
        label="To Date"
        prepend-icon="mdi-calendar"
        readonly
        dense
        clearable
        @click:clear="toDate = null"
      />
    </template>
    <v-card>
      <v-date-picker
        v-model="toDate"
        :min="fromDate"
        :max="today"
        scrollable
        show-current
        color="primary"
        @change="toDateDialog = false"
      />
    </v-card>
  </v-dialog>
</v-col>

    <!-- Action Buttons -->
    <v-col cols="12" md="3" class="d-flex align-center">
      <v-btn color="primary" class="mr-2" @click="filterByCustomDate">Apply Filters</v-btn>
      <v-btn color="grey" text @click="resetFilters">Reset</v-btn>
    </v-col>
  </v-row>



      <!-- (3) SUMMARY FOR MAIN DATA (ALWAYS THE 'ALLPROS' FILTERED LIST) -->
      <v-row class="my-3">
        <v-col cols="3">
          <div>Total Business: {{ filteredPros.length }}</div>
        </v-col>
        <v-col cols="3">
          <div>Total Deals: {{ totalDeals }}</div>
        </v-col>
        <v-col cols="3">
          <div>Total Sales: {{ totalSales }}</div>
        </v-col>
        <v-col cols="3">
          <div>Total Jobs: {{ totalJobs }}</div>
        </v-col>
      </v-row>

      <!-- (4) CONDITIONAL TABLES PER DEAL TYPE -->
      <!-- When Deal Type is "All" or empty, show the UNIFIED table -->
      <div v-if="selectedDealType === 'All' || !selectedDealType">
        <!-- <v-data-table :items="filteredPros" :headers="aproheader" dense> -->
          <v-data-table :items="filteredPros" :headers="aproheader" dense>
  <template v-slot:[`item.created_at`]="{ item }">
    {{ formatDates(item.created_at) }}
  </template>


          <template v-slot:[`item.dcount`]="{ item }">
            <v-btn color="success" text small>{{ item.deals_count }}</v-btn>
          </template>
          <template v-slot:[`item.scount`]="{ item }">
            <v-btn color="success" text small>{{ item.sales_count }}</v-btn>
          </template>
          <template v-slot:[`item.jcount`]="{ item }">
            <v-btn color="success" text small>{{ item.jobs_count }}</v-btn>
          </template>
        </v-data-table>
      </div>

      <!-- When Deal Type is "Hot Deals," show the DEALS table (with location filters + search) -->
      <div v-else-if="selectedDealType === 'Hot Deals'">
        <!-- <v-text-field
          v-model="dsearch"
          dense
          solo
          clearable
          prepend-inner-icon="mdi-magnify"
          label="Search Hot Deals"
          class="my-2"
        ></v-text-field> -->
        <v-data-table
          dense
          :items="filteredDeals"
          :headers="dealheader"
          :search="dsearch"
        >
        <template v-slot:[`item.created_at`]="{ item }">
    {{ formatDates(item.created_at) }}
  </template>
          <template v-slot:[`item.date_to`]="{ item }">
            {{ item.date_to | fdate }}
          </template>
          <template v-slot:[`item.price_to`]="{ item }">
            ₹{{ item.price_to }}
          </template>
          <template v-slot:item.hotdeal_slug="{ item }">
  <a :href="'/deals/detail/' + item.hotdeal_slug" target="_blank">View</a>
</template>
          
<template v-slot:[`item.hot_deal_status`]="{item}">
                                <v-btn v-if="item.hot_deal_status === 1" x-small dark color="green">Active</v-btn>
                                <v-btn v-else x-small dark color="red">InActive</v-btn>
                              </template>
        </v-data-table>
      </div>

      <!-- When Deal Type is "Sales," show the SALES table (with location filters + search) -->
      <div v-else-if="selectedDealType === 'Sales'">
        <!-- <v-text-field
          v-model="ssearch"
          dense
          solo
          clearable
          prepend-inner-icon="mdi-magnify"
          label="Search Sales"
          class="my-2"
        ></v-text-field> -->
        <v-data-table
          dense
          :items="filteredSales"
          :headers="saleheader"
          :search="ssearch"
        >
        <template v-slot:[`item.created_at`]="{ item }">
    {{ formatDates(item.created_at) }}
  </template>
          <template v-slot:[`item.saledate_to`]="{ item }">
            {{ item.saledate_to | fdate }}
          </template>
          <template v-slot:item.sale_slug="{ item }">
  <a :href="'/sales/detail/' + item.sale_slug" target="_blank">View</a>
</template>
          <template v-slot:[`item.sale_price`]="{ item }">
            ₹{{ item.sale_price }}
          </template>
          <template v-slot:[`item.normal_price`]="{ item }">
            <del>₹{{ item.normal_price }}</del>
          </template>
          <template v-slot:[`item.sale_status`]="{item}">
                            <v-btn v-if="item.sale_status === 1" x-small dark color="green">Active</v-btn>
                            <v-btn v-else x-small dark color="red">InActive</v-btn>
                          </template>
        </v-data-table>
      </div>

      <!-- When Deal Type is "Jobs," show the JOBS table (with location filters + search) -->
      <div v-else-if="selectedDealType === 'Jobs'">
        <!-- <v-text-field
          v-model="jsearch"
          dense
          solo
          clearable
          prepend-inner-icon="mdi-magnify"
          label="Search Jobs"
          class="my-2"
        ></v-text-field> -->
        <v-data-table
          dense
          :items="filteredJobs"
          :headers="jobheader"
          :search="jsearch"
        >
        <template v-slot:[`item.created_at`]="{ item }">
    {{ formatDates(item.created_at) }}
  </template>


        <template v-slot:item.job_slug="{ item }">
  <a :href="'/jobs/detail/' + item.job_slug" target="_blank">View</a>
</template>
          <template v-slot:[`item.salary_from`]="{ item }">
            ₹{{ item.salary_from }}
          </template>
          <template v-slot:[`item.job_status`]="{item}">
                            <v-btn v-if="item.job_status === 1" x-small dark color="green">Active</v-btn>
                            <v-btn v-else x-small dark color="red">InActive</v-btn>
                          </template>
        </v-data-table>
      </div>
    </v-container>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'DealReports',
  data() {
    
    return {
      fromDate: null,
      toDate: null,
      fromDateMenu: false,
      toDateMenu: false,
      today: new Date().toISOString().substr(0, 10),

      // (A) Filters: location
      countries: [],
      states: [],
      cities: [],
      subcats: [],
      selCountry: null,
      selState: null,
      selCity: null,
      selSubcat: null,
  stateSearch: '',
      citySearch: '',
      subcatSearch: '',
      // (B) Filter: deal type
      dealTypes: ['All', 'Hot Deals', 'Sales', 'Jobs'],
      selectedDealType: 'All',
fromDateDialog: false,
toDateDialog: false,

      // (C) Main data arrays
      allpros: [],    // unified list for "All"
      deals: [],      // hot deals data
      sales: [],      // sales data
      jobs: [],       // jobs data

      // (D) Filtered data arrays
      filteredPros: [],    // unified
      // We won't store separate "filteredDeals / filteredSales / filteredJobs" in data;
      // we'll use computed props so location filters apply automatically.

      // (E) Headers
      aproheader: [
        // { text: 'id', value: 'user_id' },
        { text: 'Business', value: 'name' },
        { text: 'Country', value: 'country' },
        { text: 'State', value: 'state' },
        { text: 'City', value: 'city' },
        { text: 'Subcat', value: 'subcat_name' },
        { text: 'Deals', value: 'dcount' },
        { text: 'Sales', value: 'scount' },
        { text: 'Jobs', value: 'jcount' },
        { text: 'Username', value: 'username' },
        { text: 'Created At', value: 'created_at' }
      ],
      dealheader: [
        { text: 'Business', value: 'name', groupable: false },
        { text: 'Category', value: 'subcat_name' },
        { text: 'Country', value: 'country' },
        { text: 'State', value: 'state' },
        { text: 'City', value: 'city' },
        { text: 'Title', value: 'hot_deal_title' },
        { text: 'Link', value: 'hotdeal_slug' },
        { text: 'Date to', value: 'date_to' },
        { text: 'Price to', value: 'price_to' },
        { text: 'Status', value: 'hot_deal_status' },
        { text: 'Created At', value: 'created_at' }

      ],
      saleheader: [
        { text: 'Business', value: 'name', groupable: false },
        { text: 'Category', value: 'subcat_name' },
        { text: 'Country', value: 'country' },
        { text: 'State', value: 'state' },
        { text: 'City', value: 'city' },
        { text: 'Title', value: 'sale_title' },
        { text: 'Link', value: 'sale_slug' },
        { text: 'Date To', value: 'saledate_to' },
        { text: 'Sale Price', value: 'sale_price' },
        { text: 'Normal Price', value: 'normal_price' },
        { text: 'Status', value: 'sale_status' },
        { text: 'Created At', value: 'created_at' }

      ],
      jobheader: [
        { text: 'Business', value: 'name', groupable: false },
        { text: 'Category', value: 'job_cat_name' },
        { text: 'Country', value: 'country' },
        { text: 'State', value: 'state' },
        { text: 'City', value: 'city' },
        { text: 'Title', value: 'job_title' },
        { text: 'Link', value: 'job_slug' },
        { text: 'Experience', value: 'min_exp' },
        { text: 'Salary From', value: 'salary_from' },
        { text: 'Status', value: 'job_status' },
        { text: 'Created At', value: 'created_at' }

      ],

      // (F) Searching for each type
      dsearch: '',
      ssearch: '',
      jsearch: '',

      // (G) For dynamic filtering
      filteredStates: [],
      filteredCities: [],
      dtype: 3, // or whatever your default is
    };
  },
  computed: {
    // Summaries for the "All" table
    totalDeals() {
  return this.filteredDeals.length;
},
    totalSales() {
      return this.filteredPros.reduce((sum, item) => sum + item.sales_count, 0);
    },
    totalJobs() {
      return this.filteredPros.reduce((sum, item) => sum + item.jobs_count, 0);
    },

    // (H) Computed arrays for *type-specific* data that also respect location filters
    // This ensures your location filters (country/state/city/subcat) apply
    // to all data sets. That way, even if you pick "Hot Deals," it only shows
    // deals from the selected country/state/city.

    filteredDeals() {
      return this.deals.filter(item => this.matchesLocationFilters(item));
    },
    filteredSales() {
      return this.sales.filter(item => this.matchesLocationFilters(item));
    },
    filteredJobs() {
      return this.jobs.filter(item => this.matchesLocationFilters(item));
    },
  },
  filters: {
    fdate(value) {
      if (!value) return '';
      const [year, month, day] = value.split('-');
      return `${day}-${month}-${year}`;
    }
  },
  created() {
    // (1) Retrieve Hot Deals / Sales / Jobs (type-specific data)
    this.getReports();
    // (2) Retrieve "All" data (for main unified table) plus needed location-lists
    this.getDealReports();
    // (3) Optional: get external lists
    this.getCountries();
    this.getStatesbycountry();
  },
  mounted() {
    // Initialize the main unified table to show everything
    this.filteredPros = this.allpros;
  },
  methods: {
    formatDate(date) {
    if (!date) return '';
    const d = new Date(date);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    return `${day}-${month}-${year}`;
  },
  formatDates(date) {
  if (!date) return '--';
  const d = new Date(date);
  if (isNaN(d)) return '--';
  return `${String(d.getDate()).padStart(2, '0')}-${String(d.getMonth() + 1).padStart(2, '0')}-${d.getFullYear()}`;
},
    resetFilters() {
  this.selCountry = null;
  this.selState = null;
  this.selCity = null;
  this.selSubcat = null;
  this.selectedDealType = 'All';
   this.stateSearch   = ''
    this.citySearch    = ''
    this.subcatSearch  = ''
  this.fromDate = null;
  this.toDate = null;
  this.filteredPros = this.allpros;
},
    // Helper: checks if an item matches the selected location filters
    matchesLocationFilters(item) {
      return (
        (!this.selCountry || item.country === this.selCountry) &&
        (!this.selState || item.state === this.selState) &&
        (!this.selCity || item.city === this.selCity) &&
        (!this.selSubcat || item.subcat_name === this.selSubcat)
      );
    },
    onDealTypeChange() {
  this.filterAllData();

  setTimeout(() => {
    const el = this.$refs.dealTypeSelect?.$el?.querySelector('input');
    if (el) el.blur();
  }, 100);
},
    // Called when Country changes
    onCountryChange() {
      if (this.selCountry) {
        // Rebuild the states list from the unified dataset
        this.filteredStates = [
          ...new Set(
            this.allpros
              .filter(i => i.country === this.selCountry)
              .map(i => i.state)
          )
        ].map(state => ({ state }));
        
        // Optionally rebuild subcats for that country
        this.subcats = [
          ...new Set(
            this.allpros
              .filter(i => i.country === this.selCountry)
              .map(i => i.subcat_name)
          )
        ].map(subcat_name => ({ subcat_name }));
      } else {
        this.filteredStates = [];
      }
      // Reset
      this.selState = null;
      this.selCity = null;
      this.filteredCities = [];
      this.filterAllData();

      // 👇 Blur the select box to remove focus
    this.$refs.countrySelect && this.$refs.countrySelect.blur();
    },

    // Called when State changes
    onStateChange() {
      if (this.selState) {
        this.filteredCities = [
          ...new Set(
            this.allpros
              .filter(i => i.state === this.selState)
              .map(i => i.city)
          )
        ].map(city => ({ city }));

        this.subcats = [
          ...new Set(
            this.allpros
              .filter(i => i.state === this.selState)
              .map(i => i.subcat_name)
          )
        ].map(subcat_name => ({ subcat_name }));
      } else {
        this.filteredCities = [];
      }
      this.selCity = null;
      this.filterAllData();

      setTimeout(() => {
      this.$refs.stateSelect?.blur();
    }, 100);
    },

    // Called when City changes
    onCityChange() {
      if (this.selCity) {
        this.subcats = [
          ...new Set(
            this.allpros
              .filter(i => i.city === this.selCity)
              .map(i => i.subcat_name)
          )
        ].map(subcat_name => ({ subcat_name }));
      }
      this.selSubcat = null;
      this.filterAllData();
      setTimeout(() => {
      this.$refs.citySelect?.blur();
    }, 100);
    },

    // Called manually or after location changes, applies location filters to the "All" table
    filterAllData() {
      this.filteredPros = this.allpros.filter(i => this.matchesLocationFilters(i));
      setTimeout(() => {
      this.$refs.subcatSelect?.blur();
    }, 100);
    },
    filterByCustomDate() {
  if (!this.fromDate || !this.toDate) return;

  const start = new Date(this.fromDate);
  const end = new Date(this.toDate);

  this.filteredPros = this.allpros.filter(item => {
    if (!item.created_at) return false;
    const createdDate = new Date(item.created_at);
    return createdDate >= start && createdDate <= end;
  });

  // Also refresh type-specific reports if needed
  this.getReports();  // If your Hot Deals/Sales/Jobs API needs it
},
    // (I) API calls
    getReports() {
  axios.get('/api/reports', {
    params: {
      from_date: this.fromDate,
      to_date: this.toDate
    }
  })
  .then(resp => {
    this.deals = resp.data.deals;
    this.sales = resp.data.sales;
    this.jobs = resp.data.jobs;
  })
  .catch(err => console.error(err));
},

    getDealReports() {
  axios.get('/api/deal/reports', {
    params: {
      type: this.dtype,
      from_date: this.fromDate,
      to_date: this.toDate
    }
  })
  .then(resp => {
    this.allpros = resp.data.allps || [];
    this.filteredPros = this.allpros;

    // build dropdowns
    this.countries = [...new Set(this.allpros.map(i => i.country))].map(country => ({ country }));
    this.states = [...new Set(this.allpros.map(i => i.state))].map(state => ({ state }));
    this.cities = [...new Set(this.allpros.map(i => i.city))].map(city => ({ city }));
    this.subcats = [...new Set(this.allpros.map(i => i.subcat_name))].map(subcat_name => ({ subcat_name }));
  })
  .catch(err => console.error(err));
},
    getCountries() {
      axios
        .get('/api/countries')
        .then(resp => {
          this.conts = resp.data.countries;
        })
        .catch(err => console.error(err));
    },

    getStatesbycountry() {
      axios
        .get('/api/get/states/1') // example for country id=1
        .then(resp => {
          this.astates = resp.data;
        })
        .catch(err => console.error(err));
    }
  }
};
</script>

<style scoped>
/* Add component-specific styles as needed */
</style>
