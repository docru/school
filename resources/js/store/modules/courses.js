import {vuexGet, vuexPost} from "../../helpers/vuexHelper.js";

const state = {
    courses: {},
    course: {},
}
const getters = {
    getCourses: (state) => state.courses,
    getCourse: (state) => state.course,
}
const mutations = {
    setCourses: (state, data) => state.courses = data,
    setCourse: (state, data) => state.courses = data,
}


const actions = {
    async actReqwestCourses({state, commit}, params = {}) {
        await vuexGet('/methodologist/courses', params, state, commit, 'setCourses', {showMsg: false});
    },
    async actReqwestCourse({state, commit}, params = {}) {
        await vuexGet('/methodologist/courses/' + params.id, params, state, commit, 'setCourse', {showMsg: false});
    },
    async actCreateCourse({state, commit}, params) {
        return await vuexPost('/methodologist/courses', params, state, commit, 'setCourses', {msgOk: 'Курс создан'});
    },
}


export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters
}
