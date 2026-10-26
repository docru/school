<template>
    <div class="groups-page">
        <!-- Шапка раздела -->
        <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-end md:tw-justify-between tw-gap-3 tw-mb-4">
            <div>
                <h1 class="adf-heading tw-text-2xl tw-leading-8 tw-m-0">Группы</h1>
                <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted tw-mt-1">
                    {{ subtitle }}
                </div>
            </div>
            <div class="tw-flex tw-items-center tw-gap-2">
                <v-btn
                    icon="mdi-refresh"
                    variant="text"
                    color="muted"
                    :loading="load"
                    title="Обновить список"
                    @click="actRequestGroups"
                />
                <v-btn color="primary" class="tw-flex-1 md:tw-flex-none" prepend-icon="mdi-plus" @click="dialog = true">
                    Создать группу
                </v-btn>
            </div>
        </div>

        <!-- Фильтр по статусу -->
        <v-btn-toggle
            v-model="filterItemSelected"
            mandatory
            density="comfortable"
            variant="outlined"
            divided
            class="status-filter tw-mb-4 tw-w-full md:tw-w-auto"
        >
            <v-btn v-for="f in filterItems" :key="f.val" :value="f.val" class="tw-flex-1 md:tw-flex-none">
                {{ f.title }}
                <span class="tab-count">{{ counts[f.val] }}</span>
            </v-btn>
        </v-btn-toggle>

        <v-progress-linear v-if="load && !groups.length" indeterminate color="primary" class="tw-mb-4"/>

        <!-- Карточки групп -->
        <div v-if="groups.length" class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 xl:tw-grid-cols-3 tw-gap-4">
            <v-card
                v-for="g in groups"
                :key="g.id"
                class="group-card"
                :to="{name: 'AdministratorDetailGroup', params: {id: g.id}}"
            >
                <div class="tw-p-4 tw-flex tw-flex-col tw-gap-4 tw-h-full">
                    <!-- Название, курс, статус -->
                    <div class="tw-flex tw-items-start tw-justify-between tw-gap-3">
                        <div class="tw-min-w-0">
                            <div class="adf-heading tw-text-xl tw-leading-7 tw-truncate">{{ g.name }}</div>
                            <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted tw-truncate">
                                {{ g.course_name || 'Курс не задан' }}
                            </div>
                        </div>
                        <v-chip size="small" label :color="statusColor(g)" class="tw-shrink-0">
                            {{ statusTitle(g) }}
                        </v-chip>
                    </div>

                    <!-- Прогресс по учебным дням -->
                    <div>
                        <div class="tw-flex tw-justify-between tw-items-baseline tw-mb-1">
                            <span class="bp-overline">Занятия</span>
                            <span class="tw-text-sm">
                                <span class="tw-font-medium">{{ g.days_closed }}</span>
                                <span class="tw-text-text-muted dark:tw-text-dark-text-muted"> из {{ g.days_total }}</span>
                            </span>
                        </div>
                        <v-progress-linear
                            :model-value="progress(g)"
                            height="4"
                            rounded
                            color="primary"
                            bg-color="border"
                            bg-opacity="1"
                        />
                    </div>

                    <!-- Показатели -->
                    <div class="tw-grid tw-grid-cols-3 tw-gap-3">
                        <div>
                            <div class="bp-overline">Ученики</div>
                            <div class="tw-text-sm tw-mt-0.5">
                                <span class="tw-font-medium">{{ g.disciples_active }}</span>
                                <span v-if="g.disciples_expelled" class="tw-text-text-muted dark:tw-text-dark-text-muted">
                                    · отчисл. {{ g.disciples_expelled }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <div class="bp-overline">Старт</div>
                            <div class="tw-text-sm tw-mt-0.5">{{ fmtDate(g.first_day) }}</div>
                        </div>
                        <div>
                            <div class="bp-overline">{{ g.open_day ? 'Текущее' : 'Последнее' }}</div>
                            <div class="tw-text-sm tw-mt-0.5">{{ fmtDate(g.open_day || g.last_day) }}</div>
                        </div>
                    </div>

                    <!-- Учителя -->
                    <div class="tw-mt-auto tw-pt-3 tw-border-t tw-border-border dark:tw-border-dark-border">
                        <div class="bp-overline">Учителя</div>
                        <div class="tw-text-sm tw-mt-0.5" v-if="g.teachers.length">{{ g.teachers.join(', ') }}</div>
                        <div class="tw-text-sm tw-mt-0.5 tw-text-text-muted dark:tw-text-dark-text-muted" v-else>
                            не назначены
                        </div>
                    </div>
                </div>
            </v-card>
        </div>

        <!-- Пустое состояние -->
        <v-card v-else-if="!load">
            <div class="tw-p-8 tw-text-center">
                <v-icon size="40" color="muted">mdi-account-group-outline</v-icon>
                <div class="adf-heading tw-text-lg tw-mt-2">{{ emptyTitle }}</div>
                <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted tw-mt-1" v-if="filterItemSelected !== 'archived'">
                    Создайте группу и назначьте в неё учителей и учеников
                </div>
            </div>
        </v-card>

        <!-- Диалог создания группы -->
        <v-dialog v-model="dialog" width="480">
            <v-card>
                <v-card-title class="tw-flex tw-items-center tw-justify-between">
                    <span>Новая группа</span>
                    <v-btn icon="mdi-close" variant="text" color="muted" size="small" @click="dialog = false"/>
                </v-card-title>
                <v-card-text>
                    <v-text-field label="Название группы" v-model="newGroup.name" autofocus/>
                    <v-autocomplete
                        label="Курс"
                        :items="courses"
                        item-title="name"
                        item-value="id"
                        v-model="newGroup.course_id"
                    />
                </v-card-text>
                <v-card-actions class="tw-px-6 tw-pb-4">
                    <v-spacer/>
                    <v-btn variant="text" color="muted" @click="dialog = false">Отмена</v-btn>
                    <v-btn color="primary" :disabled="!newGroup.name || !newGroup.course_id" @click="createGroup">
                        Создать
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";

export default {
    name: 'GroupsList',
    data() {
        return {
            dialog: false,
            newGroup: {name: '', course_id: null},
            filterItems: [
                {title: 'Открытые', val: 'open'},
                {title: 'Архив', val: 'archived'},
                {title: 'Все', val: 'all'},
            ],
            filterItemSelected: 'open',
        }
    },
    computed: {
        ...mapGetters('administrator', {
            getGroups: 'getGroups',
            courses: 'getCourses',
            load: 'getLoad',
        }),
        groups() {
            return this.getGroups.filter((g) => {
                return this.filterItemSelected === 'all' || g.status === this.filterItemSelected;
            });
        },
        counts() {
            return {
                open: this.getGroups.filter(g => g.status === 'open').length,
                archived: this.getGroups.filter(g => g.status === 'archived').length,
                all: this.getGroups.length,
            };
        },
        subtitle() {
            const open = this.counts.open;
            const disciples = this.getGroups
                .filter(g => g.status === 'open')
                .reduce((sum, g) => sum + (g.disciples_active ?? 0), 0);
            if (!open) return 'Открытых групп нет';
            return `${open} ${this.plural(open, ['открытая группа', 'открытые группы', 'открытых групп'])}, `
                + `${disciples} ${this.plural(disciples, ['ученик', 'ученика', 'учеников'])}`;
        },
        emptyTitle() {
            return {
                open: 'Открытых групп нет',
                archived: 'В архиве пусто',
                all: 'Групп пока нет',
            }[this.filterItemSelected];
        },
    },
    methods: {
        ...mapActions('administrator', ['actRequestGroups', 'actCreateGroup', 'actReqwestCourses']),
        statusTitle(g) {
            return g.status === 'open' ? 'Идёт' : 'В архиве';
        },
        statusColor(g) {
            return g.status === 'open' ? 'success' : 'muted';
        },
        progress(g) {
            if (!g.days_total) return 0;
            return Math.min(100, Math.round(g.days_closed / g.days_total * 100));
        },
        // Дата из 'Y-m-d' в 'd.m.Y'
        fmtDate(value) {
            if (!value) return '—';
            const [y, m, d] = String(value).slice(0, 10).split('-');
            return `${d}.${m}.${y}`;
        },
        plural(n, forms) {
            const abs = Math.abs(n) % 100;
            const last = abs % 10;
            if (abs > 10 && abs < 20) return forms[2];
            if (last > 1 && last < 5) return forms[1];
            if (last === 1) return forms[0];
            return forms[2];
        },
        async createGroup() {
            await this.actCreateGroup({...this.newGroup});
            this.dialog = false;
            this.newGroup = {name: '', course_id: null};
        },
    },
    created() {
        this.actRequestGroups();
        this.actReqwestCourses();
    }
}
</script>

<style lang="scss" scoped>
.group-card {
    transition: border-color .15s ease, transform .15s ease;

    &:hover {
        border-color: var(--bp-accent) !important;
    }
}

.status-filter {
    border-color: var(--bp-border-control) !important;
    border-radius: var(--bp-radius-sm, 4px) !important;

    :deep(.v-btn) {
        border-color: var(--bp-border-control) !important;
        border-radius: 0 !important;
        color: var(--bp-text-muted);
    }

    :deep(.v-btn--active) {
        background: var(--bp-surface-muted);
        color: var(--bp-text-primary);
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
</style>
