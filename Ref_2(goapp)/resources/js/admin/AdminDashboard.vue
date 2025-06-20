<template>
  <v-container fluid>
    <!-- Top Summary Cards -->
    <v-row dense>
      <v-col cols="12" md="4" v-for="(item, index) in summaryCards" :key="index">
        <v-card class="pa-4" elevation="3">
          <v-row align="center">
            <v-col cols="3">
              <v-icon size="36" color="primary">{{ item.icon }}</v-icon>
            </v-col>
            <v-col>
              <div class="text-subtitle-1">{{ item.label }}</div>
              <div class="text-h5 font-weight-bold">{{ item.count }}</div>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
    </v-row>

    <!-- Static Line Graph -->
    <v-row>
      <v-col cols="12">
        <v-card class="pa-4 mt-4" elevation="3">
          <h3 class="text-h6 mb-4">Line graph</h3>

          <!-- Line Graph Container -->
          <div class="static-graph">
            <div class="graph-line dataset1">
              <div v-for="(value, index) in dataset1" :key="'d1-' + index" class="point"
                   :style="getStyle(value, index)">
              </div>
            </div>
            <div class="graph-line dataset2">
              <div v-for="(value, index) in dataset2" :key="'d2-' + index" class="point"
                   :style="getStyle(value, index)">
              </div>
            </div>
          </div>

          <!-- Labels -->
          <div class="d-flex justify-space-between mt-2 text-caption px-2">
            <div v-for="(month, i) in months" :key="i" style="width: 8%; text-align: center">
              {{ month }}
            </div>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  name: "StaticLineGraph",
  data() {
    return {
      months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      dataset1: [90, 85, 100, 95, 160, 120, 60, 90, 150, 170, 130, 100],
      dataset2: [90, 90, 90, 85, 80, 70, 40, 80, 60, 130, 90, 60],
      maxValue: 180,
      summaryCards: [
        { label: "Total Users", count: 250, icon: "mdi-account" },
        { label: "Orders", count: 140, icon: "mdi-cart" },
        { label: "Revenue", count: "£ 1.2L", icon: "mdi-currency-gbp" }
      ]
    }
  },
  mounted() {
    this.fetchCounts()
    this.getStyle()
  },
  methods: {
    getStyle(value, index) {
      const left = `${index * 8.5}%`
      const bottom = `${(value / this.maxValue) * 100}%`
      return {
        left,
        bottom
      }
    },
    fetchCounts() {
      // Replace this with real API calls
      setTimeout(() => {
        this.summaryCards[0].count = 154
        this.summaryCards[1].count = 89
        this.summaryCards[2].count = "£ 12,340"
      }, 500)
    },
  }
}
</script>

<style scoped>
.static-graph {
  position: relative;
  height: 100px;
  width: 100%;
  border-left: 1px solid #ccc;
  border-bottom: 1px solid #ccc;
}

.graph-line {
  position: absolute;
  height: 100%;
  width: 100%;
}

.point {
  position: absolute;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  transform: translate(-50%, 50%);
}

.dataset1 .point {
  background-color: #e67e22;
}

.dataset2 .point {
  background-color: #27ae60;
}

/* Optional: Add lines between dots using pseudo-elements */
</style>