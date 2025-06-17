import Vue from 'vue'
import Vuetify from "vuetify";


Vue.use(Vuetify)

export default new Vuetify({
    theme:{
        themes:{
            light:{
                primary: '#1976d2',
                secondary: '#696969',
                accent: '#8c9eff',
                error: '#fd0606',
                red: '#fd0606',
                conblue: '#0165a3',
            }
        }
    }
})
