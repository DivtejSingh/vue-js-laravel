<template>
    <div>
        <h2>Bizlx.com™ - Daily Market Update, Hot Deals, Shopping, Sales homepage</h2>
        <v-card class="mx-auto text-center" color="green" dark max-width="">
            <v-card-text>
                <v-sheet color="rgba(0, 0, 0, .12)">
                    <v-sparkline :value="value" :gradient="gradient" :smooth="radius || false" :padding="padding"
                                 :line-width="width" :stroke-linecap="lineCap" :gradient-direction="gradientDirection" :fill="fill"
                                 :type="type" :auto-line-width="autoLineWidth" auto-draw>
                    </v-sparkline>
                </v-sheet>
            </v-card-text>

            <v-card-text>
                <div class="text-h4 font-weight-thin">Sales Last 24h</div>
            </v-card-text>
            <v-divider></v-divider>

            <v-card-actions class="justify-center">
                <v-btn block text>Go to Report</v-btn>
            </v-card-actions>
        </v-card>
    </div>
</template>
<script>
const gradients = [
    ['#222'],
    ['#42b3f4'],
    ['red', 'orange', 'yellow'],
    ['purple', 'violet'],
    ['#00c6ff', '#F0F', '#FF0'],
    ['#f72047', '#ffd200', '#1feaea'],
]
export default {
    name:'AdminDashboard',
    data(){
        return{
            width: 2,
            radius: 10,
            padding: 8,
            lineCap: 'round',
            gradient: gradients[5],
            value: [0, 2, 5, 9, 5, 10, 3, 5, 0, 0, 1, 8, 2, 9, 0],
            gradientDirection: 'top',
            gradients,
            fill: false,
            type: 'trend',
            autoLineWidth: false,
        }
    }
}

</script>

<style scoped>

</style>
