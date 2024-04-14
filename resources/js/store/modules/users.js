import {vuexGet} from "../../helpers/vuexHelper.js";
const state = {
    users: null,
    roles: null,
}
const getters = {
    getUsers: (state) => state.users,
    getRoles: (state) => state.roles,
}
const mutations = {
    setUsers:(state,payload)=>{ state.users = payload},
    setRoles:(state,payload)=>{
        state.roles = payload
            .map((el)=>{return {...el, value:false}})
    }
}


const actions = {
    async ACT_GET_User({state, commit}){
        return await vuexGet('/api/users', {}, state, commit, 'setUsers', {showMsg: false});
    },
    async ACT_GET_UserRoles({state, commit}){
        return await vuexGet('/api/roles', {}, state, commit, 'setRoles', {showMsg: false});
    },

}


export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters
}