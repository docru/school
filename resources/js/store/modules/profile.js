import {vuexGet, vuexPost} from "../../helpers/vuexHelper.js";

const state = {
    profile: null,
    load:false
}
const getters = {
    getProfile: (state) => state.profile,
    getLoad: (state) => state.load,

}
const mutations = {
    setProfile: (state, data) => state.profile = data
}


const actions = {
    async ACT_GET_Profile({state, commit}, params = {}) {
        await vuexGet('/users/profile', params, state, commit, 'setProfile', {showMsg: false});
    }
}


export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters
}
