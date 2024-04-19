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



const vuetify = createVuetify({
    components,
    defaults: {
        VTextField:{
            density:'compact',
            hideDetails:true,
            variant:'outlined',
            class:'tw-my-2'
        },
        VBtn:{
            variant:'flat',
            ripple:true,

        },
        VDivider:{
            class:'tw-my-2',
            color:'blue'
        },
        VTabs:{
            background:'red',
            color:'grey',
            VBtn:{
                color:'white'
            }
        },
        VDataTable:{
            density: "comfortable",
            class:"adf-table tw-p-2",
            noDataText:'нет данных',
            itemsPerPageOptions:[
                { title: "10", value: 10 },
                { title: "50", value: 50 },
                { title: "100", value: 100 },
                { title: "Все", value: -1 }
            ],
            itemsPerPageText:"Кол-во на странице: ",
            loadingText:"Подождите. Идет загрузка данных.",
            pageText:"{0} из {1}"
        },
        VTextarea:{
            variant:'outlined',
            hideDetails:true,
            class:'tw-my-2'
        },
        VCard:{
            VBtn: {
                variant:'flat',
                color:'primary'
            },
        }
    },
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