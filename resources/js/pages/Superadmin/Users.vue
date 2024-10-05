<template>
    <v-card v-if="getUsers && getRoles">
        <v-card-title>
            Пользователи
        </v-card-title>
        <v-card-text>
            <v-card>
                <v-card-text>
                    <div class="tw-flex tw-flex-col tw-gap-[15px] tw-mb-5 tw-justify-center">
                        <div class="tw-w-full tw-grid tw-gap-2 tw-grid-cols-1 md:tw-grid-cols-2 xl:tw-grid-cols-4">
                            <v-text-field label="Фамилия" v-model="surname"></v-text-field>
                            <v-text-field label="Имя" v-model="name"></v-text-field>
                            <v-text-field label="Отчество" v-model="patronymic"></v-text-field>
                            <v-text-field label="Телефон" type="number" v-model="phone"></v-text-field>
                        </div>
                        <div class="tw-w-full tw-grid tw-gap-2 tw-grid-cols-1 md:tw-grid-cols-2">
                            <div class="tw-flex tw-flex-col md:tw-flex-row">
                                <v-checkbox hide-details top
                                            density="compact"
                                            v-model="role.value"
                                            v-for="role in getRoles"
                                            :label="role.display_name"/>
                            </div>

                            <v-btn class="tw-mt-2" v-if="getRoles" color="primary"
                                   :disabled="!canCreate"
                                   @click="create">Создать пользователя
                            </v-btn>
                        </div>

                    </div>
                </v-card-text>
            </v-card>
            <v-divider class="tw-my-3" color="blue"/>
            <div v-if="getUsers">
                <div class="tw-flex tw-flex-col md:tw-flex-row tw-justify-between">
                    <div class="tw-w-full md:tw-w-1/3">
                        <v-text-field
                            clearable
                            persistent-clear
                            label="Поиск по фамилии/телефону"
                            v-model="search"
                        ></v-text-field>
                    </div>

                    <div class="tw-flex tw-gap-3 tw-flex-col md:tw-flex-row">
                        <v-checkbox
                            hide-details
                            density="compact"
                            v-model="role.value"
                            v-for="role in getRolesSelect"
                            top
                            :label="role.display_name"
                        />
                    </div>
                </div>


                <v-data-table
                    @click:row="dialog=!dialog"
                    :headers="usersHeaders"
                    :items="searchItems"
                    :loading="getLoad"
                >
                    <template v-slot:item.actions="{ item }">
                        <v-icon class="me-2" size="small" @click.stop="editItem(item)">mdi-pencil</v-icon>
                        <v-icon size="small" @click.stop="deleteItem(item)">mdi-delete</v-icon>
                    </template>
                    <template v-slot:header.actions>
                        <div style="float: right">
                            <svg class="tw-cursor-pointer" @click.stop="actReqwestUsers" width="17" height="16"
                                 viewBox="0 0 17 16"
                                 fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M1.59964 3.45737C3.80628 0.250428 8.08538 -0.94687 11.6901 0.811276C15.6603 2.74768 17.3091 7.53595 15.3727 11.5062C13.4363 15.4764 8.64798 17.1251 4.67776 15.1887C3.08138 14.4101 1.81733 13.1467 1.03718 11.5918C0.839085 11.197 0.998557 10.7163 1.39337 10.5182C1.78819 10.3201 2.26884 10.4796 2.46694 10.8744C3.09113 12.1184 4.10063 13.1275 5.379 13.751C8.55518 15.3001 12.3858 13.9811 13.9349 10.8049C15.484 7.62876 14.1651 3.79815 10.9889 2.24902C7.94449 0.764172 4.29884 1.91436 2.63736 4.80937H3.99909C4.44082 4.80937 4.79891 5.16746 4.79891 5.60918C4.79891 6.05091 4.44082 6.409 3.99909 6.409H0.799819C0.358091 6.409 0 6.05091 0 5.60918V2.40991C0 1.96818 0.358091 1.61009 0.799819 1.61009C1.24155 1.61009 1.59964 1.96818 1.59964 2.40991V3.45737Z"
                                      fill="#B0BACD"/>
                            </svg>
                        </div>

                    </template>
                    <template v-slot:loading>
                        <v-skeleton-loader type="table-row@10"></v-skeleton-loader>
                    </template>
                    <template v-slot:item.roles="{item}">
                        <div v-html="item.roles.map((el)=>el.display_name).join(', ')" style="font-size: 12px; "/>
                    </template>
                    <template v-slot:item.surname="{item}">
                        {{ item.surname }} {{ item.name }} {{ item.patronymic }}
                    </template>
                    <template v-slot:item.link="{item}">
                        <Code v-if="item.entry_code" :code="item.entry_code"/>
                        <div v-else @click="actUserCreateLink({ uid:item.id })">сгенерить ключ</div>
                    </template>
                    <template v-slot:item.entrance="{item}">
                        <v-chip :color="item.authorized_at ? 'green': 'grey'">
                            {{ item.authorized_at ? item.authorized_at : 'не заходил' }}
                        </v-chip>
                    </template>

                </v-data-table>
            </div>
        </v-card-text>
    </v-card>
    <Loading v-else/>


</template>

<script>
import {mapActions, mapGetters, mapMutations} from "vuex";
import Code from "../../components/Code.vue";
import Loading from "../../components/Loading.vue";
import ICode from "../../components/icon/ICode.vue";
import IVast from "../../components/icon/IVast.vue";
import IModeration from "../../components/icon/IModeration.vue";
import IMore from "../../components/icon/IMore.vue";

export default {
    name: "Users",
    components: {IMore, IModeration, IVast, ICode, Loading, Code},
    data() {
        return {
            search: '',
            dialog: false,
            surname: '',
            name: '',
            patronymic: '',
            phone: '',
            usersHeaders: [
                {title: 'id', key: 'id'},
                {title: 'Вход', key: 'entrance'},
                {title: 'ФИО', key: 'surname'},
                {title: 'Роли', key: 'roles'},
                {title: 'Телефон', key: 'phone'},
                {title: 'Ссылка', sortable: false, key: 'link'},
                {title: '', align: 'end', sortable: false, key: 'actions'},
            ],
        }
    },
    methods: {
        create() {
            this.actUserCreate({
                surname: this.surname,
                name: this.name,
                patronymic: this.patronymic,
                phone: this.phone,
                roles: this.getRoles.filter(el => el.value).map(el => el.name)
            }).then((res) => {
                if (res) {
                    this.search = this.phone.replace(/^8/, 7);
                    this.surname = '';
                    this.name = '';
                    this.patronymic = '';
                    this.phone = '';
                    let roles = this.getRoles();
                    for (const k in roles) {
                        roles[k].value = false;
                    }
                    this.setRoles(roles);
                }
            });
        },
        editItem(item) {
            alert('Редактирование пользователя. Пока не сделано ((');
        },
        deleteItem(item) {
            alert('Удаление пользователя. Пока не сделано ((');
        },
        ...mapMutations('users', ['setRoles']),
        ...mapActions('users', ['actReqwestUsers', 'actReqwestUserRoles', 'actUserCreate', 'actUserCreateLink']),
    },
    computed: {
        ...mapGetters('users', ['getUsers', 'getRoles', 'getLoad', 'getRolesSelect']),
        searchItems() {
            let result = this.getUsers;
            let isSelectCount = this.getRolesSelect.filter(el => el.value).length;
            let helpSelectArrSelected = this.getRolesSelect.filter(select => select.value)?.map(el => el.name);

            if (!this.search && !isSelectCount) {
                return this.getUsers;
            }

            if (this.search) {
                result = this.getUsers.filter((el) => {
                    return el.phone?.search(this.search) > -1 || el.surname?.search(this.search) > -1;
                });
            }

            if (isSelectCount) {
                result = result.filter(elem => {
                    let arrRoles = elem.roles.map(el => el.name);
                    return arrRoles.filter(ar => helpSelectArrSelected.includes(ar)).length;
                });
            }
            return result;
        },
        canCreate() {
            return !!this.getRoles.filter((el) => el.value).length && !!this.phone && !!this.surname && !!this.name;
        },
    },
    created() {
        this.actReqwestUsers()
        this.actReqwestUserRoles()
    }
}
</script>

<style scoped>

</style>
