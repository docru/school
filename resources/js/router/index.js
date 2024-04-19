import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/',
        name: 'Home',
        component: () => import('../pages/Home.vue'),
        meta:{
            title: 'Обзор платформы',
            breadcrumbs:[
                {title:'Дашборд'},
            ]
        }
    },
    {
        path: '/schedule',
        name: 'Schedule',
        component: () => import('../pages/Schedule.vue'),
        meta:{
            title: 'Обзор платформы',
            breadcrumbs:[
                {title:'Дашборд'},
            ]
        }
    },
    {
        path: '/formnicname',
        name: 'FormNicName',
        component: () => import('../pages/FormNicName.vue'),
    },
    {
        path: '/groups',
        name: 'GroupsList',
        component: () => import('../pages/Groups/GroupsList.vue'),
    },
    {
        path: '/plan',
        name: 'Plan',
        component: () => import('../pages/Plan/Plan.vue'),
    },
    {
        path: '/profile',
        name: 'Profile',
        component: () => import('../pages/Profile.vue'),
    },
    {
        path: '/courses/:idCourse',
        name: 'courseDetail',
        component: () => import('../pages/Courses/CoursesDetail.vue'),
    },
    {
        path: '/users',
        name: 'Users',
        component: () => import('../pages/Users.vue'),
    },
    {
        path: '/courses',
        name: 'CoursesList',
        component: () => import('../pages/Courses/CoursesList.vue'),
    },
    // {
    //     path: '/sites',
    //     name: 'Sites',
    //     component: () => import('../pages/Sites.vue'),
    //     meta:{
    //         title: 'Сайты',
    //         breadcrumbs:[
    //             {title:'Дашборд',routName:'Home'},
    //             {title:'Сайты'},
    //         ],
    //     }
    // },
    // {
    //     path: '/sites/:domain',
    //     name: 'SitesDetail',
    //     component: () => import('../pages/SitesDetail.vue'),
    //     meta:{
    //         breadcrumbs: [
    //             {title: 'Обзор', routName: 'Home'},
    //             {title: 'Сайты', routName: 'Sites'},
    //             {dynamicTitle: 'domain',  params: ['domain']},
    //
    //         ],
    //     }
    // },
    // {
    //     path: '/partner',
    //     name: 'Partner',
    //     component: () => import('../pages/Partner.vue'),
    //     meta:{
    //         title:'Партнеры',
    //         breadcrumbs:[
    //             {title:'Дашборд',routName:'Home'},
    //             {title:'Партнеры'},
    //         ]
    //     }
    // },
    // {
    //     path: '/partner/:link',
    //     name: 'PartnerDetail',
    //     component: () => import('../pages/PartnerDetail.vue'),
    //     meta:{
    //         title:'Ссылка',
    //         breadcrumbs:[
    //             {title:'Дашборд',routName:'Home'},
    //             {title:'Партнеры',routName:'Partner'},
    //             {dynamicTitle: 'link',  params: ['link']},
    //         ]
    //     }
    // },
    // {
    //     path: '/payouts',
    //     name: 'Payouts',
    //     component: () => import('../pages/Payouts.vue'),
    //     meta:{
    //         title:'Вывод денег',
    //         breadcrumbs:[
    //             {title:'Вывод денег'},
    //         ]
    //     }
    // },
    // {
    //     path: '/payouts-settings',
    //     name: 'SettingsPayouts',
    //     component: () => import('../pages/SettingsPayouts.vue'),
    //     meta:{
    //         title:'Настройки платежей',
    //         breadcrumbs:[
    //             {title:'Дашборд',routName:'Home'},
    //             {title:'Настройки платежей'},
    //         ],
    //     }
    // },
    // {
    //     path: '/settings',
    //     name: 'Settings',
    //     component: () => import('../pages/Settings.vue'),
    //     meta:{
    //         title: 'Настройка профиля',
    //         breadcrumbs:[
    //             {title:'Дашборд',routName:'Home'},
    //             {title:'Настройки'},
    //         ],
    //     }
    // },
    //
    //
    {
        path: '/:pathMatch(.*)*',
        name: '404',
        component: () => import('../pages/404.vue'),
        meta: {
            title: '404',
        }
    },
]

const router = createRouter({
    history: createWebHistory(), // import.meta.env.BASE_URL
    routes
});

export default router
