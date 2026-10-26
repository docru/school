<template>
    <!-- Нижний отступ на мобильном — чтобы плавающая кнопка не закрывала последнюю строку -->
    <div class="disciples-page tw-pb-24 md:tw-pb-0">
        <!-- Шапка раздела (только десктоп): на мобильном название уже есть в хлебных крошках -->
        <div v-if="!isMobile" class="tw-flex tw-items-start tw-justify-between tw-gap-3 tw-mb-4">
            <div class="tw-min-w-0">
                <h1 class="adf-heading tw-text-2xl tw-leading-8 tw-m-0">Ученики</h1>
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
                <v-btn color="primary" prepend-icon="mdi-plus" @click="openCreate">Новый ученик</v-btn>
            </div>
        </div>

        <!-- Поиск и фильтр -->
        <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center tw-gap-3 tw-mb-4">
            <v-text-field
                v-model="search"
                label="Поиск по имени или телефону"
                prepend-inner-icon="mdi-magnify"
                clearable
                class="!tw-my-0 md:tw-max-w-sm tw-w-full"
            />
            <v-btn-toggle
                v-model="filter"
                mandatory
                density="comfortable"
                variant="outlined"
                divided
                class="status-filter tw-flex md:tw-inline-flex"
            >
                <v-btn v-for="f in filterItems" :key="f.val" :value="f.val">
                    {{ f.title }}
                    <span class="tab-count">{{ counts[f.val] }}</span>
                </v-btn>
            </v-btn-toggle>
        </div>

        <v-progress-linear v-if="getLoad && !users.length" indeterminate color="primary" class="tw-mb-4"/>

        <!-- Таблица (десктоп) -->
        <v-card v-if="!isMobile && filtered.length">
            <v-data-table
                class="disciples-table"
                :headers="headers"
                :items="visibleItems"
                :sort-by="sortBy"
                @update:sort-by="sortBy = $event"
                :custom-key-sort="noSort"
                items-per-page="-1"
                hide-default-footer
            >
                <template v-slot:item.authorized_at="{item}">
                    <v-tooltip :text="item.authorized_at ? 'Последний вход ' + fmtDateTime(item.authorized_at) : 'Входа ещё не было'">
                        <template v-slot:activator="{ props }">
                            <span v-bind="props" class="entry-dot" :class="{'entry-dot--on': item.authorized_at}"></span>
                        </template>
                    </v-tooltip>
                </template>
                <template v-slot:item.fio="{item}">
                    <span class="tw-font-medium">{{ item.fio }}</span>
                </template>
                <template v-slot:item.phone="{item}">
                    <Code v-if="item.phone" :code="fmtPhone(item.phone)" density="compact" class="tw-w-max"/>
                    <span v-else class="tw-text-text-muted dark:tw-text-dark-text-muted">—</span>
                </template>
                <template v-slot:item.groupNames="{item}">
                    <span v-if="item.groupNames">{{ item.groupNames }}</span>
                    <span v-else class="tw-text-text-muted dark:tw-text-dark-text-muted">без группы</span>
                </template>
                <template v-slot:item.lesson="{item}">
                    <div v-for="a in openLessons(item)" :key="a.gsd_id" class="tw-flex tw-items-center tw-gap-1">
                        <v-checkbox
                            color="primary"
                            hide-details
                            density="compact"
                            class="lesson-check"
                            v-model="attendancesAll"
                            :value="a.uid + '_' + a.gid"
                            @change="setAttendance({groupSchoolDayId: a.gsd_id, userId: a.uid})"
                        />
                        <span class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted">{{ fmtDate(a.date) }}</span>
                    </div>
                    <span v-if="!openLessons(item).length" class="tw-text-text-muted dark:tw-text-dark-text-muted">—</span>
                </template>
                <template v-slot:item.link="{item}">
                    <v-btn
                        v-if="item.entry_code"
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
                        <v-btn icon="mdi-delete-outline" variant="text" color="error" size="small" title="Удалить" @click="removeUser(item)"/>
                    </div>
                </template>
            </v-data-table>
        </v-card>

        <!-- Список (телефон) -->
        <v-card v-else-if="filtered.length">
            <div
                v-for="item in visibleItems"
                :key="item.id"
                class="user-row"
                @click="openSheet(item)"
            >
                <span class="entry-dot tw-shrink-0" :class="{'entry-dot--on': item.authorized_at}"></span>
                <div class="tw-min-w-0 tw-flex-1">
                    <div class="tw-text-[15px] tw-font-medium tw-truncate">{{ item.fio }}</div>
                    <div class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted tw-truncate">
                        <span v-if="item.phone">{{ fmtPhone(item.phone) }}</span>
                        <span v-else>телефон не указан</span>
                        <span> · {{ item.groupNames || 'без группы' }}</span>
                    </div>
                </div>
                <v-icon size="20" color="muted" class="tw-shrink-0">mdi-chevron-right</v-icon>
            </div>
        </v-card>

        <!-- Пустое состояние -->
        <v-card v-else-if="!getLoad">
            <div class="tw-p-8 tw-text-center">
                <v-icon size="40" color="muted">mdi-account-search-outline</v-icon>
                <div class="adf-heading tw-text-lg tw-mt-2 tw-mb-1">{{ users.length ? 'Никого не найдено' : 'Учеников пока нет' }}</div>
                <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted" v-if="users.length">
                    Измените запрос или фильтр
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
            title="Новый ученик"
            class="create-fab"
            @click="openCreate"
        />

        <!-- Окно ученика (телефон) -->
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
                            {{ selected.groupNames || 'без группы' }}
                            · {{ selected.authorized_at ? 'вход ' + fmtDateTime(selected.authorized_at) : 'входа не было' }}
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

                    <!-- Открытое занятие: отметка посещения -->
                    <div v-for="a in openLessons(selected)" :key="a.gsd_id" class="sheet-block tw-mt-3">
                        <div class="bp-overline">Занятие {{ fmtDate(a.date) }} · {{ a.group_name }}</div>
                        <v-checkbox
                            color="primary"
                            hide-details
                            density="compact"
                            label="Присутствует"
                            v-model="attendancesAll"
                            :value="a.uid + '_' + a.gid"
                            @change="setAttendance({groupSchoolDayId: a.gsd_id, userId: a.uid})"
                        />
                    </div>

                    <!-- Ссылка для входа -->
                    <div class="sheet-block tw-mt-3">
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
                        <v-btn variant="text" color="error" prepend-icon="mdi-delete-outline" @click="removeUser(selected)">Удалить</v-btn>
                    </div>
                </div>
            </v-card>
        </v-bottom-sheet>

        <!-- Создание / редактирование -->
        <v-dialog v-model="editDialog" width="420" max-width="calc(100vw - 32px)">
            <v-card class="edit-dialog">
                <div class="tw-flex tw-items-center tw-justify-between tw-gap-2 tw-mb-2">
                    <div class="adf-heading tw-text-xl tw-leading-7">{{ form.id ? 'Редактирование ученика' : 'Новый ученик' }}</div>
                    <v-btn icon="mdi-close" size="small" density="comfortable" variant="text" color="muted" class="tw--mr-1" @click="editDialog = false"/>
                </div>
                <v-text-field v-model="form.surname" label="Фамилия" autofocus/>
                <v-text-field v-model="form.name" label="Имя"/>
                <v-text-field v-model="form.patronymic" label="Отчество"/>
                <v-text-field v-model="form.phone" label="Телефон" type="tel" placeholder="+7 900 000-00-00"/>
                <div class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted tw-mt-1 tw-min-h-[16px]">{{ formHint }}</div>
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
import swipeSheet from "../../mixins/swipeSheet.js";
import normalize from "../../utils/normalize.js";
import {copyText} from "../../utils/clipboard.js";

const PAGE = 50;

export default {
    name: "Disciples",
    components: {Code},
    mixins: [swipeSheet],
    data() {
        return {
            search: '',
            filter: 'all',
            filterItems: [
                {title: 'Все', val: 'all'},
                {title: 'В группе', val: 'inGroup'},
                {title: 'Без группы', val: 'noGroup'},
            ],
            sortBy: [{key: 'fio', order: 'asc'}],
            // Сортирует computed filtered (до среза для автоподгрузки); таблице самой сортировать нельзя
            noSort: {fio: () => 0, groupNames: () => 0, authorized_at: () => 0},
            shown: PAGE,
            observer: null,
            sheet: false,
            selectedId: null,
            editDialog: false,
            saving: false,
            form: {id: null, surname: '', name: '', patronymic: '', phone: ''},
        }
    },
    watch: {
        // Новый запрос или фильтр — список начинается с начала
        search() { this.resetShown(); },
        filter() { this.resetShown(); },
        sortBy() { this.resetShown(); },
    },
    computed: {
        ...mapGetters('users', ['getUsers', 'getLoad']),
        ...mapGetters('administrator', ['getAttendancesAll']),
        // Строки отметок по ученику: открытые группы, где он активен
        attendanceByUid() {
            const res = {};
            for (const k in this.getAttendancesAll) {
                const a = this.getAttendancesAll[k];
                (res[a.uid] ??= []).push(a);
            }
            return res;
        },
        users() {
            return (this.getUsers ?? []).map(u => {
                const groups = this.attendanceByUid[u.id] ?? [];
                return {
                    ...u,
                    fio: [u.surname, u.name, u.patronymic].filter(Boolean).join(' '),
                    groups,
                    groupNames: [...new Set(groups.map(g => g.group_name))].join(', '),
                };
            });
        },
        counts() {
            return {
                all: this.users.length,
                inGroup: this.users.filter(u => u.groups.length).length,
                noGroup: this.users.filter(u => !u.groups.length).length,
            };
        },
        subtitle() {
            const n = this.counts.all;
            if (!n) return 'Учеников пока нет';
            return `${n} ${this.plural(n, ['ученик', 'ученика', 'учеников'])}, в открытых группах ${this.counts.inGroup}`;
        },
        filtered() {
            let list = this.users;
            if (this.filter === 'inGroup') list = list.filter(u => u.groups.length);
            if (this.filter === 'noGroup') list = list.filter(u => !u.groups.length);

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
                const av = a[key] ?? '', bv = b[key] ?? '';
                return String(av).localeCompare(String(bv), 'ru') * dir;
            });
        },
        hasOpenLessons() {
            return this.users.some(u => this.openLessons(u).length);
        },
        headers() {
            return [
                {title: '', key: 'authorized_at', width: 40},
                {title: 'ФИО', key: 'fio'},
                {title: 'Телефон', key: 'phone', sortable: false, width: 190},
                {title: 'Группа', key: 'groupNames', width: 200},
                // Колонка с отметками нужна только когда у кого-то идёт занятие
                ...(this.hasOpenLessons ? [{title: 'Занятие', key: 'lesson', sortable: false, width: 160}] : []),
                {title: 'Ссылка для входа', key: 'link', sortable: false, width: 170},
                {title: '', key: 'actions', align: 'end', sortable: false, width: 100},
            ];
        },
        visibleItems() {
            return this.filtered.slice(0, this.shown);
        },
        selected() {
            return this.users.find(u => u.id === this.selectedId) ?? null;
        },
        attendancesAll: {
            get() {
                const res = [];
                for (const k in this.getAttendancesAll) {
                    const a = this.getAttendancesAll[k];
                    if (a.attendance) res.push(a.uid + '_' + a.gid);
                }
                return res;
            },
            set() {},
        },
        canShare() {
            return typeof navigator !== 'undefined' && !!navigator.share;
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
                && !this.phoneTaken;
        },
        formHint() {
            if (this.phoneTaken) return 'Ученик с таким телефоном уже есть';
            if (this.form.phone && this.formPhone.length !== 11) return 'Телефон: 11 цифр, например +7 900 000-00-00';
            if (!this.canSave) return 'Заполните фамилию, имя и телефон';
            return '';
        },
    },
    methods: {
        ...mapActions('users', ['actReqwestUsers', 'actUserCreateLink', 'actUserDelete', 'actUserSave', 'actUserCreate']),
        ...mapActions('administrator', ['actRequestAttendancesAll', 'actSetAttendance']),
        async loadAll() {
            await this.actReqwestUsers({role: 'disciple'});
            await this.actRequestAttendancesAll();
        },
        resetShown() {
            this.shown = PAGE;
            this.$nextTick(() => this.fillViewport());
        },
        // Если после отрисовки маячок всё ещё виден — добавляем ещё страницу
        fillViewport() {
            const el = this.$refs.sentinel;
            if (!el || this.shown >= this.filtered.length) return;
            if (el.getBoundingClientRect().top < window.innerHeight + 400) {
                this.shown += PAGE;
                this.$nextTick(() => this.fillViewport());
            }
        },
        openLessons(item) {
            return item.groups.filter(a => a.status_day === 'open');
        },
        async setAttendance(data) {
            await this.actSetAttendance(data);
            await this.actRequestAttendancesAll();
        },
        openSheet(item) {
            this.selectedId = item.id;
            this.openDialog('sheet');
        },
        openCreate() {
            this.form = {id: null, surname: '', name: '', patronymic: '', phone: ''};
            this.editDialog = true;
        },
        openEdit(item) {
            this.form = {
                id: item.id,
                surname: item.surname ?? '',
                name: item.name ?? '',
                patronymic: item.patronymic ?? '',
                phone: item.phone ? this.fmtPhone(item.phone) : '',
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
                    res = await this.actUserSave({id: this.form.id, ...payload});
                } else {
                    res = await this.actUserCreate({...payload, role: 'disciple'});
                }
                if (res !== false) {
                    this.editDialog = false;
                    // При сохранении «Сохранено» показывает сам сервер
                    if (!this.form.id) this.notify('Ученик добавлен');
                    await this.loadAll();
                }
            } finally {
                this.saving = false;
            }
        },
        async removeUser(item) {
            const ok = await this.$confirm({
                title: 'Удалить ученика?',
                text: `${item.fio} потеряет доступ в школу.`,
                okText: 'Удалить',
                color: 'error',
            });
            if (!ok) return;
            const res = await this.actUserDelete(item.id);
            if (res !== false) {
                this.sheet = false;
                this.notify('Ученик удалён');
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
        shareText(item) {
            return `${item.name}, ваша ссылка для входа в школу: ${item.entry_code}`;
        },
        async shareLink(item) {
            // Системное «Поделиться» есть только по HTTPS; иначе хотя бы копируем ссылку
            if (!this.canShare) {
                await copyText(item.entry_code);
                this.notify('Отправка работает только по HTTPS. Ссылка скопирована', 'warning');
                return;
            }
            try {
                await navigator.share({title: 'Вход в школу', text: this.shareText(item)});
            } catch (e) {
                // пользователь закрыл окно отправки — это не ошибка
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
    created() {
        this.loadAll();
    },
    mounted() {
        // Подгрузка при прокрутке: маячок под списком попадает в зону видимости — показываем ещё 50
        this.observer = new IntersectionObserver(entries => {
            if (entries.some(e => e.isIntersecting)) this.fillViewport();
        }, {rootMargin: '400px 0px'});
        this.observer.observe(this.$refs.sentinel);
    },
    beforeUnmount() {
        this.observer?.disconnect();
    },
}
</script>

<style lang="scss" scoped>
.user-row {
    display: flex;
    align-items: center;
    gap: 12px;
    min-height: 56px;
    padding: 8px 12px 8px 16px;
    border-bottom: 1px solid var(--bp-border);
    cursor: pointer;

    &:last-child {
        border-bottom: 0;
    }

    &:active {
        background: var(--bp-surface-muted);
    }
}

/* Точка «заходил / не заходил» */
.entry-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--bp-border-control);

    &--on {
        background: #22c55e;
    }
}

.status-filter {
    border-color: var(--bp-border-control) !important;
    border-radius: var(--bp-radius-sm, 4px) !important;

    :deep(.v-btn) {
        border-color: var(--bp-border-control) !important;
        border-radius: 0 !important;
        color: var(--bp-text-muted);

        @media (max-width: 767px) {
            flex: 1 1 0;
            min-width: 0;
            padding: 0 4px;
            font-size: 12px;

            .tab-count {
                margin-left: 4px;
                padding: 0 5px;
            }
        }
    }

    :deep(.v-btn--active) {
        background: var(--bp-surface-muted);
        color: var(--bp-text-primary);

        .tab-count {
            background: var(--bp-surface-card);
        }
    }
}

.tab-count {
    margin-left: 6px;
    font-size: 12px;
    line-height: 16px;
    padding: 0 6px;
    border-radius: 999px;
    background: var(--bp-surface-muted);
    color: var(--bp-text-muted);
}

.create-fab {
    position: fixed;
    right: 16px;
    bottom: 16px;
    z-index: 5;
}

.lesson-check :deep(.v-selection-control) {
    min-height: 32px;
}

/* Окно ученика: шапка фиксирована, тело прокручивается */
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
