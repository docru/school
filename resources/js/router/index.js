import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/',
        name: 'Home',
        component: () => import('../pages/Home.vue'),
        meta:{
            title: 'Обзор платформы',
            breadcrumbs:[
                {title:'Главная'},
                // {title:'Дашборд',routName:'AdministratorDisciples'},
            ]
        }
    },

    // администратор
    {
        path: '/administrator/disciples',
        name: 'AdministratorDisciples',
        component: () => import('../pages/Administrator/Disciples.vue'),
        meta:{
            title: 'Группа',
            breadcrumbs:[
                {title:'Главная',routName:'Home'},
                {title:'Ученики'},
            ]
        }
    },
    {
        path: '/administrator/groups',
        name: 'AdministratorGroupsList',
        component: () => import('../pages/Administrator/GroupsList.vue'),
        meta:{
            title: 'Группа',
            breadcrumbs:[
                {title:'Главная',routName:'Home'},
                {title:'Группы'},
            ]
        }
    },
    {
        path: '/administrator/group/:id',
        name: 'AdministratorDetailGroup',
        component: () => import('../pages/Administrator/DetailGroup.vue'),
        meta:{
            title: 'Группа',
            breadcrumbs:[
                {title:'Главная',routName:'Home'},
                {title:'Группы',routName:'AdministratorGroupsList'},
                {title:'Группа'},
            ]
        }
    },

    // методист
    {
        path: '/methodologist/courses',
        name: 'MethodologistCoursesList',
        component: () => import('../pages/Methodologist/CoursesList.vue'),
        meta:{
            title: 'Курсы',
            breadcrumbs:[
                {title:'Курсы'},
            ]
        }
    },
    {
        path: '/methodologist/courses/:idCourse',
        name: 'MethodologistCourseDetail',
        component: () => import('../pages/Methodologist/CourseDetail.vue'),
        meta:{
            title: 'Курсы',
            breadcrumbs:[
                {title:'Курсы',routName:'MethodologistCoursesList'},
            ]
        }
    },

    // учитель
    {
        path: '/teacher/groups',
        name: 'TeacherGroupsList',
        component: () => import('../pages/Teacher/GroupsList.vue'),
        meta:{
            title: 'Группы',
            breadcrumbs:[
                {title:'Главная',routName:'Home'},
                {title:'Курсы'},
            ]
        }
    },
    {
        path: '/teacher/group/:id',
        name: 'TeacherDetailGroup',
        component: () => import('../pages/Teacher/DetailGroup.vue'),
        meta:{
            title: 'Группы',
            breadcrumbs:[
                {title:'Главная',routName:'Home'},
                {title:'Курсы',routName:'TeacherGroupsList'},
                {title:'Уроки'},

            ]
        }
    },
    {
        path: '/teacher/lesson/:groupId/:lessonId',
        name: 'TeacherLesson',
        component: () => import('../pages/Teacher/Lesson.vue'),
        meta:{
            title: 'Урок',
            breadcrumbs:[
                {title:'Главная',routName:'Home'},
                {title:'Курсы',routName:'TeacherGroupsList'},
                {title:'Уроки',href:'/teacher/group',params:'groupId'},
                {title:'Урок'},

            ]
        }
    },

    // ученик
    {
        path: '/disciple/groups',
        name: 'DiscipleGroupsList',
        component: () => import('../pages/Disciple/GroupsList.vue'),
        meta:{
            title: 'Группы',
            breadcrumbs:[
                {title:'Главная',routName:'Home'},
                {title:'Курсы'},
            ]
        }
    },
    {
        path: '/disciple/group/:id',
        name: 'DiscipleDetailGroup',
        component: () => import('../pages/Disciple/DetailGroup.vue'),
        meta:{
            title: 'Группы',
            breadcrumbs:[
                {title:'Главная',routName:'Home'},
                {title:'Курсы',routName:'DiscipleGroupsList'},
                {title:'Уроки'},

            ]
        }
    },
    {
        path: '/disciple/lesson/:groupId/:lessonId',
        name: 'DiscipleLesson',
        component: () => import('../pages/Disciple/Lesson.vue'),
        meta:{
            title: 'Урок',
            breadcrumbs:[
                {title:'Главная',routName:'Home'},
                {title:'Курсы',routName:'DiscipleGroupsList'},
                {title:'Уроки',href:'/disciple/group',params:'groupId'},
                {title:'Урок'},

            ]
        }
    },


    // {
    //     path: '/profile',
    //     name: 'Profile',
    //     component: () => import('../pages/Profile.vue'),
    // },
    {
        path: '/superadmin/users',
        name: 'SuperadminUsers',
        component: () => import('../pages/Superadmin/Users.vue'),
    },


    // Под вопросом???
    {
        path: '/plan',
        name: 'Plan',
        component: () => import('../pages/Plan/Plan.vue'),
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
