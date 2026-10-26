<template>
    <div v-if="getUsers">
        <!-- Поиск и создание в одном: по мере ввода фамилии или телефона список ниже фильтруется,
             а если никого не нашлось и поля заполнены — можно создать нового пользователя -->
        <div class="tw-grid tw-gap-2 tw-grid-cols-2 xl:tw-grid-cols-4">
            <v-text-field label="Фамилия" v-model="surname" class="!tw-my-0"/>
            <v-text-field label="Имя" v-model="name" class="!tw-my-0"/>
            <v-text-field label="Отчество" v-model="patronymic" class="!tw-my-0"/>
            <v-text-field label="Телефон" type="tel" inputmode="numeric" v-model="phone" class="!tw-my-0"/>
        </div>
        <div class="tw-flex tw-items-center tw-justify-between tw-gap-3 tw-mt-2">
            <div class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted">{{ hint }}</div>
            <v-btn color="primary" :disabled="!canCreate" @click="create" class="tw-shrink-0">
                {{ isMobile ? 'Создать' : 'Создать нового пользователя' }}
            </v-btn>
        </div>
        <v-divider class="tw-my-3"/>

        <!-- Мобильный список: имя и телефон, нажатие добавляет в группу -->
        <div v-if="isMobile">
            <v-skeleton-loader v-if="getLoad" type="list-item-two-line@6"/>
            <template v-else>
                <div
                    v-for="item in searchItems"
                    :key="item.id"
                    class="user-row"
                    @click="choose(item.id)"
                >
                    <div class="tw-min-w-0">
                        <div class="tw-text-[15px] tw-truncate">{{ fullName(item) }}</div>
                        <div class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted" v-if="item.phone">+{{ item.phone }}</div>
                    </div>
                    <v-icon color="primary" size="small">mdi-plus-circle-outline</v-icon>
                </div>
                <div class="tw-py-4 tw-text-center tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted" v-if="!searchItems.length">
                    Никого не найдено
                </div>
            </template>
        </div>

        <v-data-table
            v-else
            :headers="usersHeaders"
            :items="searchItems"
            :loading="getLoad"
            class="users-table"
        >
            <template v-slot:item.name="{ item }">
                <div @click="choose(item.id)">{{ fullName(item) }}</div>
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
        fullName(item) {
            return [item.surname, item.name, item.patronymic].filter(Boolean).join(' ');
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
        isMobile() {
            return this.$vuetify.display.name === 'sm';
        },
        usersHeaders() {
            return [
                {title: 'ФИО', key: 'name'},
                {title: 'Телефон', key: 'phone'},
                {title: 'link', sortable: false, key: 'link'},
            ];
        },
        canCreate() {
            let phone = normalize.phone(this.phone);
            let surname = this.surname.trim();
            let name = this.name.trim();
            return phone.length === 11 && surname.length > 1 && name.length > 1 && this.searchItems.length === 0;
        },
        // Подсказка под полями: что делать дальше
        hint() {
            const typed = this.surname.trim() || normalize.phone(this.phone);
            if (!typed) return 'Введите фамилию или телефон, чтобы найти';
            if (this.searchItems.length) return `Найдено: ${this.searchItems.length}. Нажмите, чтобы добавить`;
            if (this.canCreate) return 'Никого не нашли — можно создать';
            return 'Не нашли. Для создания нужны фамилия, имя и телефон из 11 цифр';
        },
    },
    created() {
        this.actReqwestUsers({role: this.role});
    },
}
</script>

<style scoped lang="scss">
.user-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    min-height: 48px;
    padding: 6px 4px 6px 0;
    border-bottom: 1px solid var(--bp-border);
    cursor: pointer;
}

.users-table :deep(tbody tr) {
    cursor: pointer;
}
</style>
