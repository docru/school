import {vuexGet, vuexPost} from "../../helpers/vuexHelper.js";

const state = {
    users: null,
    roles: null,
    rolesSelect: null,
    load: false
}
const getters = {
    getUsers: (state) => state.users,
    getRoles: (state) => state.roles,
    getRolesSelect: (state) => state.rolesSelect,
    getLoad: (state) => !!state.load
}
const mutations = {
    setUsersLink(state, payload){
        state.users.filter(el=>el.id === payload.id)[0].entry_code = payload.key
    },
    setUsers: (state, payload) => state.users = payload.users,
    setRoles: (state, payload) => {
        state.roles = payload.roles
            .map((el) => {
                return {...el, value: false}
            })
        state.rolesSelect = JSON.parse(JSON.stringify(state.roles))
    }
}


const actions = {
    async actReqwestUsers({state, commit}, params) {
        let url = '/users';
        if(params.role){
            url += '/' + params.role;
        }
        return await vuexGet(url, {}, state, commit, 'setUsers');
    },
    async actReqwestUserRoles({state, commit}) {
        return await vuexGet('/users/roles', {}, state, commit, 'setRoles');
    },
    async actUserCreate({state, commit}, params) {
        return await vuexPost('/users/create', params, state, commit, 'setUsers', {showMsg: false});
    },
    async actUserCreateLink({state, commit}, params) {
        return await vuexPost('/users/auth-link/'+params.uid, {}, state, commit, 'setUsersLink', {showMsg: false});
    },
}


export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters
}
