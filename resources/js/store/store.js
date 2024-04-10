
import { createStore } from 'vuex';
import app from './modules/app.js';
import users from './modules/users.js';



export const store = createStore({
    modules: {
        app,
        users
    }
})

