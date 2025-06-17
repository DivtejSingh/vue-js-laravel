<template>
    <div class="pa-2">
      <h4>Post deals</h4>
      <v-tabs v-model="tab">
            <v-tab>Hot deals</v-tab>
            <v-tab>Sales</v-tab>
            <v-tab>Jobs</v-tab>
            <v-tab>Video</v-tab>
      </v-tabs>
      <v-tabs-items v-model="tab">
        <v-tab-item>
          <div class="row pb-3">
            <div class="col-12 col-sm-4">
              <v-text-field v-model="search1" append-icon="mdi-magnify" label="Search" single-line hide-details>
              </v-text-field>
            </div>
            <div class="col-12 col-sm-4 test-decoration-none">
              <div v-if="hotdeals_count === userDta.hot_deals">
                <v-btn @click="MessageModel1 = true" color="error"  tile large>
                  <v-icon left>mdi-plus</v-icon>CREATE HOT DEAL
                </v-btn>
              </div>
              <div v-else>
                <v-btn @click="openhotdeal" color="error"  tile large>
                  <v-icon left>mdi-plus</v-icon>CREATE HOT DEAL
                </v-btn>
              </div>
            </div>
          </div>
            <v-data-table :search="search1" :headers="hotDealsHeaders" :items="itemsWithSno" class="elevation-2">
              <template v-slot:top>
                <v-dialog v-model="hotDealModalEdit" max-width="900px">

                  <v-form @submit.prevent="dealAddFormSubmit" ref="form" v-model="valid" lazy-validation>
                    <v-card>
                      <v-card-title class="text-h5 grey lighten-2" style="justify-content:space-between;">{{ formTitle1 }}
                          <v-icon  @click="close" class="text-h5">mdi-close</v-icon>
                      </v-card-title>
                      <v-card-text>
                        <v-row>
                          <v-col cols="12" md="12" class="py-0" >
                              <v-text-field label="Deal title"
                                      type="text"
                                      v-model="editedItem.hot_deal_title"
                                      :rules="dealTitleRules"
                                      :counter="100">
                              </v-text-field>
                          </v-col>
                          <v-col cols="12" md="6" class="py-0 d-none">
                              <v-text-field label="Price start from"
                                      type="number"
                                      v-model="startPrice"
                                      :rules="priceFromRules">
                              </v-text-field>
                          </v-col>
                          <v-col cols="12" md="6" class="py-0">
                            <v-text-field label="Price upto"
                                      type="number"
                                      v-model="editedItem.price_to"
                                      :rules="[(parseFloat(editedItem.price_from) < parseFloat(editedItem.price_to)) || 'To price greater than From price.']">
                              </v-text-field>
                          </v-col>
                          <v-col cols="12" md="6" class="py-0 d-none">
                            <v-text-field
                                      type="date"
                                      label="Date"
                                      v-model="todayDate"
                                      :rules="dateFromRules"
                                      prepend-inner-icon="mdi-calendar" format="yyyy-mm-dd">
                            </v-text-field>
                          </v-col>
                          <v-col cols="12" md="6" class="py-0">
                            <v-menu
                              ref="menu2"
                              v-model="menu2"
                              :close-on-content-click="false"
                              transition="scale-transition"
                              offset-y
                              min-height="290px"
                              max-height="290px"
                              max-width="290px"
                              min-width="auto"
                            >
                          <template v-slot:activator="{ on, attrs }">
                              <v-text-field
                                v-model="formattedDateTo"
                                label="Date"
                                persistent-hint
                                prepend-icon="mdi-calendar"
                                v-bind="attrs"
                                @blur="editdate = parsedDate(editedItem.date_to)"
                                v-on="on"
                              ></v-text-field>
                            </template>
                            <v-date-picker
                              v-model="dateToInternal"
                              no-title
                              @input="menu2 = false"
                            ></v-date-picker>
                          </v-menu>
                          </v-col>
                          <v-col cols="12" md="6" class="py-0">
                            <span style="color:red">Your have uploaded ({{ editedItem.hotdeals_images_count }}) images</span>

                              <div v-if="editedItem.hotdeals_images!=''">
                                <v-row>
                                    <v-col v-for="(img, Dindex) in editedItem.hotdeals_images" :key="Dindex" cols="12" md="4">
                                        <v-img :src=(url+img.hotdeal_img_url) class="rounded-3" height="50" width="50"></v-img>
                                        <v-btn icon @click="addedDeleteImage(img.id, Dindex)">
                                            <v-icon>mdi-close</v-icon>
                                        </v-btn>
                                    </v-col>
                                </v-row>
                              </div>
                          </v-col>
                          <v-col cols="12" md="6" class="py-0">
                              <span style="color:red">You can upload maximum ({{ userDta.images }}) images</span>
                              <v-file-input
                                      v-model="editedItem.updateSelectedFiles"
                                      :rules="selectedFilesRules"
                                      label="Select images"
                                      multiple
                                      show-size
                                      @change="UpdateHandleFileChange">
                              </v-file-input>
                              <div v-for="(file, index) in updateSelectedFilesWithPreview" :key="index" class="image-preview">
                                <img :src="file.UpdatePreview" alt="UpdatePreview" class="preview-image">
                                <v-btn icon @click="updateDeleteFile(index)">
                                  <v-icon>mdi-close</v-icon>
                                </v-btn>
                              </div>
                          </v-col>
                          <v-col cols="12" md="12" class="py-0">
                              <v-textarea
                                      v-model="editedItem.hot_deal_description"
                                      :rules="descriptionRules"
                                      :counter="1000"
                                      rows="3"
                                      row-height="20"
                                      label="Description">
                              </v-textarea>
                          </v-col>
                          <v-col cols="12" md="12" class="py-0 d-flex" style="justify-content: space-between;">
                                <div>
                                  <v-checkbox v-model="editedItem.hot_deal_status" label="Active"></v-checkbox>
                                </div>
                                <div class="ms-3 pb-3">
                                    <v-btn v-if="isLoading" class="my-2 me-3 cred">Loading...</v-btn>
                                    <v-btn v-else class="my-2 me-3" color="success" type="submit" :disabled="!valid">UPDATE</v-btn>
                                    <v-btn @click="close" class="my-2 me-3" color="error">CLOSE</v-btn>
                                </div>
                          </v-col>
                        </v-row>
                      </v-card-text>
                    </v-card>
                  </v-form>
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
              </template>
              <template v-slot:[`item.description`]="{ item }">
                  <div>{{ item.hot_deal_description.slice(0, descMaxLength)+'...' }}</div>
              </template>
              <template v-slot:[`item.date_from`] = {item}>
                {{ item.date_from | fdate }}
              </template>
              <template v-slot:[`item.date_to`] = {item}>
                {{ item.date_to | fdate }}
              </template>
              <template v-slot:[`item.status`]="{ item }">
                  <div v-if="item.hot_deal_status === 1" style="color:green">
                    <div v-if="item.date_to < currentDate" class="cred">Expired</div>
                    <div v-else><span style="color:green">Active</span></div>
                  </div>
                  <div v-else>
                    <div v-if="item.date_to < currentDate" class="cred">Expired</div>
                    <div v-else><span style="color:gray">Inactive</span></div>
                  </div>
              </template>
              <template v-slot:[`item.action1`]="{ item }">
                <!-- <a :href="{name:'dealsdetail', params:{id:item.id}}" target="_blank" class="link-dark">
                  <v-icon small class="mr-2">mdi-eye </v-icon>
                </a> -->
                <v-icon small class="mr-2" @click="editItem(item)">mdi-pencil</v-icon>
                <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
              </template>
              <template v-slot:[`item.eye`]="{ item }">
                <a :href= "'/deals/detail/'+item.hotdeal_slug" target="_blank" class="link-dark">
                  <v-icon small class="mr-2">mdi-eye </v-icon>
                </a>
              </template>
            </v-data-table>
        </v-tab-item>
        <v-tab-item>
            <sales-view/>
        </v-tab-item>
        <v-tab-item>
            <jobs-view/>
        </v-tab-item>
        <v-tab-item>
            <videos-view/>
        </v-tab-item>
      </v-tabs-items>
      <v-dialog v-model="showModel1" persistent max-width="900">
        <v-card>
          <v-form @submit.prevent="dealAddFormSubmit" ref="form" v-model="valid" lazy-validation>
              <v-card-title class="text-h5 grey lighten-2 d-flex" style="justify-content:space-between;">{{ formTitle1 }}
                  <v-icon  @click="showModel1 = false" class="text-h5">mdi-close</v-icon>
              </v-card-title>
              <v-card-text>
                <v-row>
                  <v-col cols="12" md="12" class="py-0" >
                      <v-text-field label="Deal title"
                              type="text"
                              v-model="formData.dealTitle"
                              :rules="dealTitleRules"
                              :counter="100">
                      </v-text-field>
                  </v-col>
                  <v-col cols="12" md="6" class="py-0 d-none">
                      <v-text-field label="Price start from"
                              type="number"
                              v-model="startPrice"
                              :rules="priceFromRules">
                      </v-text-field>
                  </v-col>
                  <v-col cols="12" md="6" class="py-0">
                    <v-text-field label="Price upto"
                              type="number"
                              v-model="formData.priceTo"
                              :rules="[(parseFloat(formData.priceFrom) < parseFloat(formData.priceTo)) || 'To price greater than From price.']">
                      </v-text-field>
                  </v-col>
                  <v-col cols="12" md="6" class="py-0 d-none">
                    <v-text-field
                              type="date"
                              label="Date from"
                              v-model="todayDate"
                              :rules="dateFromRules"
                              prepend-inner-icon="mdi-calendar" format="yyyy-mm-dd">
                    </v-text-field>
                  </v-col>
                  <v-col cols="12" md="6" class="py-0">
                    <v-menu
                        ref="menu1"
                        v-model="menu1"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        min-height="290px"
                        max-height="290px"
                        max-width="290px"
                        min-width="auto"
                      >
                    <template v-slot:activator="{ on, attrs }">
                        <v-text-field
                          v-model="formData.dateFormatted"
                          label="Date"
                          persistent-hint
                          prepend-icon="mdi-calendar"
                          v-bind="attrs"
                          @blur="date = parseDate(formData.dateFormatted)"
                          v-on="on"
                        ></v-text-field>
                      </template>
                      <v-date-picker
                        v-model="date"
                        no-title
                        @input="menu1 = false"
                      ></v-date-picker>
                    </v-menu>
                  </v-col>
                  <v-col cols="12" md="12" class="py-0">
                      <span style="color:red">Click box below to upload images (max {{ userDta.images }} images)</span>
                      <v-file-input
                              v-model="formData.selectedFiles"
                              :rules="selectedFilesRules"
                              label="Select images"
                              multiple
                              show-size
                              @change="handleFileChange">
                      </v-file-input>
                      <div v-for="(file, index) in selectedFilesWithPreview" :key="index" class="image-preview">
                        <img :src="file.preview" alt="Preview" class="preview-image">
                        <v-btn icon @click="deleteFile(index)">
                          <v-icon>mdi-close</v-icon>
                        </v-btn>
                      </div>
                  </v-col>
                  <v-col cols="12" md="12" class="py-0">
                      <v-textarea
                              v-model="formData.description"
                              :rules="descriptionRules"
                              :counter="1000"
                              rows="3"
                              row-height="20"
                              label="Description">
                      </v-textarea>
                  </v-col>
                </v-row>
              </v-card-text>
              <div class="ms-3 pb-3">
                <v-btn v-if="isLoading" class="my-2 me-3 cred">Loading...</v-btn>
                <v-btn v-else class="my-2 me-3" color="success" type="submit" :disabled="!valid">SAVE</v-btn>
                <v-btn @click="showModel1 = false" class="my-2 me-3" color="error">CLOSE</v-btn>
              </div>
          </v-form>
        </v-card>
      </v-dialog>
        <v-dialog v-model="MessageModel1" persistent max-width="500">
          <v-card>
            <v-col cols="12" md="12" class="d-flex" style="justify-content: space-between;">
              <div class="mt-1 text-h6">Your present plan has limit of {{ userDta.hot_deals }} hot deals</div>
              <div @click="closeDelete" class="my-2">
                <v-icon>mdi-close</v-icon>
              </div>
            </v-col>
          </v-card>
        </v-dialog>
      <v-dialog v-model="MessageModel" persistent max-width="500">
          <v-card>
            <v-col cols="12" md="12" class="d-flex" style="justify-content: space-between;">
              <div class="mt-1 text-h6">{{ text }}</div>
              <div @click="closeDelete" class="my-2">
                <v-icon>mdi-close</v-icon>
              </div>
            </v-col>
          </v-card>
        </v-dialog>
      </div>
  </template>
  <script>
  import axios from 'axios';
  import SalesView from './AdminSalesView.vue';
  import JobsView from './AdminJobsView.vue';
  import VideosView from './AdminVideosView.vue';
  export default {
    name: 'AdminBusinessdeals',
    components: {SalesView, JobsView, VideosView},
    metaInfo: {title: 'Hot Deals'},
    data: vm => ({
      date: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
      newdate1 :'',
      menu1: false,
      menu2: false,
      url:'https://bizlx.s3.ap-south-1.amazonaws.com',
      search1: '',
      descMaxLength: 20,
      showModel1: false,
      MessageModel: false,
      MessageModel1: false,
      showModel1update: false,
      hotDealModalEdit: false,
      tab: null,
      dialogDelete: false,
      startPrice: 1,
      todayDate: new Date().toJSON().slice(0,10).replace(/-/g,'-'),
      currentDate: '',
      hotDealsHeaders: [
        { text: "Serial #", value: 'sno', sortable: false },
        { text: "Title", value: "hot_deal_title", sortable: false },
        { text: "Price start from", value: "price_from", sortable: false },
        { text: "Price upto", value: "price_to", sortable: false },
        { text: "Date from", value: "date_from", sortable: false },
        { text: "Date to", value: "date_to", sortable: false },
        { text: "Description", value: "description", sortable: false },
        { text: "Status", align: 'end', value: "status", sortable: false },
        { text: "View Link", align: 'end', value: "eye", sortable: false },
        { text: "Actions", align: 'center', value: "action1", sortable: false },
      ],

      userDta: [],
      userdata: [],
      hotdeals: [],
      hotdeals_count: '',
      editedIndex: -1,
      editedItem: {
        date_to:'',
      },
      defaultItem: {
        dealTitle: '',
        price: '',
        shortdescription: '',
      },

      addedDeleted_hotddealsImage_id: [],
      selectedFilesWithPreview: [],
      updateSelectedFilesWithPreview: [],
      addedImageDeletes: [],
      valid: false,
      formData:{
        profile_id: '',
        plan_id: '',
        services: '',
        dealTitle: '',
        priceFrom: 1,
        priceTo: '',
        dateFrom: '',
        dateTo: '',
        hot_deal_status: 1,
        selectedFiles: [],
        multipleFilesUploadByAPI: [],
        updateMultipleFilesUploadByAPI: [],
        description: '',
        dateFormatted: vm.formatDate((new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10)),
      },
      dealTitleRules: [
          v => !!v || 'Deal title is required.',
          v => v.length >= 8 || 'Deal title must be greater than 8 characters.',
      ],
      priceFromRules: [
          v => !!v || 'Price from is required.',
      ],
      priceToRules: [
          v => !!v || 'Price to is required.',
      ],
      dateFromRules: [
          v => !!v || 'Date from is required.',
      ],
      selectedFilesRules: [
          v => !!v[0] || 'Image is required.',
      ],
      descriptionRules: [
          v => !!v || 'Description is required.',
      ],
      text: '',
      isLoading: false,
    }),
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
    created() {
      this.user_id();
      this.userdata = this.user_id();
      if(this.userdata.id == null){
        this.getBusinessServices();

      }else{

        this.getBusinessServicesByReseller();
      }
    },
    computed: {
      itemsWithSno() {
        return this.hotdeals.map((d, index) => ({ ...d, sno: index + 1 }))
      },
      selectedFilesCount() {
        return this.formData.selectedFiles.length;
      },
      formTitle1() {
        return this.editedIndex === -1 ? 'Add Hot Deal' : 'Edit Hot Deal'
      },
      computedDateFormatted () {
          return this.formatDate(this.date)
      },
      formattedDateTo: {
        get() {
          return this.formatedDate(this.editedItem.date_to);
        },
        set(value) {
          this.editedItem.date_to = this.parsedDate(value);
        }
      },
      dateToInternal: {
        get() {
          return this.editedItem.date_to;
        },
        set(value) {
          this.editedItem.date_to = value;
        }
      },
    },
    methods: {
      resetForm() {
            if (this.$refs.form) {
              this.$refs.form.reset();
              this.error = false; // Reset error state if form reset is successful
            } else {
              this.error = true;
              this.errorMessage = 'Form reference is not defined';
            }
          },
          user_id() {
            const url = window.location.href;
            const lastSegment = url.split('/').filter(Boolean).pop();
            return lastSegment;
          },
          handleFileChange() {
            this.selectedFilesWithPreview = [];
            this.multipleFilesUploadByAPI = [];
            for (let i = 0; i < this.formData.selectedFiles.length; i++) {
              const file = this.formData.selectedFiles[i];
              const reader = new FileReader();
              reader.onload = (event) => {
                this.selectedFilesWithPreview.push({
                  file: file,
                  preview: event.target.result
                });
              };
              reader.readAsDataURL(file);
              this.multipleFilesUploadByAPI.push(this.formData.selectedFiles[i]); // Multiple file upload for api
            }
          },
          deleteFile(index) {
            this.formData.selectedFiles.splice(index, 1);
            this.selectedFilesWithPreview.splice(index, 1);
          },

          UpdateHandleFileChange() {
            this.updateSelectedFilesWithPreview = [];
            this.updateMultipleFilesUploadByAPI = [];
            for (let i = 0; i < this.editedItem.updateSelectedFiles.length; i++) {
                  const file = this.editedItem.updateSelectedFiles[i];
                  const reader2 = new FileReader();
                  reader2.onload = (event) => {
                    this.updateSelectedFilesWithPreview.push({
                      file: file,
                      UpdatePreview: event.target.result
                    });
                  };
                  reader2.readAsDataURL(file);
                  this.updateMultipleFilesUploadByAPI.push(this.editedItem.updateSelectedFiles[i]); // Multiple file upload for api
            }
            // console.warn(this.multipleFilesUploadByAPI);
          },
          updateDeleteFile(index) {
            this.editedItem.updateSelectedFiles.splice(index, 1);
            this.updateSelectedFilesWithPreview.splice(index, 1);
          },
          addedDeleteImage(imageId, Dindex) { // uploaded image delete
            this.addedDeleted_hotddealsImage_id.push(imageId);
            this.$delete(this.editedItem.hotdeals_images, Dindex);
          },
          getBusinessServices() {
              this.currentDate = new Date().toJSON().slice(0,10).replace(/-/g,'-');
              axios.post("/api/businesses/hotdeals/getbyadmin",{user_id:this.user_id()})
                .then((resp) =>{
                    this.hotdeals = resp.data.hotDeals;
                    this.hotdeals_count = resp.data.hotDeals.length;
                    this.userDta = resp.data.user;
              });
          },
          getBusinessServicesByReseller() {
              this.currentDate = new Date().toJSON().slice(0,10).replace(/-/g,'-');
              axios.post("/api/businesses/hotdeals/getbyReseller",{userId:this.userdata.id})
              .then((resp) =>{
                  this.hotdeals = resp.data.hotDeals;
                  this.userDta = resp.data.user;
                }
              );
          },
        openhotdeal() {
          this.isLoading = false,
          this.showModel1 = true;
          this.$refs.form.reset();
        },
      editItem(item) {
        this.editedIndex = this.itemsWithSno.indexOf(item);
        this.editedItem = Object.assign({}, item);
        this.hotDealModalEdit = true;
        this.fsdate();
      },

      deleteItem(item) {
        this.editedIndex = this.itemsWithSno.indexOf(item);
        this.editedItem = Object.assign({}, item);
        this.dialogDelete = true;
      },
      deleteItemConfirm() {
          const config = {
              headers: { 'content-type': 'multipart/form-data' }
          }
          let data = {
              hotdel_id: this.editedItem.id,
              services: 'hot_deals_delete',
          };
          axios.post("/api/businesses/hotdeals/addbyReseller", data, config)
              .then((resp) => { // delete
                this.closeDelete();
                  this.text = resp.data.message;
                  this.MessageModel = true,
                this.getBusinessServices();
            });
      },
      close() {
        this.hotDealModalEdit = false;
        this.updateMultipleFilesUploadByAPI = [];
        this.addedDeleted_hotddealsImage_id = [];
        this.updateSelectedFilesWithPreview = [];
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem);
          this.editedIndex = -1;
        });
      },
      closeDelete() {
        this.dialogDelete = false;
        this.MessageModel = false;
        this.MessageModel1 = false;
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem);
          this.editedIndex = -1;
        });
      },



      dealAddFormSubmit() {
        const config = {
            headers: { 'content-type': 'multipart/form-data' }
        };

        if (this.editedIndex > -1) { // edit deals
            let data = {
                hotdel_id: this.editedItem.id,
                profile_id: this.editedItem.profile_id,
                plan_id: this.userDta.plan_id,
                services: 'hot_deals_update',
                hot_deal_title: this.editedItem.hot_deal_title,
                hot_deal_description: this.editedItem.hot_deal_description,
                price_from: 1,
                price_to: this.editedItem.price_to,
                date_from: new Date().toJSON().slice(0, 10).replace(/-/g, '-'),
                date_to: this.editedItem.date_to,
                hot_deal_status: this.editedItem.hot_deal_status,
                hotdeal_img: this.updateMultipleFilesUploadByAPI,
                oldDeleted_HotDeals_id: this.addedDeleted_hotddealsImage_id,
                user_id: this.user_id(),
            };

            this.isLoading = true;

            axios.post("/api/businesses/hotdeals/addbyadmin", data, config)
                .then((resp) => {
                    this.close();
                    this.MessageModel = true;
                    this.text = resp.data.message;
                    this.isLoading = false;
                    this.getBusinessServices();
                    this.$refs.form.reset();
                });

            Object.assign(this.hotdeals[this.editedIndex], this.editedItem);
        } else { // Add new deal
            let data = {
                profile_id: this.userDta.profile_id,
                plan_id: this.userDta.plan_id,
                services: 'hot_deals_add',
                hot_deal_title: this.formData.dealTitle,
                hot_deal_description: this.formData.description,
                price_from: 1,
                price_to: this.formData.priceTo,
                date_from: new Date().toJSON().slice(0, 10).replace(/-/g, '-'),
                date_to: this.date,
                hotdeal_img: this.multipleFilesUploadByAPI,
                hot_deal_status: 1,
                user_id: this.user_id(),
            };

                if (this.formData.dealTitle && this.formData.priceFrom && this.formData.description) {
                    this.isLoading = true;

                axios.post("/api/businesses/hotdeals/addbyadmin", data, config)
                    .then((resp) => {
                        this.showModel1 = false;
                        this.MessageModel = true;
                        this.text = resp.data.message;
                        this.isLoading = false;
                        this.getBusinessServices();
                        this.$refs.form.reset();
                    });
            }
          }

        this.$refs.form.validate();
        this.updateMultipleFilesUploadByAPI = [];
        this.addedDeleted_hotddealsImage_id = [];
    },

    formatDate (date) {
      if (!date) return null
      const [year, month, day] = date.split('-')
      return `${day}-${month}-${year}`
      },
      parseDate (date) {
        if (!date) return null
        const [year, month, day] = date.split('-')
        return `${day.padStart(2, '0')}-${month.padStart(2, '0')}-${year}`
      },
      formatedDate(date) {
      if (!date) return '';
      const [year, month, day] = date.split('-');
      return `${day}-${month}-${year}`;
    },
    parsedDate(date) {
      if (!date) return '';
      const [day, month, year] = date.split('-');
      return `${year}-${month}-${day}`;
    }
    },
    watch: {
        date() {
          this.formData.dateFormatted = this.formatDate(this.date);
        },
        editdate() {
          this.formData.dateFormatted = this.formatedDate(this.date);
        },
      },
  }
  </script>

  <style scoped>
  .image-preview {
    display: inline-block;
    margin-right: 10px;
  }
  .preview-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
  }
  .cgreen{
    color: green;
  }
  </style>



