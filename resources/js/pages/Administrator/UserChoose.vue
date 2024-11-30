<template>
    <div v-if="getUsers">
        <div>
            <div
                class="tw-flex tw-flex-col md:tw-flex-row  tw-gap-[15px] tw-mb-5 tw-justify-center"
            >
                <div class="tw-w-full tw-grid tw-gap-2 tw-grid-cols-1 md:tw-grid-cols-2 xl:tw-grid-cols-4">
                    <v-text-field label="Фамилия" v-model="surname"></v-text-field>
                    <v-text-field label="Имя" v-model="name"></v-text-field>
                    <v-text-field label="Отчество" v-model="patronymic"></v-text-field>
                    <v-text-field label="Телефон" type="number" v-model="phone"></v-text-field>
                </div>

                <v-btn class="tw-mt-2" color="primary" :disabled="!canCreate" @click="create">
                    {{ $vuetify.display.name === 'sm' ? 'Создать' : 'Создать нового пользователя' }}
                </v-btn>
            </div>
            <v-divider class="tw-my-3" color="blue"/>

            <div v-if="getUsers">
                <v-data-table
                    @click:row="dialog=!dialog"
                    :headers="usersHeaders"
                    :items="searchItems"
                    :loading="getLoad"
                >
                    <template v-slot:item.name="{ item }">
                        <div @click="choose(item.id)">
                            {{ item.surname }} {{ item.name }} {{ item.patronymic }}
                        </div>
                    </template>
                    <template v-slot:loading>
                        <v-skeleton-loader type="table-row@10"></v-skeleton-loader>
                    </template>
                    <template v-slot:item.link="{item}">
                        <Code
                            v-if="item.entry_code"
                            :code="item.entry_code"
                        />
                        <div v-else @click="actUserCreateLink({ uid:item.id })">
                            сгенерить ключ
                        </div>
                    </template>

                </v-data-table>
            </div>
        </div>
    </div>
    <Loading v-else/>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import Code from "../../components/Code.vue";
import Loading from "../../components/Loading.vue";
import ICode from "../../components/icon/ICode.vue";
import normalize from "../../utils/normalize.js";

export default {
    name: "UserChoose",
    components: {ICode, Loading, Code},
    props: {
        group: Object,
        role: {
            type: String,
            default: "disciple",
        },
    },
    data() {
        return {
            search: '',
            dialog: false,
            surname: '',
            name: '',
            patronymic: '',
            phone: '',
        }
    },
    methods: {
        ...mapActions('users', ['actUserCreateLink', 'actReqwestUsers', 'actUserCreate']),
        ...mapActions('administrator', ['actJoinUserToGroup', 'actRequestGroupUsers']),
        create() {
            this.phone = normalize.phone(this.phone);
            this.actUserCreate({
                surname: normalize.name(this.surname),
                name: normalize.name(this.name),
                patronymic: normalize.name(this.patronymic),
                phone: this.phone,
                role: this.role
            }).then((res) => {
                if (res?.users) {
                    for (const i in res.users) {
                        let user = res.users[i];
                        if (user.phone == this.phone) {
                            this.surname = '';
                            this.name = '';
                            this.patronymic = '';
                            this.phone = '';
                            this.choose(user.id);
                        }
                    }
                }
            });
        },
        async choose(userId) { // 'disciple'
            await this.actJoinUserToGroup({groupId: this.group.id, userId, role: this.role});
            this.$emit('choosedUser');
        },
    },
    computed: {
        ...mapGetters('users', ['getUsers', 'getLoad']),
        searchItems() {
            let surname = this.surname.trim().toLowerCase();
            let phone = normalize.phone(this.phone);
            if (surname && phone) {
                return this.getUsers.filter((el) => {
                    return el.phone?.search(phone) > -1 && el.surname?.toLowerCase().search(surname) > -1;
                });
            } else if (phone) {
                return this.getUsers.filter((el) => {
                    return el.phone?.search(phone) > -1;
                });
            } else if (surname) {
                return this.getUsers.filter((el) => {
                    return el.surname?.toLowerCase().search(surname) > -1;
                });
            } else {
                return this.getUsers;
            }
        },
        usersHeaders() {
            if (this.$vuetify.display.name !== 'sm') {
                return [
                    {title: 'ФИО', key: 'name'},
                    {title: 'Телефон', key: 'phone'},
                    {title: 'link', sortable: false, key: 'link'},
                ];
            } else {
                return [
                    {title: 'ФИО', key: 'name'},
                    {title: 'Телефон', key: 'phone'},
                ];
            }
        },
        canCreate() {
            let phone = normalize.phone(this.phone);
            let surname = this.surname.trim();
            let name = normalize.phone(this.phone);
            return phone.length === 11 && surname.length > 3 && name.length > 1 && this.searchItems.length === 0;
        },
    },
    created() {
        this.actReqwestUsers({role: this.role});
    },
}
</script>

<style scoped>

</style>
