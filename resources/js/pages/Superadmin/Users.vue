<template>
    <!-- Нижний отступ на мобильном — чтобы плавающая кнопка не закрывала последнюю строку -->
    <div class="users-page tw-pb-24 md:tw-pb-0">
        <!-- Шапка раздела (только десктоп): на мобильном название уже есть в хлебных крошках -->
        <div v-if="!isMobile" class="tw-flex tw-items-start tw-justify-between tw-gap-3 tw-mb-4">
            <div class="tw-min-w-0">
                <h1 class="adf-heading tw-text-2xl tw-leading-8 tw-m-0">Пользователи</h1>
                <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted">{{ subtitle }}</div>
            </div>
            <div class="tw-flex tw-items-center tw-gap-1 tw-shrink-0">
                <v-btn
                    icon="mdi-refresh"
                    variant="text"
                    color="muted"
                    size="small"
                    :loading="getLoad"
                    title="Обновить список"
                    @click="loadAll"
                />
                <v-btn color="primary" prepend-icon="mdi-plus" @click="openCreate">Новый пользователь</v-btn>
            </div>
        </div>

        <!-- Поиск и фильтр по ролям -->
        <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center tw-gap-3 tw-mb-4">
            <v-text-field
                v-model="search"
                label="Поиск по имени или телефону"
                prepend-inner-icon="mdi-magnify"
                clearable
                class="!tw-my-0 md:tw-max-w-sm tw-w-full"
            />
            <v-select
                v-model="roleFilter"
                :items="roleItems"
                item-title="display_name"
                item-value="name"
                label="Роли"
                multiple
                clearable
                hide-details
                class="!tw-my-0 md:tw-max-w-sm tw-w-full"
            >
                <template v-slot:selection="{item, index}">
                    <span v-if="index === 0" class="tw-truncate">{{ item.title }}</span>
                    <span v-if="index === 1" class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted tw-ml-1">
                        +{{ roleFilter.length - 1 }}
                    </span>
                </template>
            </v-select>
        </div>

        <v-progress-linear v-if="getLoad && !users.length" indeterminate color="primary" class="tw-mb-4"/>

        <!-- Таблица (десктоп) -->
        <v-card v-if="!isMobile && filtered.length">
            <v-data-table
                class="users-table"
                :headers="headers"
                :items="visibleItems"
                :sort-by="sortBy"
                @update:sort-by="sortBy = $event"
                :custom-key-sort="noSort"
                items-per-page="-1"
            >
                <!-- В Vuetify 3 подвал убирается пустым слотом: hide-default-footer там нет -->
                <template v-slot:bottom></template>
                <template v-slot:item.authorized_at="{item}">
                    <v-tooltip :text="item.authorized_at ? 'Последний вход ' + fmtDateTime(item.authorized_at) : 'Входа ещё не было'">
                        <template v-slot:activator="{ props }">
                            <span v-bind="props" class="entry-dot" :class="{'entry-dot--on': item.authorized_at}"></span>
                        </template>
                    </v-tooltip>
                </template>
                <template v-slot:item.fio="{item}">
                    <span class="tw-font-medium" :class="{'tw-text-text-muted dark:tw-text-dark-text-muted': item.deleted_at}">{{ item.fio }}</span>
                </template>
                <template v-slot:item.roleNames="{item}">
                    <span v-if="item.roleNames" class="tw-text-xs">{{ item.roleNames }}</span>
                    <span v-else class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted">без ролей</span>
                </template>
                <template v-slot:item.phone="{item}">
                    <Code v-if="item.phone" :code="fmtPhone(item.phone)" density="compact" class="tw-w-max"/>
                    <span v-else class="tw-text-text-muted dark:tw-text-dark-text-muted">—</span>
                </template>
                <template v-slot:item.link="{item}">
                    <span v-if="item.deleted_at" class="tw-text-text-muted dark:tw-text-dark-text-muted">—</span>
                    <v-btn
                        v-else-if="item.entry_code"
                        variant="text"
                        size="small"
                        color="primary"
                        prepend-icon="mdi-content-copy"
                        @click="copyLink(item)"
                    >Скопировать</v-btn>
                    <v-btn
                        v-else
                        variant="text"
                        size="small"
                        color="muted"
                        prepend-icon="mdi-link-variant-plus"
                        @click="actUserCreateLink({uid: item.id})"
                    >Создать</v-btn>
                </template>
                <template v-slot:item.actions="{item}">
                    <div class="tw-flex tw-justify-end tw-gap-1">
                        <v-btn icon="mdi-pencil-outline" variant="text" color="muted" size="small" title="Редактировать" @click="openEdit(item)"/>
                        <v-btn v-if="!item.deleted_at" icon="mdi-delete-outline" variant="text" color="error" size="small" title="Удалить" @click="removeUser(item)"/>
                    </div>
                </template>
            </v-data-table>
        </v-card>

        <!-- Список (телефон) -->
        <v-card v-else-if="filtered.length">
            <ListRow
                v-for="item in visibleItems"
                :key="item.id"
                :title="item.fio"
                :muted="!!item.deleted_at"
                clickable
                @click="openSheet(item)"
            >
                <template #lead>
                    <span class="entry-dot tw-shrink-0" :class="{'entry-dot--on': item.authorized_at}"></span>
                </template>
                <template #subtitle>
                    <span v-if="item.phone">{{ fmtPhone(item.phone) }}</span>
                    <span v-else>телефон не указан</span>
                    <span> · {{ item.roleNames || 'без ролей' }}</span>
                </template>
            </ListRow>
        </v-card>

        <!-- Пустое состояние -->
        <v-card v-else-if="!getLoad">
            <div class="tw-p-8 tw-text-center">
                <v-icon size="40" color="muted">mdi-account-search-outline</v-icon>
                <div class="adf-heading tw-text-lg tw-mt-2 tw-mb-1">{{ users.length ? 'Никого не найдено' : 'Пользователей пока нет' }}</div>
                <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted" v-if="users.length">
                    Измените запрос или фильтр по ролям
                </div>
            </div>
        </v-card>

        <!-- Автоподгрузка: пока этот блок виден, показываем ещё -->
        <div ref="sentinel" class="tw-h-px"></div>
        <div v-if="shown < filtered.length" class="tw-flex tw-items-center tw-justify-center tw-gap-2 tw-py-4 tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted">
            <v-progress-circular indeterminate size="16" width="2" color="muted"/>
            показано {{ shown }} из {{ filtered.length }}
        </div>

        <!-- Мобильная кнопка создания -->
        <v-btn
            v-if="isMobile"
            icon="mdi-plus"
            color="primary"
            size="large"
            elevation="4"
            title="Новый пользователь"
            class="create-fab"
            @click="openCreate"
        />

        <!-- Окно пользователя (телефон) -->
        <v-bottom-sheet v-model="sheet">
            <v-card
                v-if="selected"
                class="user-sheet"
                data-swipe-close="sheet"
                :style="sheetStyle"
                @touchstart.passive="sheetTouchStart"
                @touchmove="sheetTouchMove"
                @touchend="sheetTouchEnd"
                @touchcancel="sheetTouchEnd"
            >
                <div class="sheet-handle"></div>
                <div class="tw-flex tw-items-start tw-justify-between tw-gap-2 tw-px-4 tw-pt-1 tw-pb-2">
                    <div class="tw-min-w-0">
                        <div class="adf-heading tw-text-xl tw-leading-7 tw-truncate">{{ selected.fio }}</div>
                        <div class="tw-text-xs tw-mt-0.5 tw-text-text-muted dark:tw-text-dark-text-muted">
                            {{ selected.authorized_at ? 'вход ' + fmtDateTime(selected.authorized_at) : 'входа не было' }}
                        </div>
                    </div>
                    <v-btn icon="mdi-close" size="small" density="comfortable" variant="text" color="muted" class="tw-shrink-0 tw--mr-1" @click="sheet = false"/>
                </div>

                <div class="swipe-scroll user-sheet__body tw-px-4 tw-pb-4">
                    <!-- Телефон -->
                    <div class="tw-flex tw-items-center tw-justify-between tw-gap-2">
                        <Code v-if="selected.phone" :code="fmtPhone(selected.phone)" density="compact" class="tw-flex-1 tw-min-w-0"/>
                        <span v-else class="tw-flex-1 tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted">телефон не указан</span>
                        <v-btn
                            v-if="selected.phone"
                            icon="mdi-phone-outline"
                            size="small"
                            density="comfortable"
                            variant="text"
                            color="primary"
                            :href="'tel:+' + selected.phone"
                            title="Позвонить"
                        />
                    </div>

                    <!-- Роли -->
                    <div class="sheet-block tw-mt-3">
                        <div class="bp-overline tw-mb-1">Роли</div>
                        <div class="tw-text-sm" v-if="selected.roleNames">{{ selected.roleNames }}</div>
                        <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted" v-else>ролей нет</div>
                    </div>

                    <!-- Ссылка для входа -->
                    <div class="sheet-block tw-mt-3" v-if="!selected.deleted_at">
                        <div class="bp-overline tw-mb-2">Ссылка для входа</div>
                        <div v-if="selected.entry_code" class="tw-flex tw-gap-2">
                            <v-btn variant="outlined" color="primary" prepend-icon="mdi-content-copy" class="tw-flex-1" @click="copyLink(selected)">Скопировать</v-btn>
                            <v-btn variant="outlined" color="primary" prepend-icon="mdi-share-variant-outline" class="tw-flex-1" @click="shareLink(selected)">Отправить</v-btn>
                        </div>
                        <v-btn v-else variant="outlined" color="primary" prepend-icon="mdi-link-variant-plus" block @click="actUserCreateLink({uid: selected.id})">Создать ссылку</v-btn>
                    </div>

                    <!-- Действия -->
                    <div class="tw-flex tw-justify-between tw-gap-2 tw-mt-4 tw--mx-2">
                        <v-btn variant="text" color="muted" prepend-icon="mdi-pencil-outline" @click="openEdit(selected)">Редактировать</v-btn>
                        <v-btn v-if="!selected.deleted_at" variant="text" color="error" prepend-icon="mdi-delete-outline" @click="removeUser(selected)">Удалить</v-btn>
                    </div>
                </div>
            </v-card>
        </v-bottom-sheet>

        <!-- Создание / редактирование -->
        <v-dialog v-model="editDialog" width="420" max-width="calc(100vw - 32px)">
            <v-card class="edit-dialog">
                <div class="tw-flex tw-items-center tw-justify-between tw-gap-2 tw-mb-2">
                    <div class="adf-heading tw-text-xl tw-leading-7">{{ form.id ? 'Редактирование пользователя' : 'Новый пользователь' }}</div>
                    <v-btn icon="mdi-close" size="small" density="comfortable" variant="text" color="muted" class="tw--mr-1" @click="editDialog = false"/>
                </div>
                <v-text-field v-model="form.surname" label="Фамилия" autofocus/>
                <v-text-field v-model="form.name" label="Имя"/>
                <v-text-field v-model="form.patronymic" label="Отчество"/>
                <v-text-field v-model="form.phone" label="Телефон" type="tel" placeholder="+7 900 000-00-00"/>
                <div class="bp-overline tw-mt-2 tw-mb-1">Роли</div>
                <v-checkbox
                    v-for="role in roleItems"
                    :key="role.name"
                    v-model="form.roles"
                    :value="role.name"
                    :label="role.display_name"
                    color="primary"
                    hide-details
                    density="compact"
                />
                <div class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted tw-mt-2 tw-min-h-[16px]">{{ formHint }}</div>
                <div class="tw-flex tw-justify-end tw-gap-2 tw-mt-4">
                    <v-btn variant="text" color="muted" @click="editDialog = false">Отмена</v-btn>
                    <v-btn color="primary" :disabled="!canSave" :loading="saving" @click="save">{{ form.id ? 'Сохранить' : 'Создать' }}</v-btn>
                </div>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import Code from "../../components/Code.vue";
import ListRow from "../../components/ListRow.vue";
import swipeSheet from "../../mixins/swipeSheet.js";
import normalize from "../../utils/normalize.js";
import {copyText} from "../../utils/clipboard.js";

const PAGE = 50;

export default {
    name: "Users",
    components: {Code, ListRow},
    mixins: [swipeSheet],
    data() {
        return {
            search: '',
            roleFilter: [],
            sortBy: [{key: 'fio', order: 'asc'}],
            // Сортирует computed filtered (до среза для автоподгрузки); таблице самой сортировать нельзя
            noSort: {fio: () => 0, roleNames: () => 0, authorized_at: () => 0},
            shown: PAGE,
            observer: null,
            sheet: false,
            selectedId: null,
            editDialog: false,
            saving: false,
            form: {id: null, surname: '', name: '', patronymic: '', phone: '', roles: []},
        }
    },
    watch: {
        // Новый запрос или фильтр — список начинается с начала
        search() { this.resetShown(); },
        roleFilter() { this.resetShown(); },
        sortBy() { this.resetShown(); },
    },
    computed: {
        ...mapGetters('users', ['getUsers', 'getRoles', 'getLoad']),
        // До загрузки стор отдаёт null, а v-select на null падает с «t is not iterable»
        roleItems() {
            return this.getRoles ?? [];
        },
        users() {
            return (this.getUsers ?? []).map(u => {
                const fio = [u.surname, u.name, u.patronymic].filter(Boolean).join(' ');
                return {
                    ...u,
                    fio: fio || 'Удалённый пользователь',
                    roleNames: (u.roles ?? []).map(r => r.display_name).join(', '),
                };
            });
        },
        subtitle() {
            const n = this.users.length;
            if (!n) return 'Пользователей пока нет';
            const entered = this.users.filter(u => u.authorized_at).length;
            return `${n} ${this.plural(n, ['пользователь', 'пользователя', 'пользователей'])}, заходили ${entered}`;
        },
        headers() {
            return [
                {title: '', key: 'authorized_at', width: 40},
                {title: 'ФИО', key: 'fio'},
                {title: 'Роли', key: 'roleNames', width: 260},
                {title: 'Телефон', key: 'phone', sortable: false, width: 190},
                {title: 'Ссылка для входа', key: 'link', sortable: false, width: 170},
                {title: '', key: 'actions', align: 'end', sortable: false, width: 100},
            ];
        },
        filtered() {
            let list = this.users;

            if (this.roleFilter?.length) {
                list = list.filter(u => (u.roles ?? []).some(r => this.roleFilter.includes(r.name)));
            }

            // Поиск подстрокой, а не регулярным выражением: иначе «+7» или «(» ломают фильтр
            const q = (this.search ?? '').trim().toLowerCase();
            const digits = q.replace(/\D/g, '');
            if (digits.length >= 3) {
                list = list.filter(u => (u.phone ?? '').includes(digits));
            } else if (q) {
                const words = q.split(/\s+/);
                list = list.filter(u => {
                    const fio = u.fio.toLowerCase();
                    return words.every(w => fio.includes(w));
                });
            }

            const {key, order} = this.sortBy[0] ?? {key: 'fio', order: 'asc'};
            const dir = order === 'desc' ? -1 : 1;
            return [...list].sort((a, b) => {
                const x = a[key] ?? '';
                const y = b[key] ?? '';
                return dir * String(x).localeCompare(String(y), 'ru');
            });
        },
        visibleItems() {
            return this.filtered.slice(0, this.shown);
        },
        selected() {
            return this.users.find(u => u.id === this.selectedId) ?? null;
        },
        formPhone() {
            return normalize.phone(this.form.phone ?? '');
        },
        phoneTaken() {
            return this.users.some(u => u.phone === this.formPhone && u.id !== this.form.id);
        },
        canSave() {
            return this.formPhone.length === 11
                && this.form.surname.trim().length > 1
                && this.form.name.trim().length > 1
                && this.form.roles.length > 0
                && !this.phoneTaken;
        },
        formHint() {
            if (this.phoneTaken) return 'Пользователь с таким телефоном уже есть';
            if (this.form.phone && this.formPhone.length !== 11) return 'Телефон: 11 цифр, например +7 900 000-00-00';
            if (!this.form.roles.length) return 'Выберите хотя бы одну роль';
            if (!this.canSave) return 'Заполните фамилию, имя и телефон';
            return '';
        },
    },
    methods: {
        ...mapActions('users', [
            'actReqwestUsers',
            'actReqwestUserRoles',
            'actUserCreate',
            'actUserCreateLink',
            'actUserSave',
            'actUserDelete',
        ]),
        async loadAll() {
            await this.actReqwestUsers();
            await this.actReqwestUserRoles();
        },
        resetShown() {
            this.shown = PAGE;
            this.$nextTick(() => this.fillViewport());
        },
        // Если после отрисовки маячок всё ещё виден — добавляем ещё страницу
        fillViewport() {
            const el = this.$refs.sentinel;
            if (!el) return;
            const r = el.getBoundingClientRect();
            if (r.top <= window.innerHeight && this.shown < this.filtered.length) {
                this.shown += PAGE;
                this.$nextTick(() => this.fillViewport());
            }
        },
        openSheet(item) {
            this.selectedId = item.id;
            this.openDialog('sheet');
        },
        openCreate() {
            this.form = {id: null, surname: '', name: '', patronymic: '', phone: '', roles: []};
            this.editDialog = true;
        },
        openEdit(item) {
            this.form = {
                id: item.id,
                surname: item.surname ?? '',
                name: item.name ?? '',
                patronymic: item.patronymic ?? '',
                phone: item.phone ? this.fmtPhone(item.phone) : '',
                roles: (item.roles ?? []).map(r => r.name),
            };
            this.editDialog = true;
        },
        async save() {
            this.saving = true;
            try {
                const payload = {
                    surname: normalize.name(this.form.surname),
                    name: normalize.name(this.form.name),
                    patronymic: normalize.name(this.form.patronymic),
                    phone: this.formPhone,
                };
                let res;
                if (this.form.id) {
                    // Сохранение ждёт роли объектами, создание — именами
                    res = await this.actUserSave({
                        id: this.form.id,
                        ...payload,
                        roles: this.form.roles.map(name => ({name})),
                    });
                } else {
                    res = await this.actUserCreate({...payload, roles: this.form.roles});
                }
                if (res !== false) {
                    this.editDialog = false;
                    if (!this.form.id) this.notify('Пользователь создан');
                    await this.loadAll();
                }
            } finally {
                this.saving = false;
            }
        },
        async removeUser(item) {
            const ok = await this.$confirm({
                title: 'Удалить пользователя?',
                text: `${item.fio}: будут стёрты имя, телефонная ссылка и все роли. Восстановить данные будет нельзя.`,
                okText: 'Удалить',
                color: 'error',
            });
            if (!ok) return;
            const res = await this.actUserDelete(item.id);
            if (res !== false) {
                this.sheet = false;
                this.notify('Пользователь удалён');
                await this.loadAll();
            }
        },
        async copyLink(item) {
            if (await copyText(item.entry_code)) {
                this.notify('Ссылка скопирована');
            } else {
                this.notify('Не удалось скопировать', 'error');
            }
        },
        async shareLink(item) {
            if (navigator.share) {
                try {
                    await navigator.share({title: item.fio, text: item.entry_code});
                } catch (e) {
                    // отмену шаринга пользователем не считаем ошибкой
                }
            } else {
                await this.copyLink(item);
            }
        },
        notify(text, color = 'success') {
            this.$store.commit('app/setSnackBar', {text, color});
        },
        fmtPhone(phone) {
            const d = String(phone).replace(/\D/g, '');
            if (d.length === 11) return `+${d[0]} ${d.slice(1, 4)} ${d.slice(4, 7)}-${d.slice(7, 9)}-${d.slice(9)}`;
            return '+' + d;
        },
        fmtDate(value) {
            if (!value) return '—';
            const [y, m, d] = String(value).slice(0, 10).split('-');
            return `${d}.${m}.${y}`;
        },
        fmtDateTime(value) {
            if (!value) return '—';
            const s = String(value);
            return this.fmtDate(s) + (s.length > 10 ? ' ' + s.slice(11, 16) : '');
        },
        plural(n, forms) {
            const abs = Math.abs(n) % 100;
            const last = abs % 10;
            if (abs > 10 && abs < 20) return forms[2];
            if (last > 1 && last < 5) return forms[1];
            if (last === 1) return forms[0];
            return forms[2];
        },
    },
    async created() {
        await this.loadAll();
    },
    mounted() {
        this.observer = new IntersectionObserver(entries => {
            if (entries[0].isIntersecting && this.shown < this.filtered.length) {
                this.shown += PAGE;
            }
        });
        this.observer.observe(this.$refs.sentinel);
    },
    unmounted() {
        this.observer?.disconnect();
    },
}
</script>

<style lang="scss" scoped>
.create-fab {
    position: fixed;
    right: 16px;
    bottom: 16px;
    z-index: 5;
}

/* Окно пользователя: шапка фиксирована, тело прокручивается */
.user-sheet.v-card {
    display: flex;
    flex-direction: column;
    max-height: 85vh;
    border-radius: var(--bp-radius-md, 8px) var(--bp-radius-md, 8px) 0 0 !important;
}

.user-sheet__body {
    overflow-y: auto;
}

.sheet-block {
    padding: 12px;
    border: 1px solid var(--bp-border);
    border-radius: var(--bp-radius-md, 8px);
}
</style>
