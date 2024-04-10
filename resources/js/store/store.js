
import { createStore } from 'vuex';
import app from './modules/app.js';



export const store = createStore({
    modules: {
        app
    }
})

