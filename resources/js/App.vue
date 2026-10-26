<template>
    <v-app>
        <template v-if="showLoading">
            <div class="loading">
                <div class="loading-inner">загрузка...</div>
            </div>
        </template>
        <template v-else>

            <snack-bar/>
            <confirm-dialog/>
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
                    <v-list-subheader :class="{'bp-subheader--active': section?.key === 'disciple'}">Учебный процесс</v-list-subheader>
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
                    <v-list-subheader :class="{'bp-subheader--active': section?.key === 'teacher'}">Учитель</v-list-subheader>
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
                    <v-list-subheader :class="{'bp-subheader--active': section?.key === 'administrator'}">Администрирование</v-list-subheader>
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
                    <v-list-subheader :class="{'bp-subheader--active': section?.key === 'methodologist'}">Методология</v-list-subheader>
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
                    <v-list-subheader :class="{'bp-subheader--active': section?.key === 'superadmin'}">СуперАдминистрирование</v-list-subheader>
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
                color="surface"
                flat
                class="bp-app-bar"
                v-if="!showFormNicName"
            >
                <v-app-bar-nav-icon @click="drawer = !drawer">
                </v-app-bar-nav-icon>
                <v-app-bar-title>
                    <h3 class="bp-app-title">
                        {{ $vuetify.display.name === 'sm' ? 'Школа' : 'ЦХМ школа' }}
                    </h3>
                    <!-- Текущий раздел системы: видно, в какой его части находишься -->
                    <div v-if="section" class="bp-app-section">{{ section.title }}</div>
                </v-app-bar-title>

                <!-- Имя не должно отжимать название с подписью раздела: ограничиваем ширину и обрезаем -->
                <div class="tw-mx-3 tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted tw-truncate tw-shrink-0 tw-max-w-[35%] md:tw-max-w-none">
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
                    class="tw-w-full bp-breadcrumbs"
                    style="position: fixed; z-index: 100;"
                />


                <v-card variant="text" class="px-3 bp-page" style="min-height: 100%">
                    <router-view></router-view>
                </v-card>
            </v-main>
        </template>
    </v-app>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import SnackBar from "./components/Snackbar.vue";
import ConfirmDialog from "./components/ConfirmDialog.vue";

export default {
    name: 'App',
    components: {SnackBar, ConfirmDialog},
    data: () => ({
        drawer: false,
        // Разделы по префиксу пути маршрута
        sections: [
            {key: 'disciple', title: 'Учебный процесс'},
            {key: 'teacher', title: 'Учитель'},
            {key: 'administrator', title: 'Администрирование'},
            {key: 'methodologist', title: 'Методология'},
            {key: 'superadmin', title: 'Суперадминистрирование'},
        ],
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
                name: 'Курсы',
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
        section() {
            const first = this.$route?.path.split('/')[1];
            return this.sections.find(el => el.key === first) ?? null;
        },
        breadcrumbs() {
            if (!this.$route?.meta || !this.$route?.meta?.breadcrumbs?.length) return []
            return this.$route?.meta?.breadcrumbs.map(el => {
                // Крошка может брать заголовок из стора (например, название открытой группы)
                const title = el.getter ? (this.$store.getters[el.getter]?.[el.field] || el.title) : el.title
                if (el.params) return {...el, title, href: el.href + '/' + this.$route.params[el.params], disabled: false}
                if (el.routName) return {...el, title, to: {name: el.routName}, disabled: false}
                return {...el, title, disabled: true}
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
