<template>
    <v-card v-if="getUsers">
        <v-card-title>
            Ученики
        </v-card-title>
        <v-card-text>
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
                </div>


                <v-data-table
                    @click:row="dialog=!dialog"
                    :headers="usersHeaders"
                    :items="searchItems"
                    :loading="getLoad"
                >
                    <template v-slot:item.actions="{ item }">
                        <div class="tw-flex">
                            <v-icon
                                class="me-2"
                                size="small"
                                @click.stop="openEditF(item)"
                            >
                                mdi-pencil
                            </v-icon>
                            <v-icon
                                v-if="!item.deleted_at"
                                size="small"
                                @click.stop="openDel = true; currentItem = item"
                            >
                                mdi-delete
                            </v-icon>
                        </div>

                    </template>
                    <template v-slot:header.actions>
                        <div style="float: right">
                            <svg class="tw-cursor-pointer" @click.stop="actRequestDisciples" width="17" height="16"
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
                    <template v-slot:item.surname="{item}">
                        {{ item.surname }} {{ item.name }} {{ item.patronymic }}
                    </template>
                    <template v-slot:item.lesson="{item}">
                        <div class=""
                             v-for="a in attendance(item.id)"
                             :key="a.uid + '_' + a.gid"
                        >
                            <v-checkbox
                                color="green"
                                hide-details
                                density="compact"
                                :label="a.lable"
                                style="text-align: center"
                                v-model="attendancesAll"
                                :disabled="a.status_day !== 'open'"
                                :value="a.uid + '_' + a.gid"
                                @change="setAttendance({groupSchoolDayId: a.gsd_id, userId: a.uid})"
                            />
                        </div>
                    </template>
                    <template v-slot:item.link="{item}">
                        <template v-if="!item.deleted_at">
                            <Code
                                v-if="item.entry_code"
                                :code="item.entry_code"
                            />
                            <div v-else @click="actUserCreateLink({ uid:item.id })"> сгенерить ключ</div>
                        </template>
                    </template>
                    <template v-slot:item.entrance="{item}">
                        <v-chip v-if="!!item.deleted_at">удален</v-chip>
                        <v-tooltip v-else :text="item.authorized_at ? item.authorized_at : 'не заходил'">
                            <template v-slot:activator="{ props }">
                                <v-icon v-bind="props" :color="item.authorized_at ? 'green': 'grey'">mdi-circle</v-icon>
                            </template>
                        </v-tooltip>
                    </template>

                </v-data-table>
            </div>
        </v-card-text>
    </v-card>
    <Loading v-else/>
    <v-dialog width="300" v-model="openDel">
        <v-card elevation="0" class="tw-p-2">
            <div class="tw-flex tw-justify-between">
                <div>Вы точно хотите удалить ученика?</div>
                <div>
                    <v-icon @click="openDel = false;currentItem=null">mdi-close</v-icon>
                </div>
            </div>
            <div>
                <v-btn color="red" block class="tw-my-3" @click.stop="deleteItem">
                    да
                </v-btn>
            </div>
        </v-card>

    </v-dialog>
    <v-dialog width="300" v-model="openEdit">
        <v-card class="tw-p-2">
            <div class="tw-flex tw-justify-between">
                <div>Редактирование ученика</div>
                <div>
                    <v-icon @click="closeEdit">mdi-close</v-icon>
                </div>
            </div>
            <div v-if="currentItem">
                <v-text-field v-model="currentItem.name" label="Имя"></v-text-field>
                <v-text-field v-model="currentItem.surname" label="Фамилия"></v-text-field>
                <v-text-field v-model="currentItem.patronymic" label="Отчество"></v-text-field>
                <v-text-field v-model="currentItem.phone" label="Телефон"></v-text-field>
                <v-btn block @click.stop="editUser">Сохранить</v-btn>
            </div>

        </v-card>

    </v-dialog>


</template>

<script>
import {mapActions, mapGetters} from "vuex";
import Code from "../../components/Code.vue";
import Loading from "../../components/Loading.vue";
import ICode from "../../components/icon/ICode.vue";
import IVast from "../../components/icon/IVast.vue";
import IModeration from "../../components/icon/IModeration.vue";
import IMore from "../../components/icon/IMore.vue";
import {format} from "date-fns";

export default {
    name: "Disciples",
    components: {IMore, IModeration, IVast, ICode, Loading, Code},
    data() {
        return {
            currentItem: null,
            openEdit: false,
            openDel: false,
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
                {title: 'Телефон', key: 'phone'},
                {title: 'Урок', key: 'lesson'},
                {title: 'link', sortable: false, key: 'link'},
                {title: '', align: 'end', sortable: false, key: 'actions'},
            ],
        }
    },
    methods: {
        attendance(uid) {
            let res = [];
            for (const k in this.getAttendancesAll) {
                let a = this.getAttendancesAll[k];
                if (a.uid === uid) {
                    a.lable = a.group_name + '(' + format(new Date(a.date), 'MM.dd') + ')';
                    res.push(a);
                }
            }
            return res;
        },
        openEditF(item) {
            if (!item?.id) {
                alert('Не определен пользователь')
            } else {
                this.openEdit = true;
                this.currentItem = {
                    id: item.id,
                    phone: item.phone || '',
                    name: item.name || '',
                    surname: item.surname || '',
                    patronymic: item.patronymic || '',
                };
            }
        },
        closeEdit() {
            this.openEdit = false
            this.currentItem = null
        },
        async setAttendance(data) {
            await this.actSetAttendance(data);
            await this.actRequestAttendancesAll();
        },
        async editUser() {
            let res = await this.actUserSave(this.currentItem);
            if (res !== false) {
                this.openEdit = false;
                this.currentItem = null;
                await this.getDisciples();
            }
        },
        async deleteItem() {
            await this.actUserDelete(this.currentItem.id).then(() => this.openDel = false);
            await this.getDisciples();
        },
        async getDisciples() {
            await this.actReqwestUsers({role: 'disciple'});
            await this.actRequestAttendancesAll();
        },
        ...mapActions('users', [
            'actReqwestUsers',
            'actUserCreateLink',
            'actUserDelete',
            'actUserSave',
        ]),
        ...mapActions('administrator', ['actRequestAttendancesAll', 'actSetAttendance']),
    },
    computed: {
        attendancesAll: {
            get() {
                let res = [];
                for (const k in this.getAttendancesAll) {
                    let a = this.getAttendancesAll[k];
                    if (a.attendance) {
                        res.push(a.uid + '_' + a.gid);
                    }
                }
                return res;
            },
            set() {
            },
        },
        searchItems() {
            if (!this.search) {
                return this.getUsers;
            }

            return this.getUsers.filter((el) => {
                return el.phone?.search(this.search) > -1 || el.surname?.search(this.search) > -1;
            });
        },
        ...mapGetters('users', ['getUsers', 'getLoad']),
        ...mapGetters('administrator', ['getAttendancesAll']),
    },
    created() {
        this.getDisciples();
    }
}
</script>

<style scoped>

</style>
