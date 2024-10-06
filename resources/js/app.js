import {createApp} from 'vue/dist/vue.esm-bundler';
import App from "@/App.vue";
import router from "@/router/index.js";
import {store} from "@/store/store.js";

import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'

import {createVuetify} from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import 'md-editor-v3/lib/preview.css';

import VMdEditor from '@kangc/v-md-editor';
import '@kangc/v-md-editor/lib/style/base-editor.css';
import vuepressTheme from '@kangc/v-md-editor/lib/theme/vuepress.js';
import '@kangc/v-md-editor/lib/theme/style/vuepress.css';
import ruRU from '@kangc/v-md-editor/lib/lang/ru-RU';
// Prism
import Prism from 'prismjs';
// highlight code
import 'prismjs/components/prism-json';

VMdEditor.use(vuepressTheme, {
    Prism,
});
VMdEditor.lang.use('ru-RU', ruRU);


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

String.prototype.hashCode = function () {
    let hash = 0,
        i, chr;
    if (this.length === 0) return hash;
    for (i = 0; i < this.length; i++) {
        chr = this.charCodeAt(i);
        hash = ((hash << 5) - hash) + chr;
        hash |= 0; // Convert to 32bit integer
    }
    return hash;
}


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
        VAutocomplete:{
            variant:'outlined',
            hideDetails:true,
            class:'tw-my-2'
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
app.use(VMdEditor);
app.mount('#appVue');
