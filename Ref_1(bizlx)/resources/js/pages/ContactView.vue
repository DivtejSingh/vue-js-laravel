<template>
  <div>
    <!-- Success Message -->

    <!-- Mobile Screen Inquiry Button -->

    <!-- Web Form -->
    <div class="d-none d-md-flex row">
      <div class="row">
        <div class="col-12 col-md-6">
          <v-alert
  v-if="MessageModel1"
  :type="alertType"
  border="left"
  colored-border
  elevation="2"
  class="my-3"
  dismissible
>
  {{ message }}
</v-alert>

          <v-card>
            <v-card-text>
              <v-form ref="form" v-model="valid">
                <v-text-field clearable label="Name" v-model="name" :rules="userRules" required></v-text-field>
                <v-text-field clearable label="Email" v-model="email" :rules="emailRules" required></v-text-field>
                <v-text-field  clearable label="Contact" v-model="contact" :rules="contactRules" required></v-text-field>
                <v-text-field clearable label="City" v-model="city" :rules="cityRule" required></v-text-field>
                <v-textarea  clearable label="Description" v-model="description" :rules="descriptionRules" required></v-textarea>

                <v-row align="center" class="my-2">
                  <v-col cols="3" md="2">
                    <input type="number" class="form-control" readonly required :value="r1">
                  </v-col>
                  <v-col cols="1" md="1">
                    <v-icon size="20">mdi-plus</v-icon>
                  </v-col>
                  <v-col cols="3" md="2">
                    <input type="number" class="form-control" readonly required :value="r2">
                  </v-col>
                  <v-col cols="1" md="1">
                    <v-icon size="20">mdi-equal</v-icon>
                  </v-col>
                  <v-col cols="4" md="2">
                    <input type="number" class="form-control" v-model="total" required>
                  </v-col>
                  <v-col cols="12" md="4" class="py-0">
                    <p class="link-dark">Are you human, or spambot?</p>
                  </v-col>
                </v-row>

                <v-btn
  :loading="isSubmitting"
  :disabled="isSubmitting"
  color="red"
  dark
  @click="getAllData"
>
  Submit
</v-btn>

              <!-- <v-alert
                v-if="MessageModel1"
                :type="alertType"
                border="left"
                colored-border
                elevation="2"
                class="my-3"
                dismissible
              >
                {{ message }}
              </v-alert> -->

              </v-form>
            </v-card-text>
          </v-card>
        </div>

        <!-- Service Agent Section -->
        <div class="col-12 col-md-3">
          <h5 class="mb-3">Find Service Agent</h5>

          <v-autocomplete
            dense
            label="Select City"
            v-model="selectCity"
            prepend-inner-icon="mdi-city"
            :rules="cityRules"
            :items="allCities"
            item-value="city_id"
            no-data-text="Select Location"
            @keyup.native="getAllCity"
            item-text="city"
            @change="handleCityChange">
          </v-autocomplete>
          
          <v-btn v-if="selectCity" @click="handleGoClick" class="mt-2" color="primary">Go</v-btn>

          <div class="resellers_cls" v-if="resellers.length > 0">
            <div v-for="reseller in resellers" :key="reseller.id">
              <span><b>{{ reseller.name }}</b></span><br>
              <span><a :href="'mailto:' + reseller.email">{{ reseller.email }}</a></span><br>
              <span><a :href="'tel:' + reseller.mobile_phone">{{ reseller.mobile_phone }}</a></span><br>
              <span><p>{{ reseller.address }}</p></span>
            </div>
          </div>
          <div v-else-if="error">
            <p>{{ errorMessage }}</p>
          </div>
        </div>

        <!-- Profile Contact Section -->
        <div class="col-12 col-md-3">
          <p>{{ profile.address }}</p>
          <p>
            <strong class="me-1">Contact:</strong>
            <span class="cred">{{ profile.contact_number }}</span>
          </p>
        </div>
      </div>
    </div>
    <v-alert
  v-if="MessageModel1"
  :type="alertType"
  border="left"
  colored-border
  elevation="2"
  class="my-3"
  dismissible
>
  {{ message }}
</v-alert>

<!-- Mobile Modal Popup --> 

    <v-dialog v-model="dialog" persistent max-width="600px">
    

      <v-card class="modal-card contact-model">
        <!-- Close Icon in Top Right -->
        <v-btn
          icon
          class="close-icon"
          @click="closeDialog"
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-card-title class="text-h5">Contact Us</v-card-title>
        <v-card-text>
          <v-form ref="mobileForm" v-model="valid">
            <v-text-field label="Name" clearable v-model="name" :rules="userRules" required></v-text-field>
            <v-text-field label="Email" clearable v-model="email" :rules="emailRules" required></v-text-field>
            <v-text-field label="Contact" clearable v-model="contact" :rules="contactRules" required></v-text-field>
            <v-text-field label="City"  clearable v-model="city" :rules="cityRule" required></v-text-field>
            <v-textarea label="Description" clearable rows="2" v-model="description" :rules="descriptionRules" required></v-textarea>

            <v-row align="center" class="my-2">
              <v-col cols="3" md="2">
                <input type="number" class="form-control" readonly required :value="r1">
              </v-col>
              <v-col cols="1" md="1">
                <v-icon class="" size="20">mdi-plus</v-icon>
              </v-col>
              <v-col cols="3" md="2">
                <input type="number" class="form-control" :value="r2" solo readonly required>
              </v-col>
              <v-col cols="1" md="1">
                <v-icon class="" size="20">mdi-equal</v-icon>
              </v-col>
              <v-col cols="4" md="2">
                <input type="number" class="form-control" v-model="total" solo required>
              </v-col>
              <v-col cols="12" md="4" class="py-0">
                <p class="link-dark">Are you human, or spambot?</p>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>

        <!-- Align Submit and Cancel buttons -->
        <v-card-actions class="d-flex justify-end">
      <v-btn color="red" dark @click="closeDialog">Cancel</v-btn>
      <v-btn
  :loading="isSubmitting"
  :disabled="isSubmitting"
  color="red"
  dark
  @click="getAllData"
>
  Submit
</v-btn>
      <!-- <v-alert
  v-if="MessageModel1"
  :type="alertType"
  border="left"
  colored-border
  elevation="2"
  class="my-3"
  dismissible
>
  {{ message }}
</v-alert> -->

    </v-card-actions>

      </v-card>
    </v-dialog>
   
    <!-- Mobile Find Service Agent -->
    <div class="d-md-none">
      <!-- Profile Contact Section -->
      <div class="col-12 col-md-3">
        <p class="mb-0">{{ profile.address }}</p>
        <p class="buttons-inquiry">
          <strong class="me-1">Contact:</strong>
          <span class="cred">{{ profile.contact_number }}</span><br><br>
        <v-btn class="d-md-none inquiry-btn" @click="openDialog" color="primary large">Inquiry</v-btn>

        </p>
      </div>
      <div class="col-12">
      <div class="button-find">
        <h5 class="mb-3">Find Service Agent</h5>

      </div>
      
  
      <v-autocomplete
        dense
        label="Select City"
        v-model="selectCity"
        prepend-inner-icon="mdi-city"
        :rules="cityRules"
        :items="allCities"
        item-value="city_id"
        no-data-text="Select Location"
        @keyup.native="getAllCity"
        item-text="city"
        @change="handleCityChange">
      </v-autocomplete>
      <v-btn v-if="selectCity" @click="handleGoClick" class="mt-2" color="primary">Go</v-btn>
    </div>
      <div class="resellers_cls" v-if="resellers.length > 0">   
        <div v-for="reseller in resellers" :key="reseller.id">
          <span><b>{{ reseller.name }}</b></span><br>
          <span><a :href="'mailto:' + reseller.email">{{ reseller.email }}</a></span><br>
          <span><a :href="'tel:' + reseller.mobile_phone">{{ reseller.mobile_phone }}</a></span><br>
          <span><p>{{ reseller.address }}</p></span>
        </div>
      </div>
      <div v-else-if="error">
        <p>{{ errorMessage }}</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ContactView",
  props:{
      profile:Object
  },
  data() {
      return {
        
        isSubmitting: false,
          MessageModel1: false,
          message: '',
          alertType: 'success', 
          dialog: false, // Modal state
          error: false, 
          errorMessage: '',
          selectCity: null, // Initialize selected city variable
          MessageModel1: false,
          message:'',
          name: '',
          email: '',
          contact: '',
          city: '',
          description: '',
          valid: false,
          r1: '',
          r2: '',
          r3: '',
          total: '',
          allCities: [],
          resellers: [],
          selectedOption: 'General Enquiry',
          Formsubject: [
              { label: "General ", value: "General Enquiry" },
              { label: "Business Enquiry", value: "Business Enquiry" },
              { label: "Service Agent Enquiry", value: "Service Agent Enquiry" },
          ],
          userRules: [
              v => !!v || 'Name is required.',
              v => v.length <= 50 || 'Name must be less than 50 characters.',
          ],
          optionRules: [
              v => !!v || 'Select one input is required.',
          ],
          emailRules: [
              v => !!v || 'E-mail is required.',
              v => /.+@.+/.test(v) || 'E-mail must be valid.',
              v => v.length <= 55 || 'Email must be less than 55 characters.',
          ],
      contactRules: [
          v => !!v || 'Contact no is required.',
          v => /^[0-9]*$/.test(v) || 'Only numeric characters allowed.',
         
          v => v.length >= 10 || 'Contact must be 10 digits.',
        ],

          cityRules: [
              v => !!v || 'City is required.',
          ],
          cityRule: [
              v => !!v || 'City is required.',
              v => v.length <= 20 || 'City must be less than 20 characters.',
          ],
          descriptionRules: [
              v => !!v || 'Description is required.',
              v => v.length <= 200 || 'Description must be less than 200 characters.',
          ],
      }
  },
  mounted() {
      // this.getadresss();
      this.myFunction();
      this.mytotal();
      //this.getAllCity();
  },
  methods:{
      openDialog() {
    this.dialog = true;
  },
  closeDialog() {
    this.dialog = false;
  },
  getAllData() {
  const formRef = this.dialog ? this.$refs.mobileForm : this.$refs.form;
  const isValid = formRef.validate();
  const isCaptchaValid = parseInt(this.total) === this.r1 + this.r2;

  if (!isValid || !isCaptchaValid) {
    this.alertType = 'error';
    this.message = !isCaptchaValid
      ? 'Captcha is incorrect. Please verify you are human.'
      : 'Please fill all required fields correctly.';
    this.MessageModel1 = true;
    return;
  }

  this.isSubmitting = true;

  const data = {
    name: this.name,
    email: this.email,
    address: this.contact,
    city: this.city,
    description: this.description,
    enquiry: 'General Enquiry',
  };

  axios.post("/api/contact", data)
    .then((resp) => {
      this.isSubmitting = false;
      if (resp.data.success === true) {
        // Reset form based on active one
        formRef.reset();
        this.total = '';
        this.message = resp.data.message || "Form submitted successfully!";
        this.alertType = 'success';
        this.MessageModel1 = true;
        this.dialog = false;

        this.myFunction();

        setTimeout(() => {
          this.MessageModel1 = false;
        }, 9000);
      }
    })
    .catch(() => {
      this.isSubmitting = false;
      this.alertType = 'error';
      this.message = "Something went wrong while submitting the form.";
      this.MessageModel1 = true;
    });
},
 
      // getAllCity(){
      //     axios.get('/api/allcities')
      //         .then((resp) => {
      //                 this.allCities = resp.data.locations;
      //             }
      //         );
      // },
      getAllCity(event){
          var value = event.target.value.trim(); // Trim to remove leading and trailing spaces
          if (value.length > 0) {
          axios.get(`/api/keyword/allcities?keyword=${value}`)
              .then((resp) => {
                      this.allCities = resp.data.locations;
                  }
              );
          }else{
              this.allCities=[];
          }
      },
      // handleCityChange(){
      //     axios.get('/api/resellers/list?city_id='+this.selectCity)
      //         .then((resp) => {
      //                 this.resellers = resp.data.resellers;
      //                 console.log("njjdjke++------", this.resellers)
      //             }
      //         );
      // },
      handleCityChange() { 
          this.selectCity;
          this.resellers = []; this.error = false; this.errorMessage = '';    
      },

      handleGoClick() { 
          if (this.selectCity) { 
              // Make the API call
              axios.get('/api/resellers/list?city_id=' + this.selectCity)
                  .then((resp) => { 
                      this.resellers = resp.data.resellers; 
                      if (this.resellers.length === 0) 
                      { 
                          this.error = true; 
                          this.errorMessage = 'No Service Agents found.';
                          
                       } else 
                       {this.error = false; console.log('Resellers:', this.resellers);
                      } 
                  })
                  
                  .catch((error) => { 
                      this.error = true; this.errorMessage = 'Error fetching resellers.';
                      console.error('Error fetching resellers:', error); 
                  });
          } else {
              this.error = true; this.errorMessage = 'No city selected.';
              console.log('No city selected.'); // Additional check log
          }
      },
      close(){
          this.MessageModel1 = true;
          location.reload();
      },
      myFunction() {
  this.r1 = Math.floor(Math.random() * 10);
  this.r2 = Math.floor(Math.random() * 10);
  this.total = '';
},

      mytotal(){
          this.r3 = this.r1 + this.r2;
      },
      result(){
          if(this.total != this.r3 || this.total == null){
              alert('Please fill the correct captcha');
          }
      },
  }
}
</script>

<style lang="scss" scoped>
.resellers_cls {
  min-height: 240px;
  height: 320px;
  border-radius: 5px;
  overflow-y: scroll;
  border: 1px solid #850505;
  padding: 10px;
  }
  @media (max-width: 768px) {
      .v-dialog {
      position: fixed !important; // Ensure modal is fixed /
      z-index: 2000 !important;   // Ensure it is above the navbar /
      width: 90% !important;
      max-width: 90% !important;
      }
      .modal-card {
border-radius: 10px;
padding: 20px;
max-height: 80vh; // Reduced height for professional look /
overflow-y: auto;
}
// Mobile Inquiry Button //
.inquiry-btn[data-v-3ab35d28] {
left: 11%;
top: 2px;
bottom: 10px;
  transform: translateX(-50%);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
  
}
.col-3 {
  width: 38%; 

.col-4 {
  width: 38%; 
}

}
// Navbar should have a lower z-index /
.navbar {
position: fixed;
top: 0;
left: 0;
width: 100%;
z-index: 1000; // Set lower than modal /
}

.modal-card.contact-model.v-card.v-sheet.theme--light {
  position: absolute;
  top: 135px;
  left: 0;
  width: 95%;
  margin: 0 auto;
  right: 0;
}
.close-icon {
position: absolute;
top: 15px;
right: 14px;
z-index: 10; /* Ensures it stays above other elements */
}

.button-find {
  display: flex;
  justify-content: space-between;
}

.contact-model .v-input.theme--light.v-text-field.v-text-field--is-booted {
  padding-top: 0 !important;
  margin: 0 !important;
}
}



</style>