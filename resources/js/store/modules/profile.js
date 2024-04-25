import {vuexGet, vuexPost} from "../../helpers/vuexHelper.js";

const state = {
    profile: {},
    load:true
}
const getters = {
    getProfile: (state) => state.profile,
    getLoad: (state) => state.load,

}
const mutations = {
    setProfile: (state, data) => state.profile = data,
}

const actions = {
    async actResetProfile({state, commit}, params = {}) {
        let res = await vuexGet('/users/profile', params, state, commit, 'setProfile');
        state.load = false;
        return res;
    },
    async actSaveProfile({state, commit}, params = {}) {
        return await vuexPost('/users/profile', state.profile, state, commit, 'setProfile', {showMsg: false});
    },
}


export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters
}
