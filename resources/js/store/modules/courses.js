import {vuexDelete, vuexGet, vuexPost, vuexPut} from "../../helpers/vuexHelper.js";

const state = {
    load: false,
    courses: null,
    course: null,
    studyProgram: null,
}
const getters = {
    getLoad: (state) => state.load,
    getCourses: (state) => state.courses ?? [],
    getCourse: (state) => state.course,
    getStudyProgram: (state) => state.studyProgram ?? [],
}
const mutations = {
    setCourses: (state, data) => state.courses = data,
    setCourse: (state, data) => {
        state.course = data.course;
        state.studyProgram = data.studyProgram;
    },
    setStudyProgram: (state, data) => state.studyProgram = data,
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
    async actCreateModule({state, commit}, params) {
        return await vuexPost('/methodologist/modules', params, state, commit, 'setStudyProgram', {msgOk: 'Модуль создан'});
    },
    async actDeleteModule({state, commit}, params) {
        return await vuexDelete('/methodologist/modules/' + params.id, params, state, commit, 'setStudyProgram', {msgOk: `Модуль "${params.name}" удален`});
    },
    async actCreateLesson({state, commit}, params) {
        return await vuexPost('/methodologist/lessons', params, state, commit, 'setStudyProgram', {msgOk: 'Урок создан'});
    },
    async actDeleteLesson({state, commit}, params) {
        return await vuexDelete('/methodologist/lessons/' + params.id, params, state, commit, 'setStudyProgram', {msgOk: `Урок "${params.name}" удален`});
    },
    async actSaveCourse({state, commit}, params) {
        params = {
            course: state.course,
            studyProgram: state.studyProgram.map((el,ind)=>{
                el.module.order = ind
              el.lessons = el.lessons.map((lesson,index)=>{
                console.log({...lesson,order:index})
                return {...lesson,order:index}
              })
              return el
            })
        };
        return await vuexPut('/methodologist/courses/' + state.course.id, params, state, commit, 'setStudyProgram', {msgOk: 'Курс сохранен'});
    },
}


export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters
}
