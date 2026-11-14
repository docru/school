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
    // Окно подтверждения в стиле приложения (замена системному confirm)
    confirm: {
        open: false,
        title: '',
        text: '',
        okText: 'Да',
        cancelText: 'Отмена',
        color: 'primary',
        resolve: null,
    },
}
const getters = {
    isSnackBarDefault: (state) => state.snackBarDefault,
    isArraySnackBars: (state) => state.arraySnackBars,
    isConfirm: (state) => state.confirm,
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
    openConfirm(state, payload) {
        state.confirm = {
            open: true,
            title: payload.title || 'Подтвердите действие',
            text: payload.text || '',
            okText: payload.okText || 'Да',
            cancelText: payload.cancelText || 'Отмена',
            color: payload.color || 'primary',
            resolve: payload.resolve,
        }
    },
    closeConfirm(state, result) {
        const resolve = state.confirm.resolve;
        state.confirm.open = false;
        state.confirm.resolve = null;
        if (resolve) resolve(!!result);
    },
}


const actions = {
    // Возвращает Promise<boolean>: true — подтвердили, false — отменили или закрыли
    actConfirm({commit, state}, payload) {
        if (state.confirm.resolve) commit('closeConfirm', false);
        return new Promise(resolve => commit('openConfirm', {...payload, resolve}));
    },
}


export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters
}
