<template>
    <!-- Нижний отступ на мобильном — чтобы плавающая кнопка не закрывала последнюю строку -->
    <div class="courses-page tw-pb-24 md:tw-pb-0">
        <!-- Шапка раздела (только десктоп): на мобильном название уже есть в хлебных крошках -->
        <div v-if="!isMobile" class="tw-flex tw-items-start tw-justify-between tw-gap-3 tw-mb-4">
            <div class="tw-min-w-0">
                <h1 class="adf-heading tw-text-2xl tw-leading-8 tw-m-0">Курсы</h1>
                <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted">
                    {{ subtitle }}
                </div>
            </div>
            <div class="tw-flex tw-items-center tw-gap-1 tw-shrink-0">
                <v-btn
                    icon="mdi-refresh"
                    variant="text"
                    color="muted"
                    size="small"
                    :loading="getLoad"
                    title="Обновить список"
                    @click="actReqwestCourses"
                />
                <v-btn color="primary" prepend-icon="mdi-plus" @click="openCreate">Новый курс</v-btn>
            </div>
        </div>

        <v-progress-linear v-if="getLoad && !getCourses.length" indeterminate color="primary" class="tw-mb-4"/>

        <!-- Таблица (десктоп) -->
        <v-card v-if="!isMobile && getCourses.length">
            <v-data-table
                class="courses-table"
                :headers="headers"
                :items="getCourses"
                items-per-page="-1"
                @click:row="onRowClick"
            >
                <!-- В Vuetify 3 подвал убирается пустым слотом: hide-default-footer там нет -->
                <template v-slot:bottom></template>
                <template v-slot:item.name="{item}">
                    <span class="tw-font-medium">{{ item.name }}</span>
                </template>
                <template v-slot:item.description="{item}">
                    <span v-if="item.description" class="tw-line-clamp-2">{{ item.description }}</span>
                    <span v-else class="tw-text-text-muted dark:tw-text-dark-text-muted">без описания</span>
                </template>
                <template v-slot:item.hours="{item}">
                    <span v-if="item.hours">{{ item.hours }}</span>
                    <span v-else class="tw-text-text-muted dark:tw-text-dark-text-muted">—</span>
                </template>
                <template v-slot:item.school_days_count="{item}">
                    <span v-if="item.school_days_count">{{ item.school_days_count }}</span>
                    <span v-else class="tw-text-text-muted dark:tw-text-dark-text-muted">—</span>
                </template>
            </v-data-table>
        </v-card>

        <!-- Список (телефон) -->
        <v-card v-else-if="getCourses.length">
            <ListRow
                v-for="item in getCourses"
                :key="item.id"
                :title="item.name"
                clickable
                @click="openCourse(item)"
            >
                <template #subtitle>
                    <span>{{ item.description || 'без описания' }}</span>
                    <span class="tw-text-accent dark:tw-text-dark-accent"> · {{ courseMeta(item) }}</span>
                </template>
            </ListRow>
        </v-card>

        <!-- Пустое состояние -->
        <v-card v-else-if="!getLoad">
            <div class="tw-p-8 tw-text-center">
                <v-icon size="40" color="muted">mdi-book-open-page-variant-outline</v-icon>
                <div class="adf-heading tw-text-lg tw-mt-2 tw-mb-1">Курсов пока нет</div>
                <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted">
                    Создайте курс — внутри появятся модули, уроки и расписание
                </div>
            </div>
        </v-card>

        <!-- Мобильная кнопка создания -->
        <v-btn
            v-if="isMobile"
            icon="mdi-plus"
            color="primary"
            size="large"
            elevation="4"
            title="Новый курс"
            class="create-fab"
            @click="openCreate"
        />

        <!-- Создание курса -->
        <v-dialog v-model="dialog" width="420" max-width="calc(100vw - 32px)">
            <v-card class="edit-dialog">
                <div class="tw-flex tw-items-center tw-justify-between tw-gap-2 tw-mb-2">
                    <div class="adf-heading tw-text-xl tw-leading-7">Новый курс</div>
                    <v-btn icon="mdi-close" size="small" density="comfortable" variant="text" color="muted" class="tw--mr-1" @click="dialog = false"/>
                </div>
                <v-text-field v-model="nameCourse" label="Название курса" autofocus/>
                <v-textarea v-model="description" label="Описание курса" rows="3"/>
                <div class="tw-flex tw-justify-end tw-gap-2 tw-mt-4">
                    <v-btn variant="text" color="muted" @click="dialog = false">Отмена</v-btn>
                    <v-btn color="primary" :disabled="!canCreate" :loading="saving" @click="createCourse">Создать</v-btn>
                </div>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import ListRow from "../../components/ListRow.vue";

export default {
    name: 'CoursesList',
    components: {ListRow},
    data() {
        return {
            headers: [
                {title: 'Название', key: 'name'},
                {title: 'Описание', key: 'description', sortable: false},
                {title: 'Модули', key: 'modules_count', align: 'end'},
                {title: 'Уроки', key: 'lessons_count', align: 'end'},
                {title: 'Часы', key: 'hours', align: 'end'},
                {title: 'Дни', key: 'school_days_count', align: 'end'},
            ],
            dialog: false,
            saving: false,
            nameCourse: '',
            description: '',
        }
    },
    computed: {
        ...mapGetters('methodologist', ['getCourses', 'getLoad']),
        isMobile() {
            return this.$vuetify.display.name === 'sm';
        },
        subtitle() {
            const n = this.getCourses.length;
            if (!n) return 'Курсов пока нет';
            const lessons = this.getCourses.reduce((sum, c) => sum + (c.lessons_count ?? 0), 0);
            return `${n} ${this.plural(n, ['курс', 'курса', 'курсов'])}`
                + `, ${lessons} ${this.plural(lessons, ['урок', 'урока', 'уроков'])}`;
        },
        canCreate() {
            return !!this.nameCourse.trim() && !!this.description.trim();
        },
    },
    methods: {
        ...mapActions('methodologist', ['actReqwestCourses', 'actCreateCourse']),
        plural(n, forms) {
            const abs = Math.abs(n) % 100;
            const last = abs % 10;
            if (abs > 10 && abs < 20) return forms[2];
            if (last > 1 && last < 5) return forms[1];
            if (last === 1) return forms[0];
            return forms[2];
        },
        // Сводка по программе курса одной строкой — на телефоне колонок таблицы нет
        courseMeta(item) {
            if (!item.modules_count && !item.lessons_count) return 'программа пустая';
            const parts = [
                `${item.modules_count} ${this.plural(item.modules_count, ['модуль', 'модуля', 'модулей'])}`,
                `${item.lessons_count} ${this.plural(item.lessons_count, ['урок', 'урока', 'уроков'])}`,
            ];
            if (item.hours) {
                parts.push(`${item.hours} ч`);
            }
            if (item.school_days_count) {
                parts.push(`${item.school_days_count} ${this.plural(item.school_days_count, ['день', 'дня', 'дней'])}`);
            }
            return parts.join(' · ');
        },
        openCourse(item) {
            this.$router.push({name: 'MethodologistCourseDetail', params: {idCourse: item.id}});
        },
        // v-data-table отдаёт (event, {item})
        onRowClick(event, {item}) {
            this.openCourse(item);
        },
        openCreate() {
            this.nameCourse = '';
            this.description = '';
            this.dialog = true;
        },
        async createCourse() {
            this.saving = true;
            try {
                if (await this.actCreateCourse({nameCourse: this.nameCourse, description: this.description})) {
                    this.dialog = false;
                }
            } finally {
                this.saving = false;
            }
        },
    },
    created() {
        this.actReqwestCourses();
    }
}
</script>

<style scoped lang="scss">
.courses-table :deep(tbody tr) {
    cursor: pointer;
}

.create-fab {
    position: fixed;
    right: 16px;
    bottom: 16px;
    z-index: 5;
}
</style>
