import {vuexGet, vuexPost} from "../../helpers/vuexHelper.js";

const state = {
    groups: null,
    load: false
}
const getters = {
    getGroups: (state) =>state.groups,
    getLoad: (state) =>state.load,
}
const mutations = {
    setGroups(state, payload){
        state.groups = payload
    }
}


const actions = {
    async ACT_GET_Groups({state, commit}) {
        return await vuexGet('/groups', {}, state, commit, 'setGroups');
    }
}


export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters
}
