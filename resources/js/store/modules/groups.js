import {vuexGet, vuexPost} from "../../helpers/vuexHelper.js";

const state = {
    groups: null,
    group: null,
    load: false
}
const getters = {
    getGroups: (state) =>state.groups,
    getGroup: (state) =>state.group,
    getLoad: (state) =>state.load,
}
const mutations = {
    setGroups(state, payload){
        state.groups = payload
    },
    setGroup(state, payload){
        state.group = payload[0]
    }
}


const actions = {
    async ACT_GET_Groups({state, commit}) {
        return await vuexGet('/groups', {}, state, commit, 'setGroups');
    },
    async actCreateGroup({state, commit},params) {
        return await vuexPost('/groups/add', params, state, commit, 'setGroups');
    },
    async actGetGroup({state, commit},params) {
        return await vuexGet('/groups/'+params, {}, state, commit, 'setGroup');
    },
}


export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters
}
