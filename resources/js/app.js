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


// Темы Vuetify на токенах дизайн-системы Blueprint (см. resources/css/tokens.css).
// Именованные цвета red/green/blue/grey/teal оставлены для совместимости с color="..." в компонентах.
const adfLight = {
    dark: false,
    colors: {
        background: '#fbf6f1',          // surface-page
        surface: '#ffffff',             // surface-card
        'surface-variant': '#f6eee6',   // surface-muted
        'on-surface-variant': '#1f1d1a',
        'on-surface': '#1f1d1a',        // text-primary
        'on-background': '#1f1d1a',
        primary: '#8b5e3c',             // accent (walnut)
        'on-primary': '#ffffff',
        secondary: '#6b7280',           // neutral-500
        ink: '#1f1d1a',                 // surface-inverse
        'on-ink': '#ffffff',
        muted: '#6b5e52',               // text-muted
        border: '#efe5db',
        success: '#22c55e',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#3b82f6',
        red: '#ef4444',
        green: '#22c55e',
        blue: '#8b5e3c',
        teal: '#8b5e3c',
        grey: '#6b7280',
    },
    variables: {
        'border-color': '#efe5db',
        'border-opacity': 1,
        'medium-emphasis-opacity': 1,
    },
}

const adfDark = {
    dark: true,
    colors: {
        background: '#171411',
        surface: '#211d19',
        'surface-variant': '#2a2520',
        'on-surface-variant': '#f3ece3',
        'on-surface': '#f3ece3',
        'on-background': '#f3ece3',
        primary: '#c4936a',
        'on-primary': '#1f1d1a',
        secondary: '#6b7280',
        ink: '#f3ece3',
        'on-ink': '#1f1d1a',
        muted: '#b5a99c',
        border: '#3a332c',
        success: '#22c55e',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#3b82f6',
        red: '#ef4444',
        green: '#22c55e',
        blue: '#c4936a',
        teal: '#c4936a',
        grey: '#6b7280',
    },
    variables: {
        'border-color': '#3a332c',
        'border-opacity': 1,
        'medium-emphasis-opacity': 1,
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
        VSelect:{
            variant:'outlined',
            density:'compact',
            hideDetails:true,
            class:'tw-my-2'
        },
        VDivider:{
            class:'tw-my-2',
        },
        VTabs:{
            color:'primary',
        },
        VChip:{
            variant:'tonal',
        },
        VCard:{
            variant:'flat',
            VBtn: {
                variant:'flat',
                color:'primary'
            },
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
// this.$confirm({title, text, okText, cancelText, color}) -> Promise<boolean>; вместо системного confirm()
app.config.globalProperties.$confirm = (options) => store.dispatch('app/actConfirm', options);
app.mount('#appVue');
