<template>
    <div>
        <div class="row pb-3">
          <div class="col-12 col-sm-4">
            <v-text-field v-model="search1" append-icon="mdi-magnify" label="Search" single-line hide-details>
            </v-text-field>
          </div>
          <div class="col-12 col-sm-4 test-decoration-none">
            <div v-if="salescount === userDta.sale">
              <v-btn @click="MessageModel1 = true" color="error" tile large>
                <v-icon left>mdi-plus</v-icon>CREATE SALE
              </v-btn>
            </div>
            <div v-else>
              <v-btn @click="openSales" color="error"  tile large>
                <v-icon left>mdi-plus</v-icon>CREATE SALE
              </v-btn>
            </div>
          </div>
        </div>
          <v-data-table :search="search1" :headers="headers1" :items="itemsWithSno" class="elevation-1">
            <template v-slot:top>

              <v-dialog v-model="saleModalEdit1" max-width="900px">
                <v-card>
                  <v-form @submit.prevent="saleFormSubmit" ref="form" v-model="valid" lazy-validation>
                      <v-card-title class="text-h5 grey lighten-2" style="justify-content:space-between;">{{ formTitle1 }}
                        <v-icon  @click="close" class="text-h5">mdi-close</v-icon>
                      </v-card-title>
                      <v-card-text>
                        <v-row>
                          <v-col cols="12" md="12" class="py-0" >
                              <v-text-field label="Sale title"
                                      type="text"
                                      v-model="editedItem.sale_title"
                                      :rules="saleTitleRules"
                                      :counter="100">
                              </v-text-field>
                          </v-col>
                          <v-col cols="12" md="6" class="py-0">
                              <v-text-field label="Normal price"
                                      type="number"
                                      v-model="editedItem.normal_price"
                                      :rules="normal_priceRules">
                              </v-text-field>
                          </v-col>
                          <v-col cols="12" md="6" class="py-0">
                            <v-text-field label="Sale price"
                                      type="number"
                                      v-model="editedItem.sale_price"
                                      :rules="[(parseFloat(editedItem.normal_price) >= parseFloat(editedItem.sale_price)) || 'Sale price will be less from normal price.']">
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
                              @blur="editdate = parsedDate(editedItem.saledate_to)"
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
                              <span style="color:red">Your have uploaded ({{ editedItem.sales_images_count }}) images</span>
                              <div v-if="editedItem.sales_images!=''">
                                <v-row>
                                    <v-col v-for="(img, Dindex) in editedItem.sales_images" :key="Dindex" cols="12" md="4">
                                        <v-img :src=(url+img.sale_img_url) class="rounded-3" height="50" width="50"></v-img>
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
                                      v-model="editedItem.sale_description"
                                      :rules="descriptionRules"
                                      :counter="1000"
                                      rows="3"
                                      row-height="20"
                                      label="Description">
                              </v-textarea>
                          </v-col>
                          <v-col cols="12" md="12" class="py-0 d-flex" style="justify-content: space-between;">
                                <div>
                                  <v-checkbox v-model="editedItem.sale_status" label="Active"></v-checkbox>
                                </div>
                                <div class="ms-3 pb-3">
                                  <v-btn v-if="isLoading" class="my-2 me-3 cred">Loading...</v-btn>
                                  <v-btn v-else class="my-2 me-3" color="success" type="submit" :disabled="!valid">UPDATE</v-btn>
                                  <v-btn @click="close" class="my-2 me-3" color="error">CLOSE</v-btn>
                                </div>
                          </v-col>
                        </v-row>
                      </v-card-text>
                  </v-form>
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
            </template>
              <template v-slot:[`item.description`]="{ item }">
                  <div>{{ item.sale_description.slice(0, descMaxLength)+'...' }}</div>
              </template>
              <template v-slot:[`item.saledate_from`] = {item}>
              {{ item.saledate_from | fdate }}
            </template>
            <template v-slot:[`item.saledate_to`] = {item}>
              {{ item.saledate_to | fdate }}
            </template>
              <template v-slot:[`item.status`]="{ item }">
                <div v-if="item.sale_status === 1" style="color:green">
                  <div v-if="item.saledate_to < currentDate" class="cred">Expired</div>
                  <div v-else><span style="color:green">Active</span></div>
                </div>
                <div v-else>
                  <div v-if="item.saledate_to < currentDate" class="cred">Expired</div>
                  <div v-else><span style="color:gray">Inactive</span></div>
                </div>
            </template>

            <template v-slot:[`item.eye`]="{ item }">
              <a :href= "'/sales/detail/'+item.sale_slug"  target="_blank" class="link-dark">
                <v-icon small class="mr-2">mdi-eye </v-icon>
              </a>
            </template>

            <template v-slot:[`item.action`]="{ item }">
              <v-icon small class="mr-2" @click="editItem(item)">mdi-pencil</v-icon>
              <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
            </template>
          </v-data-table>
          <v-dialog v-model="showModel1" persistent max-width="900">
            <v-card>
              <v-form @submit.prevent="saleFormSubmit" ref="form" v-model="valid" lazy-validation>
                  <v-card-title class="text-h5 grey lighten-2 d-flex" style="justify-content:space-between;">{{ formTitle1 }}
                <v-icon  @click="showModel1 = false" class="text-h5">mdi-close</v-icon>
              </v-card-title>
                  <v-card-text>
                    <v-row>
                      <v-col cols="12" md="12" class="py-0" >
                          <v-text-field label="Sale title"
                                  type="text"
                                  v-model="formData.saleTitle"
                                  :rules="saleTitleRules"
                                  :counter="100">
                          </v-text-field>
                      </v-col>
                      <v-col cols="12" md="6" class="py-0">
                          <v-text-field label="Normal price"
                                  type="number"
                                  v-model="formData.normal_price"
                                  :rules="normal_priceRules">
                          </v-text-field>
                      </v-col>
                      <v-col cols="12" md="6" class="py-0">
                        <v-text-field label="Sale price"
                                  type="number"
                                  v-model="formData.sale_price"
                                  :rules="[(parseFloat(formData.normal_price) >= parseFloat(formData.sale_price)) || 'Sale price will be less from normal price.']">
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
                <div class="mt-1 text-h6">Your present plan has limit of {{userDta.sale}} sales </div>
                <div @click="closeDelete" class="my-2">
                   <v-icon>mdi-close</v-icon>
                </div>
              </v-col>
            </v-card>
          </v-dialog>
          <v-dialog v-model="MessageModel2" persistent max-width="500">
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
export default {
    name:'SalesView',
    metaInfo: {title: 'Sales'},
  data: vm =>  ({
    url:'https://bizlx.s3.ap-south-1.amazonaws.com',
    date: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
    menu1: false,
    menu2: false,
    search1: '',
    descMaxLength: 20,
    showModel1: false,
    MessageModel2: false,
    MessageModel1: false,
    todayDate: new Date().toJSON().slice(0,10).replace(/-/g,'-'),
    saleModalEdit1: false,
    dialogDelete: false,
    currentDate: '',
    headers1: [
      { text: "Serial #", value: 'sno', sortable: false },
      { text: "Title", value: "sale_title", sortable: false },
      { text: "Normal price", value: "normal_price", sortable: false },
      { text: "Sale price", value: "sale_price", sortable: false },
      { text: "Sale from", value: "saledate_from", sortable: false },
      { text: "Sale to", value: "saledate_to", sortable: false },
      { text: "Description", value: "description", sortable: false },
      { text: "Status", align: 'end', value: "status", sortable: false },
      { text: "View Link", align: 'end', value: "eye", sortable: false },
      { text: "Actions", align: 'end', value: "action", sortable: false },
    ],
    salesData: [],
    salescount: '',
    userDta: [],
    valid: false,
    addedDeleted_salesImage_id: [],
    selectedFilesWithPreview: [],
    updateSelectedFilesWithPreview: [],
    addedImageDeletes: [],
    formData:{
      user_id: '',
      plan_id: '',
      services: '',
      saleTitle: '',
      normal_price: '',
      sale_price: '',
      dateFrom: '',
      dateTo: '',
      sale_status: 1,
      selectedFiles: [],
      multipleFilesUploadByAPI: [],
      updateMultipleFilesUploadByAPI: [],
      description: '',
      dateFormatted: vm.formatDate((new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10)),
    },

    saleTitleRules: [
        v => !!v || 'Sale title is required.',
        v => v.length >= 8 || 'Sale title must be greater than 8 characters.',
    ],
    normal_priceRules: [
        v => !!v || 'Normal price is required.',
    ],
    sale_priceRules: [
        v => !!v || 'Sale price is required.',
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
    // snackbar: false,
    // timeout: '',
    text: '',
    isLoading: false,

    editedIndex: -1,
    editedItem: {},
    defaultItem: {
        title: '',
        price: '',
        shortdescription: '',
    },
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
    if(this.userdata.id != null){
      this.getsalesByReseller();
    }else{
      this.getsales();
    }

  },
  computed: {
    itemsWithSno() {
      return this.salesData.map((d, index) => ({ ...d, sno: index + 1 }))
    },
    formTitle1() {
      return this.editedIndex === -1 ? 'Add Sale' : 'Edit Sale'
    },
    selectedFilesCount() {
      return this.formData.selectedFiles.length;
    },
    computedDateFormatted () {
        return this.formatDate(this.date)
    },
    formattedDateTo: {
      get() {
        return this.formatedDate(this.editedItem.saledate_to);
      },
      set(value) {
        this.editedItem.saledate_to = this.parsedDate(value);
      }
    },
    dateToInternal: {
      get() {
        return this.editedItem.saledate_to;
      },
      set(value) {
        this.editedItem.saledate_to = value;
      }
    },
  },

  methods:{
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
          this.addedDeleted_salesImage_id.push(imageId);
          this.$delete(this.editedItem.sales_images, Dindex);
        },

      openSales() {
        this.showModel1 = true;
        this.$refs.form.reset();
      },
    editItem(item) {
      this.editedIndex = this.itemsWithSno.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.saleModalEdit1 = true;
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
            sale_id: this.editedItem.id,
            services: 'sale_delete',
        };
        if(this.userdata.id != null){ // delete by Reseller after login
          axios.post("/api/businesses/sales/submit", data, config)
            .then((resp) => { // delete
              this.closeDelete();
                this.text = resp.data.message;
                this.MessageModel2 = true,
              this.getsalesByReseller();
            }
          );
        }else{ // delete by business
          axios.post("/api/businesses/sales/submit", data, config)
            .then((resp) => { // delete
              this.closeDelete();
                this.text = resp.data.message;
                this.MessageModel2 = true;
              this.getsales();
            }
          );
        }
    },

    close() {
      this.saleModalEdit1 = false;
      this.updateSelectedFilesWithPreview = [];
      this.updateMultipleFilesUploadByAPI = [];
      this.addedDeleted_salesImage_id = [];
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
        // this.$refs.form.reset();
      });
    },

    closeDelete() {
      this.dialogDelete = false;
      this.MessageModel1 = false;
      this.MessageModel2 = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },
    getsalesByReseller() {
        this.currentDate = new Date().toJSON().slice(0,10).replace(/-/g,'-');
        axios.post("/api/businesses/sales/getbyReseller",{userId:this.userdata.id})
          .then((resp) =>{
              this.salesData = resp.data.sales;
              this.salescount = resp.data.sales.length;
              this.userDta = resp.data.user;
          });
    },
    getsales() {
        // alert('hello');
      this.currentDate = new Date().toJSON().slice(0,10).replace(/-/g,'-');
        axios.post("/api/businesses/sales/getbyadmin", {user_id:this.user_id()})
          .then((resp) =>{
            console.log(resp);
                this.salesData = resp.data.sales;
                this.salescount = resp.data.sales.length;
                this.userDta = resp.data.user;
            }
        );
    },

    saleFormSubmit() {
      if (this.editedIndex > -1) { // edit sale
          const config = {
              headers: { 'content-type': 'multipart/form-data' }
          }
          this.isLoading = true;
          let data = {
              user_id: this.editedItem.user_id,
              sale_id: this.editedItem.id,
              plan_id: this.userDta.plan_id,
              services: 'sale_update',
              sale_title: this.editedItem.sale_title,
              sale_description: this.editedItem.sale_description,
              normal_price: this.editedItem.normal_price,
              sale_price: this.editedItem.sale_price,
              // saledate_from: this.todayDate,
              saledate_from: new Date().toJSON().slice(0,10).replace(/-/g,'-'),
              saledate_to: this.editedItem.saledate_to,
              sale_status: this.editedItem.sale_status,
              sale_imageurl: this.updateMultipleFilesUploadByAPI,
              oldDeleted_sales_image_id: this.addedDeleted_salesImage_id,
              user_id: this.user_id(),
          };
          if(this.userdata.id != null){ // update by Reseller after login
            axios.post("/api/businesses/sales/submitbyReseller", data, config)
              .then((resp) => {
                this.close();
                  this.text = resp.data.message;
                  this.MessageModel2 = true;
                this.isLoading = false;
                this.getsalesByReseller();
                this.$refs.form.reset();
              }
            );
          }else{ // update by business
            axios.post("/api/businesses/sales/submit", data, config)
              .then((resp) => {
                this.close();
                  this.text = resp.data.message;
                  this.MessageModel2 = true;
                this.isLoading = false;
                this.getsales();
                this.$refs.form.reset();
              }
            );
          }

        Object.assign(this.hotdeals[this.editedIndex], this.editedItem);
      } else { // Add new sale
        const config = {
            headers: { 'content-type': 'multipart/form-data' }
        }
        let data = {
            user_id: this.userDta.user_id,
            plan_id: this.userDta.plan_id,
            services: 'sale_add',
            sale_title: this.formData.saleTitle,
            sale_description: this.formData.description,
            normal_price: this.formData.normal_price,
            sale_price: this.formData.sale_price,
            // saledate_from: this.todayDate,
            saledate_from: new Date().toJSON().slice(0,10).replace(/-/g,'-'),
            saledate_to: this.date,
            sale_status: 1,
            sale_imageurl: this.multipleFilesUploadByAPI,
        };
        console.log(data);
        if(this.formData.saleTitle!='' && this.formData.normal_price!='' && this.formData.sale_price!='' && this.date!='' && this.formData.description!=''){
          this.isLoading = true;
          if(this.userdata.id != null){ // add by Reseller after login
            axios.post("/api/businesses/sales/submitbyReseller", data, config)
              .then((resp) => {
                  this.showModel1 = false;
                    this.text = resp.data.message;
                    this.MessageModel2 = true,
                  this.isLoading = false;
                  this.getsalesByReseller();
                  this.$refs.form.reset();
            });
          }else{ // add by business
            axios.post("/api/businesses/sales/submit", data, config)
              .then((resp) => {
                  this.showModel1 = false;
                    this.text = resp.data.message;
                    this.MessageModel2 = true,
                  this.isLoading = false;
                  this.getsales();
                  this.$refs.form.reset();
            });
          }
        }
        this.$refs.form.validate();
      }
      this.updateMultipleFilesUploadByAPI = [];
      this.addedDeleted_salesImage_id = [];
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
<style>
.image-preview {
  display: inline-block;
  margin-right: 10px;
}
.preview-image {
  width: 100px;
  height: 100px;
  object-fit: cover;
}
</style>
