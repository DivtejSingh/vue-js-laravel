<template>
  <div>
    <v-row>
        <h2 class="text-h6 mb-1">Product-Offers</h2>
    </v-row>
    <v-container fluid>
    <v-row class="mt-0 pt-0">
      <v-col cols="12" md="11" class="p-0">
        <v-text-field v-model="ssearch" clearable dense hide-details outlined prepend-inner-icon="mdi-magnify mb-2" 
                    placeholder="Search name"/>
      </v-col>
      <v-col cols="12" md="1" class="text-end p-0 ps-2">
        <v-btn color="secondary" small class="text-none w-100" style="height: 32px; color: #1976d2; font-weight: bold;
        background-color: white !important; border: 1px solid #1976d2 !important;" @click="openAddDialog">
          Add Offers
        </v-btn>
      </v-col>
    </v-row>
</v-container>
    <!-- table -->
    <v-row class="mt-0">
        <v-col cols="12">
            <v-card outlined>
              <v-data-table v-model="selectedtag" item-key="product_offer_id" :show-select="true" :items="productOffers" :headers="offerheaders" :search="ssearch" :footer-props="{
                'items-per-page-options': [10, 25, 50, 100], 'items-per-page-text': 'Rows per page:'}">
                <template #header.actions1>
                    <div class="text-center">Action</div>
                </template>
                <template v-slot:item.index="{ index }">
  {{ index + 1 }}
</template>

                <template #item.actions1="{ item }">
                    <div class="text-center">
                        <v-chip color="primary" class="white--text" outlined pill small @click="openEditDialog(item)" 
                            style="cursor: pointer;">
                            <v-icon small left>mdi-pencil</v-icon>
                            Edit
                        </v-chip>
                    </div>
                </template>
                <template #header.actions2>
                    <div class="text-center">Action</div>
                </template>
                <template #item.actions2="{ item }">
                    <div class="text-center">
                        <v-chip color="red" class="white--text" outlined pill small @click="confirmDelete(item)" 
                            style="cursor: pointer;">
                            <v-icon small left>mdi-delete</v-icon>
                            Delete
                        </v-chip>
                    </div>
                </template>
                <template #header.delete>
                    <div class="d-flex justify-end align-center">
                        <span v-if="!selectedtag.length"></span>

                        <v-menu v-if="selectedtag.length" offset-y>
                        <template v-slot:activator="{ on, attrs }">
                            <div class="d-flex align-center">
                            <span class="mr-2 font-weight-medium text-caption">{{ selectedtag.length }} selected</span>
                            <v-icon
                                color="primary"
                                v-bind="attrs"
                                v-on="on"
                                style="cursor: pointer;"
                            >
                                mdi-dots-vertical
                            </v-icon>
                            </div>
                        </template>

                        <v-list dense>
                            <v-list-item @click="confirmBulkDelete">
                              <v-list-item-title>Delete</v-list-item-title>
                            </v-list-item>

                            <v-list-item @click="confirmBulkAdd">
                              <v-list-item-title>Add Tag</v-list-item-title>
                            </v-list-item>

                            <v-list-item @click="confirmBulkRemove">
                              <v-list-item-title>Remove Tag</v-list-item-title>
                            </v-list-item>
                        </v-list>
                        </v-menu>
                    </div>
                </template>
              </v-data-table>
            </v-card>
        </v-col>
      </v-row>
    <!-- ADD dialog -->
    <v-dialog v-model="addDialog" max-width="620" @update:model-value="resetAdd">
      <v-card>
        <v-card-title class="d-flex align-center">
          <span>Add Offer</span>
          <v-spacer></v-spacer>
          <v-icon @click="addDialog=false">mdi-close</v-icon>
        </v-card-title>
        <v-form v-model="addValid" @submit.prevent="saveAdd">
          <v-card-text>
            <v-autocomplete
              v-model="addForm.product_ids"
              :items="products"
              item-text="mproduct_title"
              item-value="mproduct_id"
              label="Products"
              multiple
              small-chips deletable-chips
              :rules="[required]"
              @change="loadAddVariants"
            />

            <v-autocomplete
              v-model="addForm.variant_ids"
              :items="addVariants"
              item-text="option_label"
              item-value="mvariant_id"
              label="Variants"
              multiple
              small-chips deletable-chips
              :rules="[required]"
            />
            <v-text-field v-model="addForm.product_deal_tag"
                          label="Product Deal Tag"/>
            <v-text-field v-model="addForm.product_offer"
                          label="Product Offer"/>
          </v-card-text>
          <v-card-actions>
            <v-btn type="submit" color="success" :disabled="!addValid || saving">
              Add Offer
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>

    <!-- EDIT dialog -->
    <v-dialog v-model="editDialog" max-width="620" @update:model-value="resetEdit">
      <v-card>
        <v-card-title class="d-flex align-center">
          <span>Edit Offer</span>
          <v-spacer></v-spacer>
          <v-icon @click="editDialog=false">mdi-close</v-icon>
        </v-card-title>
        <v-form v-model="editValid" @submit.prevent="saveEdit">
          <v-card-text>
            <v-autocomplete v-model="editForm.product_id"
                            :items="products"
                            item-text="mproduct_title"
                            item-value="mproduct_id"
                            label="Product"
                            @change="loadEditVariants"
                            :rules="[required]"/>
                          <v-autocomplete
                            v-model="editForm.variant_id"
                            :items="editVariants"
                            item-text="option_label"
                            item-value="mvariant_id"
                            label="Variant"
                            :rules="[required]"
                            clearable
                          />

            <v-text-field v-model="editForm.product_deal_tag"
                          label="Product Deal Tag"/>
            <v-text-field v-model="editForm.product_offer"
                          label="Product Offer"/>
          </v-card-text>
          <v-card-actions>
            <v-btn type="submit" color="success" :disabled="!editValid || saving">
              Update Offer
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>

      <!-- Delete dialog -->
      <v-dialog v-model="deleteDialog" max-width="400">
        <v-card>
          <v-card-title class="text-h6">
            Confirm Delete
          </v-card-title>
          <v-card-text>
            Are you sure you want to delete this offer?
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn text color="grey" @click="deleteDialog = false">Cancel</v-btn>
            <v-btn text color="red" :loading="deleteLoading" :disabled="deleteLoading" @click="performDelete">Delete</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- Bulk-delete confirmation -->
      <v-dialog v-model="bulkDeleteDialog" max-width="400">
          <v-card>
              <v-card-title class="text-h6">Confirm Delete</v-card-title>
              <v-card-text>
              Are you sure you want to delete <strong>{{ selectedtag.length }}</strong> offers?
              </v-card-text>
              <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn text color="grey" @click="bulkDeleteDialog = false">Cancel</v-btn>
              <v-btn
                  text
                  color="red"
                  :loading="bulkDeleteLoading"
                  :disabled="bulkDeleteLoading"
                  @click="performBulkDelete"
              >
                  Delete
              </v-btn>
              </v-card-actions>
          </v-card>
      </v-dialog>

      <!-- Bulk-add confirmation -->
      <v-dialog v-model="bulkAddDialog" max-width="500">
        <v-card>
          <v-card-title class="text-h6">Add Tags</v-card-title>

          <v-card-text>
            <v-text-field
              v-model="bulk_product_tag"
              label="Product tag"
              :rules="[
                v => !!v || 'Tag is required',
                v => (v && v.length >= 3) || 'Minimum 3 characters required'
              ]"
              required
            />
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn text @click="bulkAddDialog = false">Cancel</v-btn>
            <v-btn
              text
              color="red"
              :loading="bulkAddLoading"
              :disabled="bulkAddLoading || !bulk_product_tag"
              @click="performBulkAdd"
            >
              Add
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>


        <!-- Bulk-remove confirmation -->
      <v-dialog v-model="bulkRemoveDialog" max-width="500">
            <v-card>
                <v-card-title class="text-h6">Remove Tag</v-card-title>
                <v-card-text>
                Are you sure you want to remove <strong>{{ selectedtag.length }}</strong> offers tag?
              </v-card-text>
                <v-spacer></v-spacer>
                <v-card-actions>
                    <v-spacer />
                    <v-btn text @click="bulkRemoveDialog = false">Cancel</v-btn>
                    <v-btn
                        text
                        color="red"
                        :loading="bulkRemoveLoading"
                        :disabled="bulkRemoveLoading"
                        @click="performBulkRemove"
                    >
                        Remove
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "ProductCreateOffer",

  data() {
    return {
      /* table */
      ssearch: '',
      offerheaders: [
        { text: '', value: 'data-table-select', width: '10px' },
        { text: 'Index', value: 'index', sortable: true, width: '100px' },      
        { text: "Product Name", value: "mproduct_title", width: '300px' },
        { text: "Product Offer", value: "product_offer", width: '225px' },
        { text: "Product Deal Tag", value: "product_deal_tag", width: '225px' },
        { text: 'Action', value: 'actions1', sortable: false, width: '150px' },
        { text: 'Action', value: 'actions2', sortable: false, width: '150px' },
        { text: '', value: 'delete', sortable: false }
      ],
      products: [],    
      productOffers: [], 

      addDialog: false,
      addValid: false,
      addVariants: [],
      addForm: {
        product_ids: [],
        variant_ids: [],
        product_deal_tag: "",
        product_offer: "",
      },

      editDialog: false,
      editValid: false,
      editVariants: [],
      editForm: {
        product_offer_id: null,
        product_id: null,
        variant_id: null,
        product_deal_tag: "",
        product_offer: "",
      },

      deleteDialog: false,
      offerToDelete: null,
      deleteLoading: false,
      saving: false,   
      required: (v) => !!v || "Required",

      bulk_product_tag: null,

      selectedtag: [],         
      bulkDeleteDialog: false,
      bulkDeleteLoading: false,

      bulkAddDialog: false,
      bulkAddLoading: false,

      bulkRemoveDialog: false,
      bulkRemoveLoading: false,
    };
  },

  created() {
    this.loadAll();
  },

  methods: {
    async loadAll() {
  const { data } = await axios.get("/admin/product-offers/vlist");

  this.products = data.products;

  this.productOffers = data.productoffers.map(offer => {
    const product = this.products.find(p =>
      p.mvariants.some(v => v.mvariant_id === offer.mvariant_id)
    );
    return {
      ...offer,
      mproduct_title: product ? product.mproduct_title : ''
    };
  });
},
    openAddDialog() {
      this.resetAdd(false);
      this.addDialog = true;
    },
    async loadAddVariants() {
      this.addVariants = [];

      if (!this.addForm.product_ids || this.addForm.product_ids.length === 0) return;

      try {
        const { data } = await axios.get('/admin/product-offers/vlist');
        const allVariants = [];

        for (const product of data.products) {
          if (!this.addForm.product_ids.includes(product.mproduct_id)) continue;

          for (const variant of product.mvariants) {
            // Parse option_value
            let optionVal = variant.option_value;
            if (typeof optionVal === 'string') {
              try {
                optionVal = JSON.parse(optionVal);
              } catch (e) {
                optionVal = {};
              }
            }

            const isEmpty = !optionVal || Object.keys(optionVal).length === 0;

            const label = isEmpty
              ? product.mproduct_title
              : `${product.mproduct_title} - ${Object.entries(optionVal).map(([key, val]) => `${key}: ${val}`).join(' / ')}`;

            allVariants.push({
              mvariant_id: variant.mvariant_id,
              option_value: optionVal,
              option_label: label
            });
          }
        }

        this.addVariants = allVariants;
      } catch (e) {
        console.error('❌ Failed to load variants:', e);
      }
    },
    resetAdd() {
      this.addForm = {
        product_ids: [],
        variant_ids: [],
        product_deal_tag: '',
        product_offer: ''
      };
      this.addVariants = [];
    },

    async saveAdd() {
  if (!this.addValid || this.saving) return;
  this.saving = true;

  try {
      await axios.post("/admin/product-offers/add", {
        product_ids: this.addForm.product_ids,
        variant_ids: this.addForm.variant_ids,
        product_deal_tag: this.addForm.product_deal_tag,
        product_offer: this.addForm.product_offer,
      });

      this.$toast.success("Offers added successfully!", {
                        timeout: 500
                    })
      this.addDialog = false;
      this.loadAll();
    } catch (err) {
      this.$toast.error("Failed to add offers.", {
                        timeout: 500
                    })
    } finally {
      this.saving = false;
    }
  },

    openEditDialog(item) {
      this.editForm = {
        product_offer_id: item.product_offer_id,
        product_id: this.findProductIdByVariant(item.mvariant_id),
        variant_id: item.mvariant_id,
        product_deal_tag: item.product_deal_tag,
        product_offer: item.product_offer,
      };
      this.loadEditVariants();
      this.editDialog = true;
    },
    loadEditVariants() {
  const selectedProduct = this.products.find(
    (p) => p.mproduct_id === this.editForm.product_id
  );

  if (!selectedProduct) {
    this.editVariants = [];
    return;
  }

  const usedVariantIds = this.productOffers
    .filter(o => o.product_offer_id !== this.editForm.product_offer_id)
    .map(o => o.mvariant_id);

  this.editVariants = selectedProduct.mvariants
    .filter(v => !usedVariantIds.includes(v.mvariant_id) || v.mvariant_id === this.editForm.variant_id)
    .map(variant => {
      // Parse option_value
      let optionVal = variant.option_value;
      if (typeof optionVal === 'string') {
        try {
          optionVal = JSON.parse(optionVal);
        } catch (e) {
          optionVal = {};
        }
      }

      const isEmpty = !optionVal || Object.keys(optionVal).length === 0;

      const option_label = isEmpty
        ? selectedProduct.mproduct_title
        : `${selectedProduct.mproduct_title} - ${Object.entries(optionVal)
            .map(([key, val]) => `${key}: ${val}`)
            .join(' / ')}`;

      return {
        ...variant,
        option_value: optionVal,
        option_label
      };
    });

  // Ensure selected variant is still available, else reset
  if (
    this.editForm.variant_id &&
    !this.editVariants.some(v => v.mvariant_id === this.editForm.variant_id)
  ) {
    this.editForm.variant_id = null;
  }
},
    resetEdit(close = true) {
      this.editForm = {
        product_offer_id: null,
        product_id: null,
        variant_id: null,
        product_deal_tag: "",
        product_offer: "",
      };
      this.editVariants = [];
      this.editValid = false;
      if (close) this.editDialog = false;
    },
    async saveEdit() {
      if (!this.editValid || this.saving) return;
      this.saving = true;
      try {
        await axios.post("/admin/product-offers/update", {
          product_offer_id: this.editForm.product_offer_id,
          mvariant_id: this.editForm.variant_id,
          product_deal_tag: this.editForm.product_deal_tag,
          product_offer: this.editForm.product_offer,
        });
        this.$toast?.success('Offer Updated successfully!', {
                        timeout: 500
                    })
        await this.loadAll();
        this.resetEdit();
      } finally {
        this.saving = false;
      }
    },

    findProductIdByVariant(variantId) {
      const prod = this.products.find((p) =>
        p.mvariants.some((v) => v.mvariant_id === variantId)
      );
      return prod ? prod.mproduct_id : null;
    },
    confirmDelete(item) {
      this.offerToDelete = item;
      this.deleteDialog = true;
      },
      async performDelete(id) {
        if (!this.offerToDelete) return;
        this.deleteLoading = true;

        try {
          await axios.post('/admin/product-offers/delete', {
            product_offer_id: this.offerToDelete.product_offer_id
          });

          this.$toast?.success('Offer deleted successfully!', {
                        timeout: 500
                    })
          this.loadAll();
        } catch (err) {
          this.$toast?.error('Failed to delete offer', {
                        timeout: 500
                    })
        } finally {
          this.deleteLoading = false;
          this.deleteDialog = false;
          this.offerToDelete = null;
        }
      },
      confirmBulkDelete() {
          this.bulkDeleteDialog = true;
      },
      async performBulkDelete() {
                if (!this.selectedtag.length) return;
                this.bulkDeleteLoading = true;

                try {
                    await axios.post('/admin/product-offers/bulk-delete', {
                    product_offer_ids: this.selectedtag.map((c) => c.product_offer_id),
                    });

                    this.$toast?.success('Selected product offers deleted!', {
                        timeout: 500
                    })
                    this.selectedtag = [];        
                    this.loadAll();  
                } catch (err) {
                    console.error(err);
                    this.$toast?.error('Failed to delete selected product offers.', {
                        timeout: 500
                    })
                } finally {
                    this.bulkDeleteLoading = false;
                    this.bulkDeleteDialog   = false;
                }
            },
      confirmBulkAdd() {
          this.bulkAddDialog = true;
      },
      async performBulkAdd() {
                if (!this.selectedtag.length) return;
                this.bulkAddLoading = true;

                try {
                    await axios.post('/admin/product-offers/bulk-add', {
                    product_offer_ids: this.selectedtag.map((c) => c.product_offer_id),
                    bulk_product_tag: this.bulk_product_tag,
                    });

                    this.$toast?.success('Selected product offers added!', {
                        timeout: 500
                    })
                    this.selectedtag = [];        
                    this.loadAll();  
                } catch (err) {
                    console.error(err);
                    this.$toast?.error('Failed to add selected product offers.', {
                        timeout: 500
                    })
                } finally {
                    this.bulkAddLoading = false;
                    this.bulkAddDialog   = false;
                    this.bulk_product_tag = null;
                }
            },
      confirmBulkRemove() {
          this.bulkRemoveDialog = true;
      },
      async performBulkRemove() {
                if (!this.selectedtag.length) return;
                this.bulkRemoveLoading = true;

                try {
                    await axios.post('/admin/product-offers/bulk-remove', {
                    product_offer_ids: this.selectedtag.map((c) => c.product_offer_id),
                    bulk_product_tag: this.bulk_product_tag,
                    });

                    this.$toast?.success('Selected product offers removed!', {
                        timeout: 500
                    })
                    this.selectedtag = [];        
                    this.loadAll();  
                } catch (err) {
                    console.error(err);
                    this.$toast?.error('Failed to remove selected product offers.', {
                        timeout: 500
                    })
                } finally {
                    this.bulkRemoveLoading = false;
                    this.bulkRemoveDialog   = false;
                }
            },

  },
};

</script>

<style scoped>
.v-input {
  font-size: 12px !important;
}
</style>
