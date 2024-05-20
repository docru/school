<template>
    <v-card v-if="getUsers">
        <v-card-text>
            <div
                class="tw-flex tw-flex-col md:tw-flex-row  tw-gap-[15px] tw-mb-5 tw-justify-center"
            >
                <div class="tw-w-full">
                    <v-text-field label="Введите телефон" type="number" v-model="phone"></v-text-field>
                </div>

                <v-btn class="tw-mt-2" color="primary" :disabled="!phone" @click="create">Создать ученика</v-btn>
            </div>
            <v-divider class="tw-my-3" color="blue"/>
            <div v-if="getUsers">
                <div class="tw-flex tw-flex-col md:tw-flex-row tw-justify-between">
                    <div class="tw-w-full">
                        <v-text-field
                            clearable
                            type="number"
                            label="Поиск по номеру телефона"
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
                            {{ item.name }} [{{ item.nickname }}]
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
                        <div v-else @click="ACT_POST_UserCreateLink({ uid:item.id })">
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
    name: "DisciplesChoose",
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
            phone: '',
            usersHeaders: [
                {title: 'Имя', key: 'name'},
                // {title: 'Псевдоним', key: 'nicname'},
                {title: 'Телефон', key: 'phone'},
                {title: 'link', sortable: false, key: 'link'},

            ]
        }
    },
    methods: {
        ...mapActions('users', [
            'actRequestDisciples',
            'actDiscipleCreate',
            'ACT_POST_UserCreateLink',
        ]),
        ...mapActions('groups', ['actJoinUserToGroup']),
        create() {
            this.actDiscipleCreate({phone: this.phone,});
        },
        async choose(userId) { // 'disciple'
            await this.actJoinUserToGroup({groupId: this.group.id, userId, role: this.role});
            this.$emit('choosedUser');
        }
    },
    computed: {
        searchItems() {
            let result = this.getUsers;

            if (!this.search) return this.getUsers

            if (this.search)
                result = this.getUsers.filter((el) => {
                    return el.phone.search(this.search) > -1
                })
            return result;
        },
        ...mapGetters('users', ['getUsers', 'getLoad'])
    },
    created() {
        this.actRequestDisciples()
    }
}
</script>

<style scoped>

</style>
