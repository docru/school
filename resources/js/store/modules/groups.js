import {vuexDelete, vuexGet, vuexPost} from "../../helpers/vuexHelper.js";

const state = {
    groups: [],
    group: null,
    disciples: [],
    // groupSchoolDays: [],
    load: false,
}
const getters = {
    getGroups: (state) => state.groups ?? [],
    getGroup: (state) => state.group ?? {},
    getDisciples: (state) => state.disciples ?? [],
    // getGroupSchoolDays: (state) => state.groupSchoolDays ?? [],
    getLoad: (state) => state.load,
}
const mutations = {
    setGroups: (state, payload) => state.groups = payload,
    setGroup: (state, payload) => state.group = payload,
    setDisciples: (state, payload) => state.disciples = payload,
    // setGroupSchoolDays: (state, payload) => state.groupSchoolDays = payload,
}


const actions = {
    async actRequestGroups({state, commit}) {
        return await vuexGet('/administrator/groups', {}, state, commit, 'setGroups');
    },
    async actCreateGroup({state, commit}, params) {
        return await vuexPost('/administrator/groups', params, state, commit, 'setGroups');
    },
    async actDeleteGroup({state, commit}, params) {
        console.log(params)
        return await vuexDelete('/administrator/groups/' + params, params, state, commit, 'setGroups');
    },
    async actRequestGroup({state, commit}, groupId) {
        return await vuexGet('/administrator/groups/' + groupId, {}, state, commit, 'setGroup');
    },
    async actRequestDisciples({state, commit}, groupId) {
        return await vuexGet('/administrator/groups/users/' + groupId, {}, state, commit, 'setDisciples');
    },
    async actAddGroupSchoolDay({state, commit}, params) {
        let url = `/administrator/groups/school-day/${params.groupId}`;
        return await vuexPost(url, params, state, commit, 'setGroup');
    },
    async actJoinUserToGroup({state, commit}, params) {
        return await vuexPost('/administrator/groups/join-user', params, state, commit, 'setDisciples');
    },
}


export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters
}
