<template>
    <v-app>
        <template v-if="showLoading">
            <div class="loading">
                <div class="loading-inner">загрузка...</div>
            </div>
        </template>
        <template v-else>

            <snack-bar/>
            <v-navigation-drawer
                v-if="!showFormNicName"
                app
                left
                absolute
                temporary
                v-model="drawer"
                class="pa-3"
            >

                <template v-if="getProfile.roles.includes('disciple')">
                    <v-list-subheader>Учебный процесс</v-list-subheader>
                    <v-list-item
                        v-for="item in menu_disciple"
                        :key="item"
                        :to="{name:item.routName}"
                        link
                        :title="item.name"
                        v-if="getProfile.roles.includes('disciple')"
                    />
                </template>

                <template v-if="getProfile.roles.includes('teacher')">
                    <v-list-subheader>Учитель</v-list-subheader>
                    <v-list-item
                        v-for="item in menu_teacher"
                        :key="item"
                        :to="{name:item.routName}"
                        link
                        :title="item.name"
                        v-if="getProfile.roles.includes('teacher')"
                    />
                </template>

                <template v-if="getProfile.roles.includes('administrator')">
                    <v-divider></v-divider>
                    <v-list-subheader>Администрирование</v-list-subheader>
                    <v-list-item
                        v-for="item in menu_admin"
                        :key="item"
                        :to="{name:item.routName}"
                        link
                        :title="item.name"
                    />
                </template>

                <template v-if="getProfile.roles.includes('methodologist')">
                    <v-divider></v-divider>
                    <v-list-subheader>Методология</v-list-subheader>
                    <v-list-item
                        v-for="item in menu_methodologist"
                        :key="item"
                        :to="{name:item.routName}"
                        link
                        :title="item.name"
                    />
                </template>

                <template v-if="getProfile.roles.includes('superadmin')">
                    <v-divider></v-divider>
                    <v-list-subheader>СуперАдминистрирование</v-list-subheader>
                    <v-list-item
                        v-for="item in  menu_superadmin"
                        :key="item"
                        :to="{name:item.routName}"
                        link
                        :title="item.name"
                    />
                </template>

            </v-navigation-drawer>
            <v-app-bar
                app
                color="primary"
                v-if="!showFormNicName"
            >
                <v-app-bar-nav-icon @click="drawer = !drawer">
                </v-app-bar-nav-icon>
                <v-app-bar-title>
                    <h3 class="white--text">
                        {{ $vuetify.display.name === 'sm' ? 'Школа' : 'ЦХМ школа' }}
                    </h3>
                </v-app-bar-title>
                <v-spacer></v-spacer>

                <div class="tw-mx-3">
                    {{ getProfile?.name[0] }} ({{ getProfile?.name[0] }} {{ getProfile?.surname }}
                    {{ getProfile?.patronymic }})
                </div>
                <!--        <router-link class="tw-mx-3" :to="{name:'Profile'}">-->
                <!--        </router-link>-->

            </v-app-bar>
            <v-main>
                <!--              <pre>-->
                <!--                 {{ breadcrumbs }}-->
                <!--              </pre>-->

                <v-breadcrumbs
                    :items="breadcrumbs"
                    class="tw-w-full"
                    style="position: fixed; background-color: white; z-index: 100;"
                />


                <v-card variant="text" class=" px-3 pt-15" style="min-height: 100%">
                    <router-view></router-view>
                </v-card>
            </v-main>
        </template>
    </v-app>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import SnackBar from "./components/Snackbar.vue";

export default {
    name: 'App',
    components: {SnackBar},
    data: () => ({
        drawer: false,
        menu_disciple: [
            {
                name: 'Главная ',
                routName: 'Home'
            },
            {
                name: 'Мои курсы',
                routName: 'DiscipleGroupsList'
            },
            // {
            //   name: 'Мои задания ',
            //   routName: 'Home'
            // },

        ],
        menu_teacher: [
            {
                name: 'Главная ',
                routName: 'Home'
            },
            {
                name: 'Мои группы',
                routName: 'TeacherGroupsList'
            },
        ],
        menu_admin: [
            {
                name: 'Ученики',
                routName: 'AdministratorDisciples'
            },
            {
                name: 'Группы',
                routName: 'AdministratorGroupsList'
            },

        ],
        menu_methodologist: [
            {
                name: 'Курсы ',
                routName: 'MethodologistCoursesList'
            },
        ],
        menu_superadmin: [
            {
                name: 'Пользователи ',
                routName: 'SuperadminUsers'
            },

        ],
    }),
    methods: {
        routePoint() {
            if (!this.validProfile) {
                this.$router.push({
                    name: 'FormNicName'
                })
            }
        },
        ...mapActions('profile', ['actResetProfile'])
    },
    computed: {
        breadcrumbs() {
            if (!this.$route?.meta || !this.$route?.meta?.breadcrumbs?.length) return []
            return this.$route?.meta?.breadcrumbs.map(el => {
                if (el.params) return {...el, href: el.href + '/' + this.$route.params[el.params], disabled: false}
                if (el.routName) return {...el, to: {name: el.routName}, disabled: false}
                return {...el, disabled: true}
            })
        },
        validProfile() {
            return this.getProfile?.nickname && this.getProfile?.name
        },
        showLoading() {
            return this.getLoad;
        },
        showFormNicName() {
            return !this.getLoad && this.$route.name === 'FormNicName';
        },
        ...mapGetters('profile', ['getLoad', 'getProfile'])
    },
    async mounted() {
        await this.actResetProfile();
    },

}
</script>

<style scoped>
.loading {
    width: 100%;
    height: 100%;
    display: flex;
}

.loading-inner {
    margin: auto;
}
</style>
