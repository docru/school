import {createStore} from 'vuex';
import app from './modules/app.js';
import users from './modules/users.js';
import profile from './modules/profile.js';
import methodologist from './modules/methodologist.js';
import administrator from './modules/administrator.js';
import teacher from './modules/teacher.js';
import disciple from './modules/disciple.js';


export const store = createStore({
    modules: {
        profile,
        app,
        users,
        methodologist,
        administrator,
        teacher,
        disciple,
    }
})

