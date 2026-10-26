import {vuexDelete, vuexGet, vuexPost} from "../../helpers/vuexHelper.js";

const state = {
    groups: [],
    group: null,
    lesson: null,
    load: false,
}

const getters = {
    getGroups: (state) => state.groups ?? [],
    getGroup: (state) => state.group ?? {},
    getGroupSchoolDay: (state) => state.group?.groupsSchoolDay ?? {},
    getCourseSchoolDay: (state) => state.group?.courseSchoolDay ?? [],
    getLesson: (state) => state.lesson,
    getDisciples: (state) => state.group?.disciples ?? [],
    getTeachers: (state) => state.group?.teachers ?? [],
    getAttendances: (state) => state.group?.attendances ?? [],
    getLoad: (state) => state.load,
}

const mutations = {
    setGroups: (state, payload) => state.groups = payload,
    setGroup: (state, payload) => state.group = payload,
    setLesson: (state, payload) => state.lesson = payload,
}

const actions = {
    // группа
    async actRequestGroups({state, commit}) {
        return await vuexGet('/disciple/groups', {}, state, commit, 'setGroups');
    },
    async actRequestGroup({state, commit}, groupId) {
        return await vuexGet('/disciple/groups/' + groupId, {}, state, commit, 'setGroup');
    },
    async actRequestLesson({state, commit}, params) {
        // Чтение урока идёт POST-запросом, а vuexPost по умолчанию рапортует «Сохранено»
        return await vuexPost(`/disciple/lesson/${params.groupId}/${params.lessonId}`, {}, state, commit, 'setLesson', {showMsg: false});
    },
}


export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters
}
