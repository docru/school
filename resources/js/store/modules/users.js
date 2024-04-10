import {vuexGet} from "../../helpers/vuexHelper.js";
const state = {
    users: null,
}
const getters = {
    getUsers: (state) => state.users,
}
const mutations = {
    setUsers:(state,payload)=>{ state.users = payload}
}


const actions = {
    async GET_User(){
        return await vuexGet('/users/'+params.account, params, state, commit, 'setUsers', {showMsg: false});
    }

}


export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters
}