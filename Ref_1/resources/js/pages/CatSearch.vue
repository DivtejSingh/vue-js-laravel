  <template>
    <div class="w-100">
      <v-form @submit.prevent="ClickSearch" class="catsearch d-flex mobile bg-white">

        <!-- <v-autocomplete dense hide-details placeholder="Category"
            v-model="selectSubCategoty"
            :items="categories"
            item-value="id"
            item-text="subcat_name" :no-data-text="noCat"
            id="sel1" class="sel1 cat1 "
            @keyup.native="fetchCatm">
        </v-autocomplete> -->

        <v-autocomplete
        dense hide-details placeholder="Category"
        v-model="selectCategory"
        return-object 
        clearable
        :items="combinedCategories"
        item-value="id"
        item-text="display_name"
         :item-text="stripJobSuffix"
        :no-data-text="noCat"
        
        class="sel1 cat1 custom-autocomplete"
        prepend-inner-icon="mdi-format-list-bulleted-square"
 @change="onCategoryChanged"
        @keyup.native="fetchCategories"

      >
        <template v-slot:item="{ item }">
          <v-list-item-content>
            <v-list-item-title
              :class="item.is_job ? 'info--text' : ''"
            >
              {{ item.display_name }}
            </v-list-item-title>
          </v-list-item-content>
        </template>
 
      </v-autocomplete>

        <v-autocomplete dense hide-details placeholder="Location"
        clearable
            v-model="selectCity"
            :items="allCities"
            item-value="city_id"
            item-text="city" :no-data-text="noCity"
            class="sel1 loc1 custom-autocomplete"
            @keyup.native="fetchLocm">
            
        </v-autocomplete>

        <v-autocomplete dense hide-details placeholder="Deals"
        clearable
            v-model="deal"
            :items="filteredDeals"  class="sel1 deal1 custom-autocomplete"
            >
        </v-autocomplete>

        <v-btn type="submit" max-width="36px" min-width="36px" color="red"><v-icon class="text-white col-1">mdi-magnify</v-icon></v-btn>
      </v-form>

      <v-form @submit.prevent="ClickSearch" class="catsearch d-flex desktop">

          <!-- <v-autocomplete dense rounded hide-details placeholder="Category"
              v-model="selectSubCategoty"
              :items="categories"
              item-value="id"
              item-text="subcat_name" :no-data-text="noCat"
              id="sel2" class="sel2" prepend-inner-icon="mdi-format-list-bulleted-square"
              @keyup.native="fetchCat">
          </v-autocomplete> -->
          <v-autocomplete
          clearable
        dense hide-details placeholder="Category"
        v-model="selectCategory"
         return-object 
        :items="combinedCategories"
        item-value="id"
        item-text="display_name"
         :item-text="stripJobSuffix"
        :no-data-text="noCat"
        class="sel1 cat1 custom-autocomplete"
       
        prepend-inner-icon="mdi-format-list-bulleted-square"
 @change="onCategoryChanged"
        @keyup.native="fetchCategories"

      >
        <template v-slot:item="{ item }">
          <v-list-item-content>
            <v-list-item-title
              :class="item.is_job ? 'info--text' : ''"
            >
              {{ item.display_name }}
            </v-list-item-title>
          </v-list-item-content>
        </template>
 
      </v-autocomplete>

          <v-autocomplete dense rounded hide-details placeholder="Location"
          clearable
              v-model="selectCity"
              :items="allCities"
              item-value="city_id"
              item-text="city" :no-data-text="noCity"
            class="sel2 custom-autocomplete" prepend-inner-icon="mdi-map-marker"
             append-inner-icon="mdi-close"
              @keyup.native="fetchLoc">
          </v-autocomplete>

          <v-autocomplete dense rounded hide-details placeholder="Deals"
              v-model="deal" clearable  style="min-width: 0;"
              :items="filteredDeals"  class="sel2 custom-autocomplete" prepend-inner-icon="mdi-checkbox-marked-outline"
              >
          </v-autocomplete>

          <v-btn type="submit" color="red" dark><v-icon class="text-white">mdi-magnify</v-icon>Search</v-btn>
      </v-form>


      <v-snackbar v-model="snackbar" :timeout="snackbar.timeout" class="snackbar-shadow srch">{{ text }}
          <template v-slot:action="{ attrs }">
              <v-btn color="blue" text v-bind="attrs" @click="snackbar = false">OK</v-btn>
          </template>
      </v-snackbar>


    </div>
  </template>

  <script>

  import axios from 'axios';
  export default {
    name: "CatSearch",
    data: () => ({
        // selectSubCategoty: null,
        selectCategory: null,

        subname:'',
        catids:[],
        categories:       [],    // will hold subs
        jobCategories:    [],    // will hold jobs
        selectCity: null,
        searchcat:'',
        searchcatm:'',
        searchloc:'',
        searchlocm:'',
        isLoadingcat:false,
        isLoadingcatm:false,
        isLoadingloc:false,
        isLoadinglocm:false,
        noCat:'Select Category',
        noCity:'Select Location',
        deal: '',
        categories: [],
        categoriesm: [],
        locations:[],
        allCities: [],
        allCitiesm: [],
        deals:['Hot Deals', 'Sales','Jobs', 'Videos'],
        snackbar: '',
        text: '',
        timeout: 3000,
        loggedUser: []
    }),
    computed: {
    // Merge subs + jobs
    combinedCategories() {
      const subs = this.categories.map(c => ({
        id:            c.id,
        display_name:  c.subcat_name,
        is_job:        false
      }));
      const jobs = this.jobCategories.map(j => ({
        id:            j.id,
        display_name:  `${j.job_cat_name} - Job`,
        is_job:        true
      }));
      return [...subs, ...jobs];
    },
    // Only “Jobs” when it's a job cat
    filteredDeals() {
      return this.selectCategory?.is_job
        ? ['Jobs']
        : ['Hot Deals','Sales','Jobs','Videos'];
    }
  },

  watch: {
    selectCategory(cat) {
      if (cat?.is_job) {
        this.deal = 'Jobs'
      }
    }
  },


    methods: {
      stripJobSuffix(item) {
      return item.display_name.replace(/\s*[-–]\s*Job$/i, '');
    },
      onCategoryChanged(cat) {
    if (cat.is_job) {
      // job‐category path
      this.$store.dispatch('setjobcatid', { jobcat_id: cat.id });
      this.$store.dispatch('setdealtypeid', { dealtype: 2 });
      this.deal = 'Jobs';
    } else {
      // subcategory path
      this.$store.dispatch('setsubcatid', { subcat_id: cat.id });
      this.$store.dispatch('setdealtypeid', { dealtype: 1 });
    }
  },
      // fetchCat(event) {
      //   var value = event.target.value.trim(); // Trim to remove leading and trailing spaces
      //   if (value.length > 0) {
      //     axios.get(`/api/keyword/catsearch?keyword=${value}`)
      //         .then((resp) => {
      //           if (event.target.value.trim().length > 0) {
      //             // this.categories = resp.data.subcategories;
                  
      //             this.catids = resp.data.subcategories;
      //             console.log("this.categories");
      //             console.log(this.categories);
      //           }else {
      //             console.log('Input value is now empty, ignoring response data');
      //           }
      //       }
      //     ).catch((error) => {
      //             console.error('Error fetching subcategories:', error);
      //     });
      //   }else {
      //     // Handle case when value is an empty string
      //     console.log('Value is an empty string');
      //     this.categories = []; // Set subcats to empty array or handle it as needed
      //   }
      // },
      // fetchCatm(event) {
      //   var value = event.target.value.trim(); // Trim to remove leading and trailing spaces
      //   if (value.length > 2) {
      //     axios.get(`/api/keyword/catsearch?keyword=${value}`)
      //         .then((resp) => {
      //           if (event.target.value.trim().length > 0) {
      //             this.categories = resp.data.subcategories;
      //           }else {
      //             console.log('Input value is now empty, ignoring response data');
      //           }
      //       }
      //     ).catch((error) => {
      //             console.error('Error fetching subcategories:', error);
      //     });
      //   }else {
      //     // Handle case when value is an empty string
      //     console.log('Value is an empty string');
      //     this.categories = []; // Set subcats to empty array or handle it as needed
      //   }
      // },
      fetchCategories(e) {
        console.log('[CatSearch] fetchCategories() called with:', e.target.value);
      const kw = e.target.value.trim();
      if (kw.length < 0) {
        this.categories = this.jobCategories = [];
        return;
      }
      axios.get('/api/keyword/catsearch', { params:{ keyword: kw } })
        .then(r => {
          console.log('[CatSearch]  → fetchCategories response:', r.data);
          this.categories    = r.data.subcategories || [];
          this.jobCategories = r.data.jobCategories  || [];
        })
        .catch(() => {
          console.error('[CatSearch]  × fetchCategories error:', err);
          this.categories = this.jobCategories = [];
        });
    },
   
      fetchLoc(event) {
        console.log('[CatSearch] fetchLoc() called with:', event.target.value);
        var value = event.target.value.trim(); // Trim to remove leading and trailing spaces
        if (value.length > 0) {
          axios.get(`/api/keyword/allcities?keyword=${value}`)
              .then((resp) => {
                console.log('[CatSearch]  → fetchLoc response:', resp.data);
                if (event.target.value.trim().length > 0) {
                  this.allCities = resp.data.locations;
                }else {
                  console.log('Input value is now empty, ignoring response data');
                }
            }
          ).catch((error) => {
            
                  console.error('Error fetching subcategories:', error);
          });
        }else {
          // Handle case when value is an empty string
          console.log('Value is an empty string');
          console.log('[CatSearch] fetchLoc(): value < 3 chars, clearing allCities');
          this.allCities = []; // Set subcats to empty array or handle it as needed
        }
      },
      fetchLocm(event) {
        var value = event.target.value.trim(); // Trim to remove leading and trailing spaces
        if (value.length > 0) {
          axios.get(`/api/keyword/allcities?keyword=${value}`)
              .then((resp) => {
                if (event.target.value.trim().length > 0) {
                  this.allCities = resp.data.locations;
                }else {
                  console.log('Input value is now empty, ignoring response data');
                }
            }
          ).catch((error) => {
                  console.error('Error fetching subcategories:', error);
          });
        }else {
          // Handle case when value is an empty string
          console.log('Value is an empty string');
          this.allCities = []; // Set subcats to empty array or handle it as needed
        }
      },
      onInput() {
        // Handle the input event if needed
        console.log('Input event triggered');
      },
      ClickSearch() {
        console.log('[CatSearch] ClickSearch() start: ', {
      category: this.selectCategory?.id,
      city:     this.selectCity,
      deal:     this.deal
      
   });



    this.loggedUser = this.$store.state.userData;

    if (this.selectSubCategoty != null || this.selectCity != null || this.deal != null) {
      // figure out which category object we picked

      const matchedCategory = this.categories.find(c => c.id === this.selectSubCategoty);
      const matchedCity     = this.allCities.find(c => c.city_id === this.selectCity);
     

      // if they picked a **job** category, override everything
      if (matchedCategory?.is_job) {
    
  const dealtype  = 2;
  const jobcat_id = matchedCategory.id || null;
  const cityParam = city_id ? `&city_id=${city_id}` : '';
  // **pass jobcat_id into the query string**
  window.location.href =
    `/searchall?dealtype=${dealtype}` +
    `&jobcat_id=${jobcat_id}` +
    cityParam;
  return;
}

      // otherwise, your existing deals logic:
      const rawName = matchedCategory?.subcat_name
      || this.selectCategory?.display_name;
      const cleanName = rawName?.replace(/\s*[-–]\s*Job$/i, '');
      let data = {
        selectSubCategoty:this.selectCategory?.id,
        selectSubCatname: cleanName,
        selectCity:       this?.selectCity,
        selectCityname:   matchedCity?.city    || '',
        jobcat_name:      cleanName,
        deal:             this.deal,
        loggedUser_id:    this.loggedUser.id,
      };

      localStorage.removeItem('catSearchedValues');
      localStorage.removeItem('jobSearchedValues');
 
      if (data.deal === "Hot Deals") {
        const dealtype = 0;
        this.$store.dispatch('setdealtypeid', { dealtype });
        this.$store.dispatch('setcityid',      { city_id: data.selectCity });
        this.$store.dispatch('setsubcatid',    { subcat_id: data.selectSubCategoty });
        localStorage.setItem("catSearchedValues", JSON.stringify(data));
        window.location.href = `/searchall?dealtype=${dealtype}`;
      }
      else if (data.deal === "Sales") {
        const dealtype = 1;
        this.$store.dispatch('setdealtypeid', { dealtype });
        this.$store.dispatch('setcityid',      { city_id: data.selectCity });
        this.$store.dispatch('setsubcatid',    { subcat_id: data.selectSubCategoty });
        localStorage.setItem("catSearchedValues", JSON.stringify(data));
        window.location.href = `/searchall?dealtype=${dealtype}`;
      }
      else if (data.deal === "Jobs") {
        console.log(data);
       
        // **NON-category “Jobs”** path (if they manually picked “Jobs” from the Deals dropdown)
        const dealtype = 2;
        this.$store.dispatch('setdealtypeid', { dealtype });
        this.$store.dispatch('setcityid',      { city_id: data.selectCity });

        this.$store.dispatch('setsubcatid',    { subcat_id: '' });
        this.$store.dispatch('setjobcatid',    { jobcat_id: data?.selectSubCategoty || null });
        localStorage.setItem("catSearchedValues", JSON.stringify(data));
       
        // here it’s a non‐category “Jobs” pick, so leave subcat blank but can still pass city
    window.location.href =
      `/searchall?dealtype=${dealtype}` +
      (data.selectCity ? `&city_id=${data.selectCity}` : "");
      }
      else if (data.deal === "Videos") {
        const dealtype = 5;
        this.$store.dispatch('setdealtypeid', { dealtype });
        this.$store.dispatch('setcityid',      { city_id: data.selectCity });
        this.$store.dispatch('setsubcatid',    { subcat_id: '' });
        this.$store.dispatch('setjobcatid',    { jobcat_id: '' || null});
        localStorage.setItem("catSearchedValues", JSON.stringify(data));
        window.location.href = '/searchall';
      }
      else {
        // fallback Listings / “All”
        const dealtype = 3;
        this.$store.dispatch('setdealtypeid', { dealtype });
        this.$store.dispatch('setcityid',      { city_id: data.selectCity });
      
        this.$store.dispatch('setsubcatid',    { subcat_id: data.selectSubCategoty });
        localStorage.setItem("catSearchedValues", JSON.stringify(data));
        window.location.href = `/searchall?dealtype=${dealtype}`;
      }
    }
    else {
      this.snackbar = true;
      this.text     = "Please select deal for search.";
    }
  }

    }
    }

  </script>

  <style scoped>
  .catsearch.d-flex {
    border: 1px solid;
    border-radius: 4px;
    padding: 0 0 0 4px;
  }
  .sel2 {
    font-size: 14px;
    
  }
  .sel2 .v-input__control {
  padding-right: 40px !important; /* or 40px depending on your icon size */
}
 #catt1::placeholder{
  color: #000000 !important;
  opacity: 1;
 }
  #sel1::placeholder {
    color: #000000 !important;
    opacity: 1;
  }
  .sel1 .v-input__control {
  padding-right: 40px!important;
} 



  .v-btn {border-radius: 0 4px 4px 0;}
  @media(min-width: 576.1px){
    .mobile{display: none !important;}
  }
  @media(max-width: 576px){
    .desktop{display: none !important;}
    .sel1 {
      font-size: 13px;
    }
  }
 
  .catsearch.mobile .cat1 {
    width: 120px !important;
  }
  .catsearch.mobile .loc1 {
    width: 80px !important;
  }
  .catsearch.mobile .deal1 {
    width: 80px !important;
  }
  
  </style>

<style>
.custom-autocomplete .v-input__append-inner {
  width: 20px !important;         /* Fix width so input doesn’t expand */
  display: flex !important;
  justify-content: flex-end !important;
  align-items: center;
  padding-right: 4px;             /* Optional: spacing from edge */
}

.custom-autocomplete .v-input__icon--clear {
  width: 20px !important;         /* Same width as container */
  min-width: 15px !important;
  display: block !important;


}


@media (max-width: 600px) {
  .custom-autocomplete .v-input__append-inner {
    width: 20px !important;
    padding-right: 7px !important;
    gap: 1px;
  }

  .custom-autocomplete .v-input__icon--clear {
    width: 14px !important;
    min-width: 14px !important;
    margin-left: 0 !important; /* prevent overflow */
  }
}



 

</style>