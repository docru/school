import {vuexDelete, vuexGet, vuexPost} from "../../helpers/vuexHelper.js";

const state = {
    groups: [],
    group: null,
    teachers: [],
    disciples: [],
    attendances: {},
    load: false,
}
const getters = {
    getGroups: (state) => state.groups ?? [],
    getGroup: (state) => state.group ?? {},
    getTeachers: (state) => state.teachers ?? [],
    getDisciples: (state) => state.disciples ?? [],
    getAttendances: (state) => state.attendances ?? {},
    getLoad: (state) => state.load,
}
const mutations = {
    setGroups: (state, payload) => state.groups = payload,
    setGroup: (state, payload) => state.group = payload,
    setUsers: (state, payload) => {
        state.teachers = payload.teachers;
        state.disciples = payload.disciples;
    },
    setAttendances: (state, payload) => state.attendances = payload,
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

    // учителя и и ученики группы
    async actRequestGroupUsers({state, commit}, params) {
        return await vuexGet(`/administrator/group/${params.groupId}/users`, {}, state, commit, 'setUsers');
    },
    async actJoinUserToGroup({state, commit}, params) {
        let url = `/administrator/group/${params.groupId}/join-user/${params.userId}/${params.role}`;
        let config = {msgOk: 'Пользователь успешно добавлен в группу'};
        return await vuexPost(url, {}, state, commit, 'setUsers', config);
    },
    async actDelUserFromGroup({state, commit}, params) {
        let url = `/administrator/group/${params.groupId}/del-user/${params.userId}`;
        let config = {msgOk: 'Пользователь успешно удален из группы'};
        return await vuexPost(url, {}, state, commit, 'setUsers', config);
    },

    // учебные дни
    async actAddGroupSchoolDay({state, commit}, params) {
        let url = `/administrator/groups/school-day/${params.groupId}`;
        return await vuexPost(url, params, state, commit, 'setGroup');
    },

    // посещение
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
