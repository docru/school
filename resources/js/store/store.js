import {createStore} from 'vuex';
import app from './modules/app.js';
import users from './modules/users.js';
import courses from './modules/courses.js';
import profile from './modules/profile.js';
import groups from './modules/groups.js';


export const store = createStore({
    modules: {
        groups,
        profile,
        app,
        users,
        courses,
    }
})

