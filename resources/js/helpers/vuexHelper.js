import axios from "axios";

export const vuexLSSet = (name, data) => {
    localStorage[name] = JSON.stringify(data);
    return data;
}

export const vuexLSGet = (name, defaultData) => {
    let data = defaultData;
    if (typeof localStorage[name] != 'undefined')
        data = JSON.parse(localStorage[name]);
    return data;
}


export const vuexRequest = async (method, url, params = {}, state, commit, mutatorName = false, config = {}) => {
    let result = true;
    try {
        state.load = true;
        params = (method === 'get') ? {params: params} : params; // такой прикол у axios
        const {data} = await axios[method]('/api' + url, params);
        if (data.result === 'error' && !config.hideLog) {
            throw `${data.message}`
        }
        if (mutatorName !== false) {
            commit(mutatorName, data.data);
        }
        if (config.showMsg || data.data?.msgOk) {
            let msgOk = data.data?.msgOk ?? config.msgOk;
            commit('app/setSnackBar',
                {text: msgOk, open: true},
                {root: true})
        }
        result = data.data;
    } catch (e) {
        console.error(e);
        commit('app/setSnackBar',
            {text: (e ?? config.msgErr) + ' url: ' + url, open: true, color: 'red'},
            {root: true});
        result = false;
    } finally {
        state.load = false
    }
    return result;
}

export const vuexGet = async (url, params, state, commit, mutatorName = false, configCustom = {}) => {
    let config = {
        showMsg: true,
        msgOk: 'Загружено',
        msgErr: 'Что-то пошло не так',
    };
    Object.assign(config, configCustom);
    return await vuexRequest('get', url, params, state, commit, mutatorName, config);
}
export const vuexPost = async (url, params, state, commit, mutatorName = false, configCustom = {}) => {
    let config = {
        showMsg: true,
        msgOk: 'Сохранено',
        msgErr: 'Что-то пошло не так',
    };
    Object.assign(config, configCustom);
    return await vuexRequest('post', url, params, state, commit, mutatorName, config);
}
export const vuexDelete = async (url, params, state, commit, mutatorName = false, configCustom = {}) => {
    let config = {
        showMsg: true,
        msgOk: 'Удалено',
        msgErr: 'Что-то пошло не так',
    };
    Object.assign(config, configCustom);
    return await vuexRequest('delete', url, params, state, commit, mutatorName, config);
}

export const vuexPut = async (url, params, state, commit, mutatorName = false, configCustom = {}) => {
    let config = {
        showMsg: true,
        msgOk: 'Сохранено',
        msgErr: 'Что-то пошло не так',
    };
    Object.assign(config, configCustom);
    return await vuexRequest('put', url, params, state, commit, mutatorName, config);
}
