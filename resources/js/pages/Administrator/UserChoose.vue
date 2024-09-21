<template>
    <v-card v-if="getUsers">
        <v-card-text>
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
                    Создать нового пользователя
                </v-btn>
            </div>
            <v-divider class="tw-my-3" color="blue"/>
            <div v-if="getUsers">
                <div class="tw-flex tw-flex-col md:tw-flex-row tw-justify-between">
                    <div class="tw-w-full">
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
                    <template v-slot:item.name="{ item }">
                        <div @click="choose(item.id)">
                            {{ item.surname }} [{{ item.name }}]
                        </div>
                    </template>
                    <template v-slot:loading>
                        <v-skeleton-loader type="table-row@10"></v-skeleton-loader>
                    </template>
                    <template v-slot:item.surname="{item}">
                        {{ item.surname }} {{ item.name }} {{ item.patronymic }}
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
        </v-card-text>
    </v-card>
    <Loading v-else/>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import Code from "../../components/Code.vue";
import Loading from "../../components/Loading.vue";
import ICode from "../../components/icon/ICode.vue";

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
            usersHeaders: [
                {title: 'ФИО', key: 'surname'},
                {title: 'Телефон', key: 'phone'},
                {title: 'link', sortable: false, key: 'link'},
            ],
        }
    },
    methods: {
        ...mapActions('users', ['actUserCreateLink', 'actReqwestUsers', 'actUserCreate']),
        ...mapActions('administrator', ['actJoinUserToGroup', 'actRequestGroupUsers']),
        create() {
            this.actUserCreate({
                surname: this.surname,
                name: this.name,
                patronymic: this.patronymic,
                phone: this.phone,
                role: this.role
            }).then(() => {
                this.search = this.phone.replace(/^8/, 7);
                this.surname = '';
                this.name = '';
                this.patronymic = '';
                this.phone = '';
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
            if (!this.search) {
                return this.getUsers;
            }

            return this.getUsers.filter((el) => {
                return el.phone?.search(this.search) > -1 || el.surname?.search(this.search) > -1;
            });
        },
        canCreate() {
            return !!this.phone && !!this.surname && !!this.name;
        },
    },
    created() {
        this.actReqwestUsers({role: this.role});
    },
}
</script>

<style scoped>

</style>
