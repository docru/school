import {vuexDelete, vuexGet, vuexPost} from "../../helpers/vuexHelper.js";

const state = {
    groups: [],
    group: null,
    courses: null,
    course: null,
    teachers: [],
    disciples: [],
    attendances: {},
    attendancesAll: {},
    load: false,
}
const getters = {
    getGroups: (state) => state.groups ?? [],
    getGroup: (state) => state.group ?? {},
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
    getTeachers: (state) => state.teachers ?? [],
    getDisciples: (state) => state.disciples ?? [],
    getAttendances: (state) => state.attendances ?? {},
    getAttendancesAll: (state) => state.attendancesAll ?? {},
    getLoad: (state) => state.load,
}
const mutations = {
    setGroups: (state, payload) => state.groups = payload,
    setGroup: (state, payload) => state.group = payload,
    setCourses: (state, data) => state.courses = data,
    setCourse: (state, data) => {
        state.course = data.course;
        state.studyProgram = data.studyProgram;
        state.schedule = data.schedule;
    },
    setUsers: (state, payload) => {
        state.teachers = payload.teachers;
        state.disciples = payload.disciples;
    },
    setAttendances: (state, payload) => state.attendances = payload,
    setAttendancesAll: (state, payload) => state.attendancesAll = payload,
}


const actions = {
    // группа
    async actRequestGroups({state, commit}) {
        return await vuexGet('/administrator/groups', {}, state, commit, 'setGroups');
    },
    async actCreateGroup({state, commit}, params) {
        return await vuexPost('/administrator/groups', params, state, commit, 'setGroups');
    },
    async actDeleteGroup({state, commit}, params) {
        return await vuexDelete('/administrator/groups/' + params, params, state, commit, 'setGroups');
    },
    async actRequestGroup({state, commit}, groupId) {
        return await vuexGet('/administrator/groups/' + groupId, {}, state, commit, 'setGroup');
    },

    // курсы
    async actReqwestCourses({state, commit}, params = {}) {
        await vuexGet('/administrator/courses', params, state, commit, 'setCourses');
    },
    async actReqwestCourse({state, commit}, params = {}) {
        await vuexGet('/administrator/courses/' + params.id, params, state, commit, 'setCourse');
    },

    // учителя и и ученики группы
    async actRequestGroupUsers({state, commit}, params) {
        return await vuexGet(`/administrator/group/${params.groupId}/users`, {}, state, commit, 'setUsers');
    },
    async actJoinUserToGroup({state, commit}, params) {
        let url = `/administrator/group/${params.groupId}/join-user/${params.userId}/${params.role}`;
        let config = {msgOk: 'Пользователь успешно добавлен в группу'};
        return await vuexPost(url, {}, state, commit, 'setUsers', config);
    },
    async actRemoveUserFromGroup({state, commit}, params) {
        let url = `/administrator/group/${params.groupId}/remove-user/${params.userId}`;
        let config = {msgOk: 'Пользователь успешно удален из группы'};
        return await vuexPost(url, {}, state, commit, 'setUsers', config);
    },
    async actRestoreUserToGroup({state, commit}, params) {
        let url = `/administrator/group/${params.groupId}/restore-user/${params.userId}`;
        let config = {msgOk: 'Пользователь успешно восстановлен в группу'};
        return await vuexPost(url, {}, state, commit, 'setUsers', config);
    },

    // учебные дни
    async actAddGroupSchoolDay({state, commit}, params) {
        let url = `/administrator/groups/school-day/${params.groupId}/add`;
        return await vuexPost(url, params, state, commit, 'setGroup');
    },
    async actCloseGroupSchoolDay({state, commit}, params) {
        let url = `/administrator/groups/school-day/${params.groupId}/close`;
        return await vuexPost(url, params, state, commit, 'setGroup');
    },

    // посещение
    async actRequestAttendancesAll({state, commit}, params) {
        return await vuexGet(`/administrator/attendances`, {}, state, commit, 'setAttendancesAll');
    },
    async actRequestAttendances({state, commit}, params) {
        return await vuexGet(`/administrator/attendance/${params.groupId}`, {}, state, commit, 'setAttendances');
    },
    async actSetAttendance({state, commit}, params) {
        return await vuexPost(`/administrator/attendance/${params.groupSchoolDayId}/set/${params.userId}`, {}, state, commit, 'setAttendances');
    },
}


export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters
}
