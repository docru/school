import {vuexGet, vuexPost} from "../../helpers/vuexHelper.js";
const state = {
    users: null,
    roles: null,
}
const getters = {
    getUsers: (state) => state.users,
    getRoles: (state) => state.roles,
}
const mutations = {
    addUser:(state,payload)=>{ },
    setUsers:(state,payload)=>{ state.users = payload.users},
    setRoles:(state,payload)=>{
        state.roles = payload.roles
            .map((el)=>{return {...el, value:false}})
    }
}


const actions = {
    async ACT_GET_User({state, commit}){
        return await vuexGet('/users', {}, state, commit, 'setUsers', {showMsg: false});
    },
    async ACT_GET_UserRoles({state, commit}){
        return await vuexGet('/users/roles', {}, state, commit, 'setRoles', {showMsg: false});
    },
    async ACT_GET_UserCreate({state, commit},params){
        return await vuexPost('/users/create', params, state, commit, 'addUser', {showMsg: false});
    },

}


export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters
}