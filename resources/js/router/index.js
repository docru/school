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
        path: '/group/:id',
        name: 'DetailGroup',
        component: () => import('../pages/Groups/DetailGroup.vue'),
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
        path: '/disciples',
        name: 'Disciples',
        component: () => import('../pages/Disciples.vue'),
    },
    {
        path: '/courses',
        name: 'CoursesList',
        component: () => import('../pages/Courses/CoursesList.vue'),
    },
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
