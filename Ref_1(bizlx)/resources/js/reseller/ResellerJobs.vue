<template>
  <div>
    <div class="row pb-3">
            <div class="col-12 col-md-4 py-0"><h4>Jobs</h4></div>
            <div class="col-12 col-md-6 py-0">
              <v-text-field v-model="search" append-icon="mdi-magnify" label="Search" single-line hide-details>
              </v-text-field>
            </div>
            <div class="col-12 col-md-2 test-decoration-none">
              <v-btn @click="openjobs" class="cred"  tile large>
                <v-icon left>
                  mdi-plus
                </v-icon>
                CREATE JOBS
              </v-btn>
            </div>
          </div>
    <div class="">
            <v-data-table :search="search" :headers="headers" :items="desserts"
                          :single-select="singleSelect" show-select class="elevation-1">
              <template v-slot:top>
                <v-dialog v-model="dialog" max-width="500px">
                  <v-card>
                    <v-card-title>
                      <span class="text-h5">{{ formTitle }}</span>
                    </v-card-title>
                    <v-card-text>
                      <v-container>
                        <v-row>
                          <v-col cols="12" sm="6" md="12">
                            <v-text-field v-model="editedItem.name" label="Business Name"></v-text-field>
                          </v-col>
                          <v-col cols="12" sm="6" md="12">
                            <v-text-field v-model="editedItem.title" label="Title"></v-text-field>
                          </v-col>
                          <v-col cols="12" md="12" class="py-0">
                            <v-select v-model="editedItem.job_type" :items="job_type1" label="Select Job Type"></v-select>
                          </v-col>
                          <v-col cols="12" md="12" class="py-0">
                            <v-select v-model="editedItem.qualifications" :items="qualifications1" label="Select Qualifications"></v-select>
                          </v-col>
                          <v-col cols="12" md="12" class="py-0">
                            <div class="range-slider sec1 bg-white">
                                <v-text-field type="number" label="Salary" v-model="editedItem.salary" class="me-2 text-center"></v-text-field>
                            </div>
                          </v-col>
                          <v-col cols="12" md="12" class="py-0">
                            <div class="range-slider sec1 bg-white">
                                <v-text-field type="number" label="Experience" v-model="editedItem.experience" class="me-2 text-center"></v-text-field>
                            </div>
                          </v-col>
                        </v-row>
                      </v-container>
                    </v-card-text>
                    <v-card-actions>
                      <v-spacer></v-spacer>
                      <v-btn class="cred" text @click="close">
                        CANCEL
                      </v-btn>
                      <v-btn class="cred" text @click="save">
                        UPDATE
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-dialog>
                <v-dialog v-model="dialogDelete" max-width="500px">
                  <v-card>
                    <v-card-title class="text-h5">Are you sure you want to delete this
                      item?</v-card-title>
                    <v-card-actions>
                      <v-spacer></v-spacer>
                      <v-btn color="blue darken-1" text @click="closeDelete">CANCEL</v-btn>
                      <v-btn color="blue darken-1" text @click="deleteItemConfirm">OK</v-btn>
                      <v-spacer></v-spacer>
                    </v-card-actions>
                  </v-card>
                </v-dialog>
              </template>
              <template v-slot:[`item.action`]="{ item }">
                <v-icon small class="mr-2" @click="editItem(item)">
                  mdi-pencil
                </v-icon>
                <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
              </template>
              <template v-slot:[`item.active`]>
                <v-icon small class="mr-2">
                  mdi-check
                </v-icon>
              </template>
            </v-data-table>
          </div>
    <v-dialog v-model="showModel" persistent max-width="900">
      <v-card>
        <v-card-title class="text-h5 grey lighten-2">
          Add Jobs
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12" md="12" class="py-0">
              <v-autocomplete :items="business" label="Select Business"></v-autocomplete>
            </v-col>
            <v-col cols="12" md="12" class="py-0">
              <v-text-field label="Enter Job Title"></v-text-field>
            </v-col>
            <v-col cols="12" md="12" class="py-0">
              <v-select :items="job_type1" label="Select Job Type"></v-select>
            </v-col>
            <v-col cols="12" md="12" class="py-0">
              <v-select :items="qualifications1" label="Select Qualifications"></v-select>
            </v-col>
            <v-col cols="12" md="6" class="py-0">
              <v-text-field label="Price From"></v-text-field>
            </v-col>
            <v-col cols="12" md="6" class="py-0">
              <v-text-field label=" Experience"></v-text-field>
            </v-col>
            <v-col cols="12" md="12" class="py-0">
              <v-textarea rows="3" row-height="20"  label="Enter Job Description"></v-textarea>
            </v-col>
          </v-row>
        </v-card-text>
        <div class="text-end">
          <v-btn class="my-2 me-3 cred">UPDATE</v-btn>
          <v-btn @click="showModel = false" class="my-2 me-3 cred">CLOSE</v-btn>
        </div>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
export default {
  name: 'ResellerJobs',
  components: {},
  metaInfo: {title: 'All Jobs'},
  data: () => ({
    search: '',
    showModel: '',
    singleSelect: '',
    dialog: false,
    dialogDelete: false,
    desserts: [],
    job_type1: ['Full-time', 'Part-time'],
    qualifications1: ['10th', '12th', 'BA'],
    business: [
      'Himachal Taxi Service', 'Focus Smiles Dental Clinic', 'YouTube Promotion Agency', 'Sunny Taxi & adventure',
      'Digitization Solutions', 'Complete Dental Solutions'
    ],
    headers: [
      {text: "Business name", value: "name", sortable: false},
      {text: "Title", value: "title", sortable: false},
      {text: "Salary", value: "salary", sortable: false},
      {text: "Job Type", value: "job_type", sortable: false},
      {text: "Qualifications", value: "qualifications", sortable: false},
      {text: "Experience", value: "experience", sortable: false},
      {text: "Active", align: 'end', value: "active", sortable: false},
      {text: "Actions", align: 'end', value: "action", sortable: false},
    ],
    editedIndex: -1,
    editedItem: {
      name: "",
      title: "",
      minsalary: "",
      maxsalary: "",
      job_type: "",
      qualifications: "",
      minexperience: "",
      maxexperience: "",
    },
  }),
  created() {
    this.getjobs();
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? '' : 'Edit Jobs'
    },
  },

  methods: {
    openjobs() {
      this.showModel = true;
    },
    getjobs() {
      this.desserts = [
        {
          name: "Himachal Taxi Service",
          title: "Tandoori Cook",
          salary: "10000",
          job_type: "Full-time",
          qualifications: "12th",
          experience: "2",
        },
      ];
    },

    editItem(item) {
      this.editedIndex = this.desserts.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    deleteItem(item) {
      this.editedIndex = this.desserts.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },

    deleteItemConfirm() {
      this.desserts.splice(this.editedIndex, 1);
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

    save() {
      if (this.editedIndex > -1) {
        Object.assign(this.desserts[this.editedIndex], this.editedItem);
      } else {
        this.desserts.push(this.editedItem);
      }
      this.close();
    },
  },
}
</script>