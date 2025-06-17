<template>
  <div class="pa-2">
    <h4 class="ms-4">Reviews</h4>
    <div v-if="reviews.length > 0">
      <div class="row">
        <div class="col-12 col-md-6" v-for="(review,i) in reviews" :key="i">
          <v-card>
            <v-card-text>
              <v-row>
                <v-col cols="3" md="2" class="py-0">
                  <v-avatar size="80" v-if="review.user_avatar != null">
                    <img :src="url+review.user_avatar" alt="avatar">
                  </v-avatar>
                  <v-avatar size="80" color="error" v-else>
                    <span class="white--text fw-bold" style="font-size: 15px">No Logo</span>
                  </v-avatar>
                </v-col>
                <v-col cols="6" md="8" class="mobile">
                  <span class="h6 link-dark ms-1">
                    {{ review.name }}
                    <v-rating :value="review.rating" color="amber" dense readonly half-increments size="20"></v-rating>
                  </span>
                  <p>{{ review.added_date }} </p>
                </v-col>
                <v-col cols="6" md="8" class="desktop">
                  <span class="h5 link-dark ms-1">
                    {{ review.name }}
                    <v-rating :value="review.rating" color="amber" dense readonly half-increments size="20"></v-rating>
                  </span>
                  <p>{{ review.added_date | fdate }} </p>
                </v-col>
                <!-- <v-col cols="3" md="2"><v-btn @click="reviewEditModal(review)" class="cred btn-small">Edit</v-btn></v-col> -->
              </v-row>
              <v-row class="mt-3">
                <v-col cols="12" md="12"><p class="link-dark mb-0">{{ review.review_text }}</p></v-col>
              </v-row>
              <!-- <v-row  v-if="review.all_review_images.length > 0">
                <v-col v-for="(rvImages,i) in review.all_review_images" :key="i" cols="3" md="3">
                  <img cover width="100%" :src="(url+rvImages.review_image_url)">
                </v-col>
              </v-row> -->
            </v-card-text>
          </v-card>
        </div>
      </div>
    </div>
    <div v-else class="text-center">
      <h6>Review Not Found</h6>
    </div>
    <v-dialog v-model="showModel1" persistent max-width="900">
      <v-card>
        <v-form v-on:submit.prevent="reviewEditFormSubmit" ref="ReviewForm" id="ReviewForm" v-model="valid" lazy-validation>
            <v-card-title class="text-h5 grey lighten-2" style="justify-content:space-between;">Edit review
                <v-icon  @click="showModel1 = false" class="text-h5">mdi-close</v-icon>
            </v-card-title>
            <v-card-text class="py-0">
                <v-rating v-model="editItems.rating" color="yellow darken-3" background-color="grey darken-1" empty-icon="$ratingFull" half-increments hover large ></v-rating>
                <span v-if="ratingRules==true" class="reviewCss">Please provide a rating.</span>
                <v-textarea class="py-0"
                    v-model="editItems.review_text"
                    :rules="ReviewRules"
                    :counter="400"
                    rows="3" 
                    row-height="20" 
                    label="Please Write Your Review Here">
                </v-textarea>
                <v-row class="py-0">
                  <v-col cols="12" md="6" class="py-0">
                    <span v-if="alloutUploadImage-imgAddedCount>0" style="color:red">Upload Max {{ alloutUploadImage-imgAddedCount }} images</span>
                    <span v-else style="color:red">You have already Uploaded {{ imgAddedCount }} images</span>
                    <v-file-input
                        v-model="selectedFiles"
                        multiple
                        show-size
                        @change="handleFileChange"
                        v-if="alloutUploadImage-imgAddedCount>0">
                    </v-file-input>
                    <div v-for="(file, index) in selectedFilesWithPreview" :key="index" class="image-preview">
                      <img :src="file.preview" alt="Preview" class="preview-image" width="100">
                      <v-btn icon @click="deleteFile(index)">
                        <v-icon>mdi-close</v-icon>
                      </v-btn>
                    </div>
                  </v-col>
                  <v-col cols="12" md="6" class="py-0">
                    <div v-if="editItems.all_review_images!=''">
                      <v-row>
                          <v-col v-for="(img, Dindex) in editItems.all_review_images" :key="Dindex" cols="12" md="4">
                              <v-img :src=(url+img.review_image_url) class="rounded-3" height="50" width="50"></v-img>
                              <v-btn icon @click="addedDeleteImage(img.id, Dindex)">
                                  <v-icon>mdi-close</v-icon>
                              </v-btn>
                          </v-col>
                      </v-row>
                    </div>

                  </v-col>
                </v-row>
            </v-card-text>
            <div class="text-end">
              <v-btn v-if="isLoading" class="my-4 me-3 cred">Loading...</v-btn>
              <v-btn v-else class="my-4 me-3 cred" type="submit" :disabled="!valid">UPDATE</v-btn>
              <v-btn @click="showModel1 = false" class="my-4 me-3 cred">CLOSE</v-btn>
            </div>
        </v-form>
      </v-card>
    </v-dialog>
    <v-snackbar v-model="snackbar" :timeout="timeout">{{ text }}
        <template v-slot:action="{}">
          <v-btn color="blue" text v-bind="attrs" @click="snackbar = false">Close</v-btn>
        </template>
    </v-snackbar>
  </div>
</template>
<script>

import axios from "axios";

export default {
  name: 'UserReviews',
  components: {},
  metaInfo: {title: 'Bizlx User Reviews'},
  data() {
    return {
      url:'https://bizlx.s3.ap-south-1.amazonaws.com',
      reviews:[],
      ratingRules: [],
      MessageModel1: false,
      isLoading: false,
      valid: false,
      showModel1: false,
      multipleFilesUploadByAPI: [],
      selectedFilesWithPreview: [],
      added_reviews_Image_delete_id: [],
      selectedFiles: [],
      editItems: [],
      rating: null,
      ReviewRules: [
        v => !!v || 'Review Description is required.',
        v => v.length <= 400 ||'Description will be 400 characters.',
      ],
      loggedUser: [],
      timeout: 3000,
      snackbar:false,
      text:'',
      alloutUploadImage: 3,
      imgAddedCount: 0,
    }
  },
  filters:{
    fdate(value) {
      if (!value) return '';
      const [year, month, day] = value.split('-');
      return `${day}-${month}-${year}`;
    },
    reverseFdate(value) {
      if (!value) return '';
      const [day, month, year] = value.split('-');
      return `${year}-${month}-${day}`;
    }
  },
  created(){
      this.loggedUser = this.$store.state.userData;
  },
  mounted(){
    this.getuserReviews();
  },
  methods:{
    getuserReviews(){
      axios.get("/api/user/review")
          .then((resp) =>{
                this.reviews = resp.data.user_reviews;
              }
          );
    },
    reviewEditModal(review) {
      
      this.added_reviews_Image_delete_id = [];
      this.editItems = review;
      this.imgAddedCount = this.editItems.all_review_images.length;
      this.isLoading = false,
      this.showModel1 = true;
      this.$ReviewForm.form.reset();
    },
    handleFileChange() {
      this.selectedFilesWithPreview = [];
      this.multipleFilesUploadByAPI = [];
      var count = this.alloutUploadImage-this.imgAddedCount;
      for (let i = 0; i < count; i++) {
        const file = this.selectedFiles[i];
        const reader = new FileReader();
        reader.onload = (event) => {
          this.selectedFilesWithPreview.push({
            file: file,
            preview: event.target.result
          });
        };
        reader.readAsDataURL(file);
        this.multipleFilesUploadByAPI.push(this.selectedFiles[i]); // Multiple file upload for api
      }
    },
    deleteFile(index) {
      this.selectedFiles.splice(index, 1);
      this.selectedFilesWithPreview.splice(index, 1);
    },
    addedDeleteImage(imageId, Dindex) { // uploaded image delete
      this.added_reviews_Image_delete_id.push(imageId);
      this.$delete(this.editItems.all_review_images, Dindex);
      this.imgAddedCount -=1;
    },
    reviewEditFormSubmit() { 
      const config = {
          headers: { 'content-type': 'multipart/form-data' }
      }
      var reviewData = {
        review_id: this.editItems.id,
        review_business_id: this.editItems.review_business_id,
        review_user_id: this.loggedUser.id,
        rating: this.editItems.rating,
        review_text: this.editItems.review_text,
        review_imgs: this.multipleFilesUploadByAPI,
        deleteReviewImages_id: this.added_reviews_Image_delete_id,
     
      };
      this.isLoading = true;
      axios.post("/api/edit/business/review", reviewData, config)
        .then((resp) => {
          if(resp.data.success == true){
            this.showModel1 = false;
            this.isLoading = false;
            this.snackbar = true;
            this.text = resp.data.message;
            this.getuserReviews();
            this.$refs.ReviewForm.reset();
          }
      });
      this.$ReviewForm.form.validate();
    }
  }
}
</script>

<style scoped>
.image-preview {
  display: inline-block;
  margin-right: 10px;
}
.preview-image {
  width: 60px;
  height: 60px;
  object-fit: cover;
}
</style>