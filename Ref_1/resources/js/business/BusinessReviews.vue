<template>
  <div class="pa-2">
    <h4 class="ms-4">Reviews to Business</h4>

    <!-- 1) Loading spinner -->
    <div v-if="loading" class="text-center my-4">
      <v-progress-circular indeterminate />
    </div>

    <!-- 2) Show reviews when we have them -->
    <div v-else-if="reviews.length" class="row">
      <div
        v-for="(r, idx) in reviews"
        :key="r.reviewer_id || idx"
        class="col-12 col-md-6 mb-4"
      >
        <v-card>
          <v-card-text>
            <v-row align="center">
              <v-col cols="9">
                <span class="h5">
                  {{ r.name }}
                  <v-rating
                    :value="r.rating"
                    color="amber"
                    dense
                    readonly
                    half-increments
                    size="20"
                  />
                </span>
              </v-col>
              <v-col cols="3">
                <span class="small text-muted">
                  {{ r.added_date | fdate }}
                </span>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12">
                <p class="mb-0">{{ r.review_text }}</p>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </div>
    </div>

    <!-- 3) Empty state -->
    <div v-else class="text-center my-4">
      <p>No data found.</p>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'BusinessReviews',

  data() {
    return {
      reviews: [],
      loading: false,
    };
  },

  filters: {
    fdate(val) {
      if (!val) return '';
      const [year, month, day] = val.split('-');
      return `${day}-${month}-${year}`;
    },
  },

  mounted() {
    this.fetchReviews();
  },

  methods: {
    async fetchReviews() {
      console.log('🔥 fetchReviews() called');      // <— confirm this fires in the console
      this.loading = true;

      // grab JWT
      const token = localStorage.getItem('token');
      if (!token) {
        console.error('No auth token found');
        this.loading = false;
        return;
      }

      try {
        // ABSOLUTE path: always hits https://bizlx.com/api/businesses/reviews/get
        const resp = await axios.get('/api/businesses/reviews/get', {
          headers: { Authorization: `Bearer ${token}` }
        });

        this.reviews = resp.data.reviews || [];
      } catch (err) {
        console.error('Failed to load reviews:', err.response || err);
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
.mb-4 { margin-bottom: 1rem; }
.my-4 { margin: 1rem 0; }
</style>
