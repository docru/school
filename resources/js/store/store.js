import {createStore} from 'vuex';
import app from './modules/app.js';
import users from './modules/users.js';
import courses from './modules/courses.js';


export const store = createStore({
    modules: {
        app,
        users,
        courses,
    }
})

