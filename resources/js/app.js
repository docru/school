import {createApp} from 'vue/dist/vue.esm-bundler';
import App from "@/App.vue";
import router from "@/router/index.js";
import {store} from "@/store/store.js";

import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'

import {createVuetify} from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

const adfLight = {
    dark: false,
    colors: {
        background: '#F9FBFF',
        surface: '#FFF',
    },
}

const adfDark = {
    dark: true,
    colors: {
        background: '#1A1C21',
        surface: '#252830',
    },
}
// <v-text-field density="compact" variant="outlined" hide-details v-model="phone"></v-text-field>

const defParams = {
    density:'compact',
    hideDetails:true,
    variant:'outlined'
}

const vuetify = createVuetify({
    defaults: {
        VTextField:defParams,
        VBtn:{
            variant:'flat',
            ripple:true,

        },
        VDataTable:{
            density: "comfortable",
            class:"adf-table"
        },
        VTextarea:{
            variant:'outlined',
            hideDetails:true,
        },
        VCard:{
            VBtn: {
                variant:'flat',
                color:'primary'
            },
        }
    },
    components,
    directives,
    display: {
        mobileBreakpoint: 'sm',
        thresholds: {
            sm: 0,  // tailwind default
            md: 768, // tailwind md
            xl: 1280, // tailwind xl
        },
    },
    theme: {
        defaultTheme: 'adfLight',
        themes: {
            adfLight,
            adfDark,
        },
    }
})

const app = createApp(App);
app.use(vuetify);
app.use(router)
app.use(store)
app.mount('#appVue');