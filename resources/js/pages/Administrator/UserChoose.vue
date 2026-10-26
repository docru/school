<template>
    <div v-if="getUsers">
        <!-- Одно поле поиска: по любой части ФИО или по цифрам телефона -->
        <v-text-field
            v-model="search"
            label="Поиск по имени или телефону"
            prepend-inner-icon="mdi-magnify"
            clearable
            autofocus
            class="!tw-my-0"
        />
        <div class="tw-flex tw-items-center tw-justify-between tw-gap-2 tw-mt-1 tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted">
            <span>{{ listHint }}</span>
            <v-btn
                variant="text"
                size="small"
                color="primary"
                :prepend-icon="showCreate ? 'mdi-chevron-up' : 'mdi-account-plus-outline'"
                class="tw--mr-2"
                @click="toggleCreate"
            >
                {{ showCreate ? 'Скрыть форму' : 'Создать нового' }}
            </v-btn>
        </div>

        <!-- Форма создания скрыта, пока не понадобится: экономит место на телефоне -->
        <v-expand-transition>
            <div v-show="showCreate" class="create-form tw-mt-2">
                <div class="tw-grid tw-gap-2 tw-grid-cols-2 xl:tw-grid-cols-4">
                    <v-text-field label="Фамилия" v-model="surname" class="!tw-my-0"/>
                    <v-text-field label="Имя" v-model="name" class="!tw-my-0"/>
                    <v-text-field label="Отчество" v-model="patronymic" class="!tw-my-0"/>
                    <v-text-field label="Телефон" type="tel" inputmode="numeric" v-model="phone" class="!tw-my-0"/>
                </div>
                <div class="tw-flex tw-items-center tw-justify-between tw-gap-3 tw-mt-2">
                    <div class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted">{{ createHint }}</div>
                    <v-btn color="primary" :disabled="!canCreate" @click="create" class="tw-shrink-0">
                        {{ isMobile ? 'Создать' : 'Создать нового пользователя' }}
                    </v-btn>
                </div>
            </div>
        </v-expand-transition>
        <v-divider class="tw-my-3"/>

        <!-- Мобильный список: имя и телефон, нажатие добавляет в группу -->
        <div v-if="isMobile">
            <v-skeleton-loader v-if="getLoad" type="list-item-two-line@6"/>
            <template v-else>
                <div
                    v-for="item in visibleItems"
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
                <div class="tw-py-3 tw-text-center tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted" v-if="searchItems.length > visibleItems.length">
                    Показаны первые {{ visibleItems.length }} из {{ searchItems.length }} — уточните поиск
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

        <!-- Пусто: предлагаем сразу создать, подставив то, что искали -->
        <div class="tw-py-4 tw-text-center tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted" v-if="!getLoad && !searchItems.length">
            Никого не найдено
            <div class="tw-mt-2" v-if="!showCreate">
                <v-btn variant="outlined" size="small" prepend-icon="mdi-account-plus-outline" @click="toggleCreate">
                    Создать нового
                </v-btn>
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
            showCreate: false,
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
        toggleCreate() {
            this.showCreate = !this.showCreate;
            if (!this.showCreate) return;
            // Подставляем в форму то, что уже набрали в поиске
            const q = (this.search || '').trim();
            const digits = q.replace(/\D/g, '');
            if (digits.length >= 10 && digits.length >= q.length / 2) {
                if (!this.phone) this.phone = digits;
            } else if (q && !this.surname) {
                this.surname = normalize.name(q.split(/\s+/)[0]);
            }
        },
        async choose(userId) { // 'disciple'
            await this.actJoinUserToGroup({groupId: this.group.id, userId, role: this.role});
            this.$emit('choosedUser');
        },
    },
    computed: {
        ...mapGetters('users', ['getUsers', 'getLoad']),
        searchItems() {
            const q = (this.search || '').trim().toLowerCase();
            if (!q) return this.getUsers;
            const digits = q.replace(/\D/g, '');
            const words = q.split(/\s+/).filter(Boolean);
            return this.getUsers.filter((el) => {
                if (digits.length >= 3 && el.phone?.includes(digits)) return true;
                const full = this.fullName(el).toLowerCase();
                return words.every(w => full.includes(w));
            });
        },
        isMobile() {
            return this.$vuetify.display.name === 'sm';
        },
        // На телефоне длинный список не рисуем целиком: первые 50, дальше пусть уточняют поиск
        visibleItems() {
            return this.searchItems.slice(0, 50);
        },
        usersHeaders() {
            return [
                {title: 'ФИО', key: 'name'},
                {title: 'Телефон', key: 'phone'},
                {title: 'link', sortable: false, key: 'link'},
            ];
        },
        canCreate() {
            const phone = normalize.phone(this.phone);
            const surname = this.surname.trim();
            const name = this.name.trim();
            return phone.length === 11 && surname.length > 1 && name.length > 1 && !this.phoneTaken;
        },
        // Такой телефон уже есть в базе — создавать дубль нельзя
        phoneTaken() {
            const phone = normalize.phone(this.phone);
            return phone.length === 11 && this.getUsers.some(u => u.phone === phone);
        },
        listHint() {
            const n = this.searchItems.length;
            if (!(this.search || '').trim()) return `Всего: ${n}. Нажмите, чтобы добавить`;
            return n ? `Найдено: ${n}` : '';
        },
        createHint() {
            if (this.phoneTaken) return 'Такой телефон уже есть — найдите его в списке';
            if (this.canCreate) return 'Можно создать';
            return 'Нужны фамилия, имя и телефон из 11 цифр';
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
