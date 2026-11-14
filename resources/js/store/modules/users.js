import {vuexGet, vuexPost} from "../../helpers/vuexHelper.js";

const state = {
    home: {},
    users: null,
    leaders: null,
    roles: null,
    rolesSelect: null,
    load: false
}
const getters = {
    getHome: (state) => state.home,
    getUsers: (state) => state.users,
    getLeaders: (state) => state.leaders ?? [],
    getRoles: (state) => state.roles,
    getRolesSelect: (state) => state.rolesSelect,
    getLoad: (state) => !!state.load
}
const mutations = {
    deleteUser: (state, payload) => {
        state.users = state.users.filter((el)=>el.id !== payload)
    },
    editUser: (state, payload) => {
        state.users = state.users.map((el)=>{
            if(el.id === payload.id) return payload
            return el
        })
    },
    setHome: (state, payload) => state.home = payload,
    setUsersLink(state, payload) {
        state.users.filter(el => el.id === payload.id)[0].entry_code = payload.key
    },
    setUsers: (state, payload) => state.users = payload.users,
    setLeaders: (state, payload) => state.leaders = payload.users,
    setRoles: (state, payload) => {
        state.roles = payload.map((el) => {
            return {...el, value: false}
        });
        state.rolesSelect = JSON.parse(JSON.stringify(state.roles))
    }
}


const actions = {
    async actReqwestHome({state, commit}) {
        return await vuexGet('/home', {}, state, commit, 'setHome');
    },
    async actReqwestUsers({state, commit}, params = {}) {
        let url = '/users';
        if (params?.role) {
            url += '/' + params.role;
        }
        return await vuexGet(url, {}, state, commit, 'setUsers');
    },
    // Лидеры домашних групп — для выбора в окне ученика
    async actReqwestLeaders({state, commit}) {
        return await vuexGet('/users/home_group_leader', {}, state, commit, 'setLeaders');
    },
    async actReqwestUserRoles({state, commit}) {
        return await vuexGet('/users/roles', {}, state, commit, 'setRoles');
    },
    async actUserCreate({state, commit}, params) {
        return await vuexPost('/users/create', params, state, commit, 'setUsers', {showMsg: false});
    },
    async actUserCreateLink({state, commit}, params) {
        return await vuexPost('/users/auth-link/' + params.uid, {}, state, commit, 'setUsersLink', {showMsg: false});
    },
    async actUserDelete({state, commit}, params) {
        return await vuexPost('/users/delete' , {id:params}, state, commit, 'deleteUser', {showMsg: false});
    },
    async actUserSave({state, commit}, params) {
        return await vuexPost('/users/save/' +  params.id, params, state, commit, 'editUser', {showMsg: false});
    },
}


export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters
}
