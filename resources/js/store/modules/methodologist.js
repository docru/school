import {calcHash, vuexDelete, vuexGet, vuexPost, vuexPut} from "../../helpers/vuexHelper.js";


function listParams(state) {
    return {
        course: state.course,
        studyProgram: state.studyProgram,
        schedule: state.schedule,
    };
}

function hashParams(state) {
    return calcHash(listParams(state));
}

const state = {
    load: false,
    courses: null,
    course: null,
    studyProgram: null,
    schedule: null,
    hash: null,
}
const getters = {
    getLoad: (state) => state.load,
    getCourses: (state) => state.courses ?? [],
    getCourse: (state) => state.course ?? {},
    getLessons: (state) => {
        let lessons = {};
        for (const m in state.studyProgram) {
            let module = state.studyProgram[m];
            for (const l in module.lessons) {
                let lesson = module.lessons[l];
                lessons[lesson.id] = lesson;
            }
        }
        return lessons;
    },
    getStudyProgram: (state) => state.studyProgram ?? [],
    getSchedule: (state) => state.schedule ?? [],
    isSaved: (state) => state.hash === hashParams(state),
    getHashParams: (state) => hashParams(state),
}
const mutations = {
    setCourses: (state, data) => state.courses = data,
    setCourse: (state, data) => {
        state.course = data.course;
        state.studyProgram = data.studyProgram;
        state.schedule = data.schedule;
        state.hash = hashParams(state);
    },
    setStudyProgram: (state, data) => state.studyProgram = data,
    setLesson: (state, data) => {
        for (const m in state.studyProgram) {
            let module = state.studyProgram[m];
            for (const l in module.lessons) {
                let lesson = module.lessons[l];
                if (data.id === lesson.id) {
                    state.studyProgram[m][l] = lesson;
                    return true;
                }
            }
        }
        return false;
    },
}


const actions = {
    async actReqwestCourses({state, commit}, params = {}) {
        await vuexGet('/methodologist/courses', params, state, commit, 'setCourses');
    },
    async actReqwestCourse({state, commit}, params = {}) {
        await vuexGet('/methodologist/courses/' + params.id, params, state, commit, 'setCourse');
    },
    async actCreateCourse({state, commit}, params) {
        return await vuexPost('/methodologist/courses', params, state, commit, 'setCourses', {msgOk: 'Курс создан'});
    },
    async actCreateModule({state, commit}, params) {
        return await vuexPost('/methodologist/modules', params, state, commit, 'setCourse', {msgOk: 'Модуль создан'});
    },
    async actDeleteModule({state, commit}, params) {
        return await vuexDelete('/methodologist/modules/' + params.id, params, state, commit, 'setCourse', {msgOk: `Модуль "${params.name}" удален`});
    },
    async actCreateLesson({state, commit}, params) {
        return await vuexPost('/methodologist/lessons', params, state, commit, 'setCourse', {msgOk: 'Урок создан'});
    },
    async actDeleteLesson({state, commit}, params) {
        return await vuexDelete('/methodologist/lessons/' + params.id, params, state, commit, 'setCourse', {msgOk: `Урок "${params.name}" удален`});
    },
    async actSaveCourse({state, commit}) {
        if (state.hash !== hashParams(state)) {
            return await vuexPut('/methodologist/courses/' + state.course.id, listParams(state), state, commit, 'setCourse', {msgOk: 'Курс сохранен'});
        }
    },
    async actCreateCourseSchoolDay({state, commit}, params) {
        return await vuexPost('/methodologist/course_school_day', params, state, commit, 'setCourse', {msgOk: 'Учебный день создан'});
    },
    async actDeleteCourseSchoolDay({state, commit}, params) {
        return await vuexDelete('/methodologist/course_school_day/' + params.id, params, state, commit, 'setCourse', {msgOk: 'Учебный день удален'});
    },
}


export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters
}
