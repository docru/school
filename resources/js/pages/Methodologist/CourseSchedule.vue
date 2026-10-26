<script>
import {mapActions, mapGetters} from "vuex";
import draggable from 'vuedraggable'
import ListRow from "../../components/ListRow.vue";
import swipeSheet from "../../mixins/swipeSheet.js";

export default {
    name: 'CourseSchedule',
    components: {draggable, ListRow},
    mixins: [swipeSheet],
    data() {
        return {
            // день, для которого на телефоне открыт выбор уроков
            pickerDayId: null,
            pickerSheet: false,
        }
    },
    computed: {
        ...mapGetters('methodologist', [
            'getStudyProgram',
            'getSchedule',
            'getLessons',
        ]),
        courseId() {
            return this.$route.params.idCourse;
        },
        // Уроки, ещё не поставленные ни в один учебный день, по модулям
        unselectedLessons() {
            return this.getStudyProgram.map(module => ({
                module: module.module,
                lessons: module.lessons.filter(lesson => !lesson.school_day),
            }));
        },
        unselectedCount() {
            return this.unselectedLessons.reduce((sum, m) => sum + m.lessons.length, 0);
        },
        // {dayId: [уроки в порядке school_day_order]}
        schedul() {
            let schoolDays = {};
            for (const day of this.getSchedule) {
                schoolDays[day.id] = [];
            }

            let lessonsAll = this.getLessons;
            for (const l in lessonsAll) {
                let lesson = lessonsAll[l];
                if (lesson.school_day && schoolDays[lesson.school_day]) {
                    schoolDays[lesson.school_day].push(lesson);
                }
            }

            for (const d in schoolDays) {
                // порядок — число: при строковом сравнении «10» встало бы раньше «9»
                schoolDays[d].sort((a, b) => Number(a.school_day_order) - Number(b.school_day_order));
            }
            return schoolDays;
        },
    },
    methods: {
        ...mapActions('methodologist', [
            'actCreateCourseSchoolDay',
            'actDeleteCourseSchoolDay',
        ]),
        plural(n, forms) {
            const abs = Math.abs(n) % 100;
            const last = abs % 10;
            if (abs > 10 && abs < 20) return forms[2];
            if (last > 1 && last < 5) return forms[1];
            if (last === 1) return forms[0];
            return forms[2];
        },
        createSchoolDay() {
            this.actCreateCourseSchoolDay({course_id: this.courseId});
        },
        async deleteCourseSchoolDay(day) {
            if (await this.$confirm({title: 'Удалить учебный день?', text: 'Уроки этого дня останутся в программе без даты.', okText: 'Удалить', color: 'error'})) {
                await this.actDeleteCourseSchoolDay(day);
            }
        },
        // Уроки дня по порядку; заодно нумерует их подряд с нуля
        renumberDay(dayId) {
            const lessons = Object.values(this.getLessons)
                .filter(lesson => lesson.school_day == dayId)
                .sort((a, b) => Number(a.school_day_order) - Number(b.school_day_order));
            lessons.forEach((lesson, i) => lesson.school_day_order = i);
            return lessons;
        },
        // Перетаскивание (десктоп): урок кладётся в тот день, над которым его отпустили
        onEnd(e) {
            const dayId = e.to.dataset.dayId;
            const lessonId = e.item.dataset.lessonId;
            if (!dayId || !lessonId) return;

            const lesson = this.getLessons[lessonId];
            if (!lesson) return;

            const prevDayId = lesson.school_day;
            lesson.school_day = Number(dayId);

            const lessons = Object.values(this.getLessons)
                .filter(item => item.id !== lesson.id && item.school_day == dayId)
                .sort((a, b) => Number(a.school_day_order) - Number(b.school_day_order));
            lessons.splice(e.newIndex, 0, lesson);
            lessons.forEach((item, i) => item.school_day_order = i);

            if (prevDayId && prevDayId != dayId) {
                this.renumberDay(prevDayId);
            }
        },
        // Выбор уроков для дня (телефон)
        openPicker(day) {
            this.pickerDayId = day.id;
            this.openDialog('pickerSheet');
        },
        addLessonToDay(lesson) {
            // порядок считаем до присваивания дня: иначе урок уже попадёт в schedul и номер сдвинется
            const order = this.schedul[this.pickerDayId]?.length ?? 0;
            lesson.school_day = this.pickerDayId;
            lesson.school_day_order = order;
        },
        removeLesson(lesson) {
            const dayId = lesson.school_day;
            lesson.school_day = null;
            lesson.school_day_order = null;
            if (dayId) {
                this.renumberDay(dayId);
            }
        },
        // Сдвиг урока внутри дня стрелками (телефон)
        moveLesson(dayId, index, shift) {
            const lessons = this.renumberDay(dayId);
            const target = index + shift;
            if (target < 0 || target >= lessons.length) return;
            const moved = lessons.splice(index, 1)[0];
            lessons.splice(target, 0, moved);
            lessons.forEach((lesson, i) => lesson.school_day_order = i);
        },
    },
}
</script>

<template>
    <div class="course-schedule tw-p-3 md:tw-p-4">
        <div class="tw-flex tw-items-center tw-justify-between tw-gap-3 tw-mb-3">
            <div class="tw-min-w-0">
                <!-- Заголовок повторяет название вкладки, на телефоне он лишний -->
                <div v-if="!isMobile" class="adf-heading tw-text-lg tw-leading-6">Расписание курса</div>
                <div class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted">
                    {{ getSchedule.length }} {{ plural(getSchedule.length, ['учебный день', 'учебных дня', 'учебных дней']) }},
                    не распределено {{ unselectedCount }}
                </div>
            </div>
            <v-btn
                v-if="!isMobile"
                color="primary"
                variant="outlined"
                prepend-icon="mdi-calendar-plus"
                class="tw-shrink-0"
                @click="createSchoolDay()"
            >Добавить учебный день</v-btn>
        </div>
        <v-btn
            v-if="isMobile"
            color="primary"
            variant="outlined"
            prepend-icon="mdi-calendar-plus"
            block
            class="tw-mb-3"
            @click="createSchoolDay()"
        >Добавить учебный день</v-btn>

        <!-- Пустое состояние -->
        <div v-if="!getSchedule.length" class="tw-p-8 tw-text-center">
            <v-icon size="40" color="muted">mdi-calendar-blank-outline</v-icon>
            <div class="adf-heading tw-text-lg tw-mt-2 tw-mb-1">Учебных дней нет</div>
            <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted">
                Добавьте день и разложите по нему уроки программы
            </div>
        </div>

        <!-- Десктоп: слева уроки программы, справа дни; перенос перетаскиванием -->
        <div v-else-if="!isMobile" class="tw-flex tw-gap-3">
            <div class="tw-w-1/3 schedule-pane">
                <div class="bp-overline tw-mb-2">Уроки программы</div>
                <div v-for="moduleItem in getStudyProgram" :key="moduleItem.module.id" class="section-card">
                    <div class="section-card__header">
                        <div class="tw-min-w-0">
                            <div class="bp-overline">Модуль</div>
                            <div class="tw-text-sm tw-font-medium tw-truncate">{{ moduleItem.module.name }}</div>
                        </div>
                    </div>
                    <draggable
                        :group="{ name: moduleItem.module.name, pull: 'clone', put: false}"
                        :list="moduleItem.lessons"
                        item-key="id"
                        handle=".item"
                        @end="onEnd"
                    >
                        <template #item="{element: lesson}">
                            <ListRow
                                dense
                                :title="lesson.name"
                                :muted="!!lesson.school_day"
                                :class="{'item': !lesson.school_day}"
                                :data-lesson-id="lesson.id"
                            >
                                <template #lead>
                                    <v-icon v-if="!lesson.school_day" size="18" color="muted" class="tw-cursor-grab tw-shrink-0">mdi-drag</v-icon>
                                    <v-icon v-else size="18" color="success" class="tw-shrink-0">mdi-check</v-icon>
                                </template>
                                <template #subtitle>{{ parseInt(lesson.order) }} урок</template>
                            </ListRow>
                        </template>
                    </draggable>
                    <div v-if="!moduleItem.lessons.length" class="tw-px-3 tw-py-2 tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted">
                        В модуле пока нет уроков
                    </div>
                </div>
            </div>

            <div class="tw-w-2/3 schedule-pane">
                <div class="bp-overline tw-mb-2">Учебные дни</div>
                <div v-for="day in getSchedule" :key="day.id" class="section-card">
                    <div class="section-card__header">
                        <div class="tw-min-w-0 tw-flex-1">
                            <div class="tw-text-sm tw-font-medium">День {{ day.order }}</div>
                            <div class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted">
                                {{ schedul[day.id].length }} {{ plural(schedul[day.id].length, ['урок', 'урока', 'уроков']) }}
                            </div>
                        </div>
                        <v-btn
                            icon="mdi-delete-outline"
                            size="small"
                            density="comfortable"
                            variant="text"
                            color="muted"
                            title="Удалить учебный день"
                            class="tw-shrink-0"
                            @click="deleteCourseSchoolDay(day)"
                        />
                    </div>
                    <draggable
                        class="day-drop"
                        :data-day-id="day.id"
                        :list="schedul[day.id]"
                        :group="{ name: 'to_' + day.id, put: true }"
                        item-key="id"
                        @end="onEnd"
                    >
                        <template #item="{element: lesson, index}">
                            <ListRow dense :title="lesson.name" :data-lesson-id="lesson.id">
                                <template #lead><span class="row-num">{{ index + 1 }}</span></template>
                                <template #actions>
                                    <v-btn
                                        icon="mdi-close"
                                        size="small"
                                        density="comfortable"
                                        variant="text"
                                        color="muted"
                                        title="Убрать из дня"
                                        class="tw-shrink-0"
                                        @click="removeLesson(lesson)"
                                    />
                                </template>
                            </ListRow>
                        </template>
                    </draggable>
                    <div v-if="!schedul[day.id].length" class="tw-px-3 tw-py-3 tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted">
                        Перетащите сюда уроки из программы
                    </div>
                </div>
            </div>
        </div>

        <!-- Телефон: дни списком, уроки добавляются выбором и двигаются стрелками -->
        <div v-else>
            <div v-for="day in getSchedule" :key="day.id" class="section-card">
                <div class="section-card__header">
                    <div class="tw-min-w-0 tw-flex-1">
                        <div class="tw-text-sm tw-font-medium">День {{ day.order }}</div>
                        <div class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted">
                            {{ schedul[day.id].length }} {{ plural(schedul[day.id].length, ['урок', 'урока', 'уроков']) }}
                        </div>
                    </div>
                    <v-btn
                        icon="mdi-delete-outline"
                        size="small"
                        density="comfortable"
                        variant="text"
                        color="muted"
                        title="Удалить учебный день"
                        class="tw-shrink-0"
                        @click="deleteCourseSchoolDay(day)"
                    />
                </div>

                <ListRow
                    v-for="(lesson, index) in schedul[day.id]"
                    :key="lesson.id"
                    dense
                    wrap-title
                    :title="lesson.name"
                >
                    <template #lead><span class="row-num">{{ index + 1 }}</span></template>
                    <template #actions>
                    <v-btn
                        icon="mdi-arrow-up"
                        size="small"
                        density="compact"
                        variant="text"
                        color="muted"
                        title="Выше"
                        :disabled="index === 0"
                        class="tw-shrink-0"
                        @click="moveLesson(day.id, index, -1)"
                    />
                    <v-btn
                        icon="mdi-arrow-down"
                        size="small"
                        density="compact"
                        variant="text"
                        color="muted"
                        title="Ниже"
                        :disabled="index === schedul[day.id].length - 1"
                        class="tw-shrink-0"
                        @click="moveLesson(day.id, index, 1)"
                    />
                    <v-btn
                        icon="mdi-close"
                        size="small"
                        density="compact"
                        variant="text"
                        color="muted"
                        title="Убрать из дня"
                        class="tw-shrink-0"
                        @click="removeLesson(lesson)"
                    />
                    </template>
                </ListRow>

                <!-- Когда свободных уроков нет, кнопку прячем: иначе она висит погашенной в каждом дне -->
                <div class="tw-p-2" v-if="unselectedCount">
                    <v-btn
                        variant="text"
                        color="primary"
                        size="small"
                        prepend-icon="mdi-plus"
                        block
                        @click="openPicker(day)"
                    >Добавить урок</v-btn>
                </div>
            </div>
        </div>

        <!-- Выбор уроков для дня (телефон) -->
        <v-bottom-sheet v-model="pickerSheet">
            <v-card
                class="picker-sheet"
                data-swipe-close="pickerSheet"
                :style="sheetStyle"
                @touchstart.passive="sheetTouchStart"
                @touchmove="sheetTouchMove"
                @touchend="sheetTouchEnd"
                @touchcancel="sheetTouchEnd"
            >
                <div class="sheet-handle"></div>
                <div class="tw-flex tw-items-center tw-justify-between tw-gap-2 tw-px-4 tw-pt-1 tw-pb-2">
                    <div class="tw-min-w-0">
                        <div class="bp-overline">Добавить в день</div>
                        <div class="adf-heading tw-text-lg tw-leading-6">
                            Нераспределённые уроки
                        </div>
                    </div>
                    <v-btn icon="mdi-close" size="small" density="comfortable" variant="text" color="muted" class="tw-shrink-0 tw--mr-1" @click="pickerSheet = false"/>
                </div>

                <div class="swipe-scroll picker-sheet__body tw-pb-4">
                    <template v-for="moduleItem in unselectedLessons" :key="moduleItem.module.id">
                        <div v-if="moduleItem.lessons.length">
                            <div class="bp-overline tw-px-4 tw-pt-3 tw-pb-1">{{ moduleItem.module.name }}</div>
                            <ListRow
                                v-for="lesson in moduleItem.lessons"
                                :key="lesson.id"
                                dense
                                clickable
                                :title="lesson.name"
                                @click="addLessonToDay(lesson)"
                            >
                                <template #subtitle>{{ parseInt(lesson.order) }} урок</template>
                                <template #actions>
                                    <v-icon size="20" color="primary" class="tw-shrink-0">mdi-plus</v-icon>
                                </template>
                            </ListRow>
                        </div>
                    </template>

                    <div v-if="!unselectedCount" class="tw-p-8 tw-text-center">
                        <v-icon size="40" color="muted">mdi-check-all</v-icon>
                        <div class="adf-heading tw-text-lg tw-mt-2 tw-mb-1">Все уроки распределены</div>
                        <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted">
                            Свободных уроков в программе не осталось
                        </div>
                    </div>
                </div>
            </v-card>
        </v-bottom-sheet>
    </div>
</template>

<style scoped lang="scss">
/* На десктопе колонки прокручиваются независимо (см. такой же расчёт в StudyProgram) */
@media (min-width: 768px) {
    .schedule-pane {
        max-height: calc(100vh - 280px);
        overflow-y: auto;
    }
}

/* Пустой день должен принимать перетаскивание, поэтому у области есть минимальная высота */
.day-drop {
    min-height: 8px;
}

.picker-sheet.v-card {
    display: flex;
    flex-direction: column;
    max-height: 85vh;
    border-radius: var(--bp-radius-md, 8px) var(--bp-radius-md, 8px) 0 0 !important;
}

.picker-sheet__body {
    overflow-y: auto;
}
</style>
