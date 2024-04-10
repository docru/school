
const statuses = {
    'default':{
        title:'Нет статуса',
        color:'gray'
    },
    'modering':{
        title:'На модерации',
        color:'yellow-darken-1'
    },
    'active':{
        title:'Активен',
        color:'green'
    },
    'decline':{
        title:'Отклонен',
        color:'red'
    },
}
export const getStatusObj=(status)=>{
    return statuses[status] || statuses['default'];
}