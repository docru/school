const state = {
    driver: false,
    menu: [
        {
            icon: 'IDashboard',
            title: 'Обзор платформы',
            routerName: 'Home'
        },
    ],
    snackBarDefault: {
        timeout: 2000,
        text: '',
        color: 'blue-grey',
        open: false
    },
    arraySnackBars: [],
}
const getters = {
    isSnackBarDefault: (state) => state.snackBarDefault,
    isArraySnackBars: (state) => state.arraySnackBars,
    // isEndDate: (state) => state.endDate,
}
const mutations = {
    setSnackBar(state, payload) {
        // alert(payload.text);
        // return;
        const obj = {
            text: payload.text || state.snackBarDefault.text,
            color: payload.color || state.snackBarDefault.color,
            open: true
        }
        state.arraySnackBars.push(obj)
    },
}


const actions = {

}


export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters
}
