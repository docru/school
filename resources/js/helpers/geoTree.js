export function addPropsToRegionsTree(region, parent, regionsCatalog) {
    // Каждому объекту в дереве регионов добавить
    region.parent = parent; // родителя
    region.set = false; // установленный статус
    region.calc = false; // вычисленный статус
    region.hasSetChaild = false; // наследники с установленным статусом
    // Создать каталог регионов
    regionsCatalog[region.id] = region;

    for (const i in region.children) {
        addPropsToRegionsTree(region.children[i], region, regionsCatalog)
    }
}

// Функция поиска всех родителей региона
export function searchParents(regionId, tree, parents) {
    for (let row in tree) {
        if (tree[row]['id'] == regionId) {
            return parents;
        }
        if (!!searchParents(regionId, tree[row]['children'], parents)) {
            parents.push(tree[row]['id']);
            return parents;
        }
    }
    return false;
}

export function uniqueizeRegionsParents(parents, regionsParents) {
    regionsParents = parents.reduce((acc, item) => {
        if (acc.includes(item)) {
            return acc; // если значение уже есть, то просто возвращаем аккумулятор
        }
        return [...acc, item]; // добавляем к аккумулятору и возвращаем новый аккумулятор
    }, []);
    return regionsParents;
}

/**
 * Запись установленного статуса каждого региона в дереве регионов
 * @param state
 */
export function setRegionsTreeStatusHelp(state) {
    state.dataTargetGeo['allowed'].forEach(function(regionId) {
        state.regionsCatalog[regionId].set = 'allowed'; // Разрешен
    });
    state.dataTargetGeo['denied'].forEach(function(regionId) {
        state.regionsCatalog[regionId].set = 'denied'; // Запрещен
    });
}

export function calcStatusRegionsHelp(state) {
    let parents = [];
    // Функция вычисления статуса дочерних регионов по установленному статусу родителя
    function calcStatusChildren(region, status) {
        // Если нет своего установленного, то записываем статус родителя
        if (!region.set) {
            region.calc = status;
        }
        for (const row in region.children) {
            calcStatusChildren(region.children[row], status);
        }
    }
    // Объединить в один массив все регионы с установленным статусом
    for (let key in state.regionsSet) {
        let regionId = state.regionsSet[key];
        // Найти родителей для тех регионов, которые имеют установленный статус
        searchParents(regionId, state.geoTree['children'], parents);
        // Для региона с установленным статусом записать наследуемый статус в его дочерние регионы
        calcStatusChildren(state.regionsCatalog[regionId], state.regionsCatalog[regionId].set);
    }
    // Уникализировать массив родителей
    state.regionsParents = uniqueizeRegionsParents(parents, {});

    // Отметить регионы, которые имеют дочерние регионы с установленным статусом
    for (let id of state.regionsParents) {
        state.regionsCatalog[id].hasSetChaild = true;
    }
}