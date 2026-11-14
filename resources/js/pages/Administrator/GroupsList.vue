<template>
    <!-- Нижний отступ на мобильном — чтобы плавающая кнопка не закрывала последнюю карточку -->
    <div class="groups-page tw-pb-24 md:tw-pb-0">
        <!-- Шапка раздела (только десктоп): на мобильном название раздела уже есть в хлебных крошках,
             а счётчики групп видны на фильтре, поэтому там шапка не показывается ради экономии места -->
        <div v-if="!isMobile" class="tw-flex tw-items-start tw-justify-between tw-gap-3 tw-mb-4">
            <div class="tw-min-w-0">
                <h1 class="adf-heading tw-text-2xl tw-leading-8 tw-m-0">Группы</h1>
                <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted">
                    {{ subtitle }}
                </div>
            </div>
            <div class="tw-flex tw-items-center tw-gap-1 tw-shrink-0">
                <v-btn
                    v-if="!isMobile"
                    icon="mdi-refresh"
                    variant="text"
                    color="muted"
                    size="small"
                    :loading="load"
                    title="Обновить список"
                    @click="actRequestGroups"
                />
                <v-btn color="primary" prepend-icon="mdi-plus" @click="dialog = true">Создать группу</v-btn>
            </div>
        </div>

        <!-- Фильтр по статусу; справа на десктопе переключатель вида -->
        <div class="tw-flex tw-items-center tw-justify-between tw-gap-3 tw-mb-4">
            <FilterChips v-model="filterItemSelected" :items="filterItems" :counts="counts"/>

            <v-btn-toggle
                v-if="!isMobile"
                v-model="viewMode"
                mandatory
                density="comfortable"
                variant="outlined"
                divided
                class="view-toggle"
            >
                <v-btn value="cards" icon="mdi-view-grid-outline" title="Карточки"/>
                <v-btn value="table" icon="mdi-format-list-bulleted" title="Таблица"/>
            </v-btn-toggle>
        </div>

        <v-progress-linear v-if="load && !groups.length" indeterminate color="primary" class="tw-mb-4"/>

        <!-- Таблица групп (десктоп) -->
        <v-card v-if="groups.length && showTable">
            <v-data-table
                :headers="tableHeaders"
                :items="groups"
                :items-per-page="-1"
                hover
                class="groups-table"
                @click:row="(e, {item}) => $router.push({name: 'AdministratorDetailGroup', params: {id: item.id}})"
            >
                <template v-slot:item.name="{item}">
                    <span class="tw-font-medium">{{ item.name }}</span>
                </template>
                <template v-slot:item.status="{item}">
                    <v-chip size="small" label :color="statusColor(item)">{{ statusTitle(item) }}</v-chip>
                </template>
                <template v-slot:item.disciples_active="{item}">
                    <span class="tw-whitespace-nowrap">
                        {{ item.disciples_active }}
                        <span v-if="item.disciples_expelled" class="tw-text-text-muted dark:tw-text-dark-text-muted">
                            · отчисл. {{ item.disciples_expelled }}
                        </span>
                    </span>
                </template>
                <template v-slot:item.days_closed="{item}">
                    <div class="tw-flex tw-items-center tw-gap-2 tw-whitespace-nowrap">
                        <!-- У v-progress-linear ширина 100%, во flex она не сжимается — задаём ширину явно -->
                        <v-progress-linear
                            :model-value="progress(item)"
                            height="4"
                            rounded
                            color="primary"
                            bg-color="border"
                            bg-opacity="1"
                            class="table-progress"
                        />
                        <span class="tw-whitespace-nowrap">{{ item.days_closed }} / {{ item.days_total }}</span>
                    </div>
                </template>
                <template v-slot:item.first_day="{item}">{{ fmtDate(item.first_day) }}</template>
                <template v-slot:item.last_day="{item}">{{ fmtDate(item.open_day || item.last_day) }}</template>
                <template v-slot:item.teachers="{item}">
                    <span v-if="item.teachers.length">{{ item.teachers.join(', ') }}</span>
                    <span v-else class="tw-text-text-muted dark:tw-text-dark-text-muted">не назначены</span>
                </template>
                <template v-slot:bottom></template>
            </v-data-table>
        </v-card>

        <!-- Карточки групп -->
        <div v-else-if="groups.length" class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 xl:tw-grid-cols-3 tw-gap-4">
            <GroupCard
                v-for="g in groups"
                :key="g.id"
                :group="g"
                :to="{name: 'AdministratorDetailGroup', params: {id: g.id}}"
            />
        </div>

        <!-- Пустое состояние -->
        <v-card v-else-if="!load">
            <div class="tw-p-8 tw-text-center">
                <v-icon size="40" color="muted">mdi-account-group-outline</v-icon>
                <div class="adf-heading tw-text-lg tw-mt-2 tw-mb-1">{{ emptyTitle }}</div>
                <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted tw-mt-1" v-if="filterItemSelected !== 'archived'">
                    Создайте группу и назначьте в неё учителей и учеников
                </div>
            </div>
        </v-card>

        <!-- Мобильная кнопка создания: плавающая, не занимает места в потоке -->
        <v-btn
            v-if="isMobile"
            icon="mdi-plus"
            color="primary"
            size="large"
            elevation="4"
            title="Создать группу"
            class="create-fab"
            @click="dialog = true"
        />

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
import FilterChips from "../../components/FilterChips.vue";
import GroupCard from "../../components/GroupCard.vue";

export default {
    name: 'GroupsList',
    components: {FilterChips, GroupCard},
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
            // Вид списка на десктопе: карточки или таблица, запоминается в браузере
            viewMode: localStorage.getItem('adminGroupsView') === 'table' ? 'table' : 'cards',
            tableHeaders: [
                {title: 'Группа', key: 'name'},
                {title: 'Курс', key: 'course_name'},
                {title: 'Статус', key: 'status', sortable: false},
                {title: 'Ученики', key: 'disciples_active'},
                {title: 'Занятия', key: 'days_closed'},
                {title: 'Старт', key: 'first_day'},
                {title: 'Последнее', key: 'last_day'},
                {title: 'Учителя', key: 'teachers', sortable: false},
            ],
        }
    },
    watch: {
        viewMode(val) {
            localStorage.setItem('adminGroupsView', val);
        },
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
        // Мобильная ширина по порогам Vuetify (< 768px)
        isMobile() {
            return this.$vuetify.display.name === 'sm';
        },
        showTable() {
            return this.viewMode === 'table' && !this.isMobile;
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
/* Переключатель вида — не фильтр, поэтому стиль свой */
.view-toggle {
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

.create-fab {
    position: fixed;
    right: 16px;
    bottom: 16px;
    z-index: 5;
}

.groups-table :deep(tbody tr) {
    cursor: pointer;
}

/* Vuetify центрирует прогресс через left:50% + translateX(-50%), во flex-строке это сдвигает его на текст */
.table-progress {
    flex: 0 0 56px;
    width: 56px;
    left: 0 !important;
    transform: none !important;
}
</style>
