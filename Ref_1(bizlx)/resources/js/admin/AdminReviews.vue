<template>
  <div>
    <div class="row">
      <div class="col-4">
        <h3 class="fw-bold">Business Review</h3>
      </div>
      <div class="col-2">
        <div class="my-2">
          <v-btn color="error" @click="exportData" dark large>Export Excel</v-btn>
        </div>
      </div>
      <div class="col-6">
        <v-text-field v-model="search" append-icon="mdi-magnify" clearable label="Search" single-line hide-details></v-text-field>
      </div>
      <v-row>
        <v-col cols="4" sm="4">
          <v-autocomplete :items="cities" ref="cityRef" clearable item-text="city" item-value="id" label="City" v-model="city" @change="handleBlur('cityRef');getSelectedData()" dense></v-autocomplete>
        </v-col>
        <v-col cols="4" sm="4">
          <v-autocomplete :items="businesses" ref="businessRef" clearable item-text="name" item-value="id" label="Business" v-model="business" @change="handleBlur('businessRef');getSelectedData()" dense></v-autocomplete>
        </v-col>
        <v-col cols="4" sm="4">
          <v-autocomplete :items="users" ref="userRef" clearable item-text="name" item-value="id" label="User" v-model="user" @change="handleBlur('userRef');getSelectedData()" dense></v-autocomplete>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="4" sm="4">
          <v-select :items="reviewRating" ref="ratingRef" clearable item-text="value" item-value="value" label="Star Rating" v-model="rating" @change="handleBlur('ratingRef');getSelectedData()"></v-select>
        </v-col>
        <v-col cols="4" sm="6" md="4">
          <v-menu v-model="menu1" :close-on-content-click="false" :nudge-right="40"
                  transition="scale-transition" offset-y min-width="auto">
            <template v-slot:activator="{ on, attrs }">
              <v-text-field clearable v-model="date1" label="Date From" prepend-icon="mdi-calendar"
                            v-bind="attrs" v-on="on"></v-text-field>
            </template>
            <v-date-picker clearable v-model="date1" @input="menu1 = false" @change="getSelectedData()"></v-date-picker>
          </v-menu>
        </v-col>
        <v-col cols="4" sm="6" md="4">
          <v-menu v-model="menu2" :close-on-content-click="false" :nudge-right="40"
                  transition="scale-transition" offset-y min-width="auto">
            <template v-slot:activator="{ on, attrs }">
              <v-text-field clearable v-model="date2" label="Date To" prepend-icon="mdi-calendar"
                            v-bind="attrs" v-on="on"></v-text-field>
            </template>
            <v-date-picker clearable v-model="date2" @input="menu2 = false" @change="getSelectedData()"></v-date-picker>
          </v-menu>
        </v-col>
      </v-row>
      <div class="">
        <v-data-table :search="search"  :headers="headers" ref="myDataTable" :items="reviews" sort-by="calories" show-select multi-select  class="elevation-1" v-model="checkedIds">
          <template v-slot:top>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-spacer></v-spacer>
            <v-dialog v-model="dialog" max-width="500px">
              <v-card>
                <v-card-title class="text-h5 grey lighten-2" style="justify-content:space-between;">{{ formTitle }}
                  <v-icon  @click="close" class="text-h5">mdi-close</v-icon>
              </v-card-title>
                <v-card-text>
                    <v-form ref="form" @submit.prevent="ReviewFormUpdate" v-model="valid" lazy-validation>
                        <v-textarea rows="2" auto-grow
                            v-model="editedItem.review_text"
                            :rules="review_textRules"
                            :counter="50"
                            dense>
                        </v-textarea>
                    </v-form>
                </v-card-text>
                  <v-card-actions>
                      <v-btn  color="error" @click="close">CANCEL</v-btn>
                      <v-btn type="submit" color="success" :disabled="!valid">SAVE</v-btn>
                  </v-card-actions>
              </v-card>
            </v-dialog>
            <v-dialog v-model="dialogDelete" max-width="500px">
              <v-card>
                <v-card-title class="text-h6">Are you sure you want to delete this item?</v-card-title>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
                  <v-btn color="blue darken-1" text @click="deleteItemConfirm">OK</v-btn>
                  <v-spacer></v-spacer>
                </v-card-actions>
              </v-card>
            </v-dialog>

            <v-dialog v-model="reviewMultidelete" max-width="500px">
              <v-card>
                <v-card-title class="text-h6">Are you sure you want to delete these reviews?</v-card-title>
                <v-card-actions>
                  <v-btn color="error" @click="closeDelete1">Cancel</v-btn>
                  <v-btn color="error" @click="deleteMultipleReviews">OK</v-btn>
                  <v-spacer></v-spacer>
                </v-card-actions>
              </v-card>
            </v-dialog>

          </template>
          <template v-slot:[`item.action`]="{ item }">
            <v-icon small class="mr-2" @click="editItem(item)">mdi-pencil</v-icon>
            <v-icon small @click="deleteItem(item)"> mdi-delete</v-icon>
          </template>
          <template v-slot:[`item.active`]="{ item }">
            <v-icon small class="mr-2" @click="editItem(item)">mdi-check</v-icon>
          </template>
          <template v-slot:footer>
          <div class="text-muted p-3">
            <v-btn color="grey"  @click="getSelectedIds">DELETE SELECTED</v-btn>
          </div>
        </template>
        </v-data-table>
      </div>
      <v-snackbar v-model="snackbar" :timeout="timeout">{{ text }}
        <template v-slot:action="{}">
            <v-btn color="blue" text v-bind="attrs" @click="snackbar = false">Close</v-btn>
        </template>
      </v-snackbar>
    </div>

  </div>
</template>

<script>

import axios from "axios";
export default {
    name: "ReviewsView",
    components: {  },
    data: () => ({
        search: '',
        error: '',
        // cities: '',
        items: '',
        menu1: false,
        menu2: false,
        modal: false,
        dialog: false,
        selectedIds: [],
        checkedIds: [],
        dialogDelete: false,
        reviewMultidelete: false,
        users:[],
        businesses:[],
        cities:[],
        reviewRating:[
          {'value':1},
          {'value':2},
          {'value':3},
          {'value':4},
          {'value':5},
        ],
        headers: [
            {text: "Business Name", align: "start", sortable: false, value: "bname",},
            { text: "Rating", value: "rating" },
            { text: "Mobile", value: "bphone" },
            { text: "City", value: "city" },
            { text: "Review", value: "review_text" },
            { text: "Created At", value: "formatted_created_at" },
            { text: "Review By User", value: "reviewby_name", sortable: false },
            { text: "Actions", value: "action", sortable: false },
        ],
        reviews: [],
        editedIndex: -1,
        editedItem: {
            review: "",
        },
        defaultItem: {
            review: "",
        },

        valid: false,
        review_textRules: [
          v => !!v || 'Review text is required.',
          v => v.length <= 50 || 'Review text must be less than 50 characters.',
        ],
        snackbar: false,
        text: '',
        timeout: '',
    }),

    computed: {
        formTitle() {
            return this.editedIndex === -1 ? "" : "Edit Business Review";
        },
    },

    watch: {
        dialog(val) {
            val || this.close();
        },
        dialogDelete(val) {
            val || this.closeDelete();
        },
        reviewMultidelete(val) {
            val || this.closeDelete1();
        },
    },

    created() {
        this.getAllreviews();
        this.getSearchData();
    },

    methods: {
      getSelectedIds() {
        this.reviewMultidelete = true;
      },
      deleteMultipleReviews() {
        var data = {
          ids:this.checkedIds.map(item => item.id)
        };

        axios.post('/api/admin/delete/multiple/reviews',data)
            .then((resp) => {
              if( resp.data.success == true){
                this.reviewMultidelete = false;
                this.getAllreviews();
                this.snackbar = true;
                this.text = resp.data.message;
                this.timeout = 3000;
              }else{
                this.reviewMultidelete = false;
                this.getAllreviews();
                this.snackbar = true;
                this.text = "Something went wrong";
                this.timeout = 3000;
              }
            });

      },
      closeDelete1() {
            this.reviewMultidelete = false;
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem);
                this.editedIndex = -1;
            });
        },
        exportData() {
        // Retrieve the data from your v-data-table
        const data = this.$refs.myDataTable.items;

        // Create an empty array to hold the exported records
        const exportedData = [];

        // Iterate over the data and extract the necessary information
        data.forEach((item) => {
          const exportedItem = {
            // Customize the properties based on your table structure
            id: item.id,
            BusinessName: item.bname,
            Rating: item.name,
            Mobile: item.bphone,
            City: item.city,
            Review: item.review_text,
            Created: item.created_at,
            ReviewByUser: item.reviewby_name,
            // Add more properties as needed
          };

          exportedData.push(exportedItem);
        });

        // Convert the exported data to a CSV or any other desired format
        const csvContent = this.convertToCSV(exportedData);

        // Create a temporary anchor element to trigger the download
        const link = document.createElement('a');
        link.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvContent));
        link.setAttribute('download', 'exported_data.csv');
        link.style.display = 'none';

        // Append the anchor element to the document and click it programmatically
        document.body.appendChild(link);
        link.click();

        // Clean up the anchor element
        document.body.removeChild(link);
      },

    convertToCSV(data) {
      // Convert the data array to a CSV string
      const header = Object.keys(data[0]).join(',') + '\n';
      const rows = data.map((item) => Object.values(item).join(',')).join('\n');
      return header + rows;
    },
      getAllreviews(){
          axios.get('/api/admin/reviews').then((resp)=>{
            this.reviews = resp.data.reviews;
          });
      },

        editItem(item) {
            this.editedIndex = this.reviews.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialog = true;
        },
        handleBlur(refName) {
        this.$nextTick(() => {
          const inputEl = this.$refs[refName]?.$el?.querySelector('input');
          if (inputEl) inputEl.blur();
        });
      },
        deleteItem(item) {
            this.editedIndex = this.reviews.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialogDelete = true;
        },

        deleteItemConfirm() {
          let data = {
            id:this.editedItem.id
          };
            axios.post('/api/admin/review/delete',data)
            .then((resp) => {
              if( resp.data.success == true){
                this.snackbar = true;
                this.text = resp.data.message;
                this.timeout = 3000;
              }else{
                this.snackbar = true;
                this.text = "Something went wrong";
                this.timeout = 3000;
              }
            });

            this.reviews.splice(this.editedIndex, 1);
            this.closeDelete();
        },

        close() {
            this.dialog = false;
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem);
                this.editedIndex = -1;
            });
        },

        closeDelete() {
            this.dialogDelete = false;
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem);
                this.editedIndex = -1;
            });
        },

        ReviewFormUpdate() {
            const config = {
                headers: { 'content-type': 'multipart/form-data' }
            }
            let data = {
              review_text: this.editedItem.review_text,
              review_id: this.editedItem.id,
            }
            if (this.editedIndex > -1) {

              axios.post("/api/admin/reviews/update", data, config)
                  .then((resp) => {

                      this.snackbar = true;
                      this.text = resp.data.message;
                      this.timeout = 3000;

                      this.getAllreviews();
                      this.close();
                  }).catch(error=>{
                      this.error=error;
                  });

                this.$refs.form.validate();
                Object.assign(this.reviews[this.editedIndex], this.editedItem);
            } else {
                this.reviews.push(this.editedItem);
                this.close();
            }
        },

        getSearchData(){
          axios.get('/api/get/information')
              .then((resp)=>{
                this.cities = resp.data.cities;
                this.businesses = resp.data.businesses;
                this.users = resp.data.users;
              });
        },

        getSelectedData(){
          var params = {
            city : this.city,
            business : this.business,
            user : this.user,
            rating : this.rating,
            date1 : this.date1,
            date2 : this.date2,
          };

          axios.post('/api/search/reviews', params )
              .then((resp)=>{
                console.log(resp);
                this.reviews = resp.data.reviews;
              });
        },
    },
};
</script>
