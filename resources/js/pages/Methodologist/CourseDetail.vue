<template>
    <div class="course-page">
        <!-- Шапка курса. На мобильном название курса уже есть в хлебных крошках,
             поэтому здесь остаётся только сводка и состояние сохранения -->
        <div class="tw-flex tw-items-start tw-justify-between tw-gap-3 tw-mb-3">
            <div class="tw-min-w-0">
                <h1 v-if="!isMobile" class="adf-heading tw-text-2xl tw-leading-8 tw-m-0 tw-truncate">
                    {{ getCourse?.name || 'Курс' }}
                </h1>
                <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted">
                    {{ subtitle }}
                </div>
            </div>

            <!-- Курс сохраняется сам; кнопка нужна, чтобы сохранить сразу и видеть состояние -->
            <v-btn
                v-if="!isMobile"
                variant="text"
                size="small"
                :color="isSaved ? 'success' : 'primary'"
                :prepend-icon="isSaved ? 'mdi-check' : 'mdi-content-save-outline'"
                :disabled="isSaved"
                class="tw-shrink-0"
                @click="save"
            >{{ isSaved ? 'Сохранено' : 'Сохранить' }}</v-btn>
            <v-btn
                v-else
                variant="text"
                size="small"
                density="comfortable"
                :color="isSaved ? 'success' : 'primary'"
                :icon="isSaved ? 'mdi-check' : 'mdi-content-save-outline'"
                :disabled="isSaved"
                :title="isSaved ? 'Сохранено' : 'Сохранить'"
                class="tw-shrink-0"
                @click="save"
            />
        </div>

        <v-progress-linear v-if="!getCourse?.id" indeterminate color="primary"/>

        <v-card v-else>
            <v-tabs v-model="tab" :grow="isMobile" color="primary">
                <v-tab value="studyProgram">Программа</v-tab>
                <v-tab value="courseSchedule">Расписание</v-tab>
            </v-tabs>
            <v-divider/>
            <!-- touch: иначе горизонтальный свайп переключает вкладку при перетаскивании урока -->
            <v-window v-model="tab" :touch="false">
                <v-window-item value="studyProgram">
                    <StudyProgram/>
                </v-window-item>
                <v-window-item value="courseSchedule">
                    <CourseSchedule/>
                </v-window-item>
            </v-window>
        </v-card>
    </div>
</template>
<script>
import {mapActions, mapGetters} from "vuex";
import StudyProgram from './StudyProgram.vue';
import CourseSchedule from './CourseSchedule.vue';

export default {
    name: 'CourseDetail',
    components: {StudyProgram, CourseSchedule},
    data() {
        return {
            tab: 'studyProgram',
            timerSaveId: null,
            lastChangeDataTimerId: false,
            lastChangeDataHash: false,
            lastChangeDataTm: 0,
        }
    },
    computed: {
        ...mapGetters('methodologist', ['getCourse', 'getStudyProgram', 'isSaved', 'getHashParams']),
        isMobile() {
            return this.$vuetify.display.name === 'sm';
        },
        courseId() {
            return this.$route.params.idCourse;
        },
        cntLessons() {
            let cnt = 0;
            for (const k in this.getStudyProgram) {
                cnt = cnt + this.getStudyProgram[k].lessons.length;
            }
            return cnt;
        },
        cntHours() {
            let cnt = 0;
            for (const k in this.getStudyProgram) {
                for (const l in this.getStudyProgram[k].lessons) {
                    // hours — nullable double, у части уроков его может не быть
                    cnt = cnt + (Number(this.getStudyProgram[k].lessons[l].hours) || 0);
                }
            }
            return Math.round(cnt * 10) / 10;
        },
        subtitle() {
            if (!this.getCourse?.id) return 'Загрузка…';
            const modules = this.getStudyProgram.length;
            if (!modules && !this.cntLessons) return 'Программа пока пустая';
            return `${modules} ${this.plural(modules, ['модуль', 'модуля', 'модулей'])}`
                + ` · ${this.cntLessons} ${this.plural(this.cntLessons, ['урок', 'урока', 'уроков'])}`
                + ` · ${this.cntHours} ${this.plural(this.cntHours, ['час', 'часа', 'часов'])}`;
        },
    },
    methods: {
        ...mapActions('methodologist', ['actReqwestCourse', 'actSaveCourse']),
        plural(n, forms) {
            const abs = Math.abs(n) % 100;
            const last = abs % 10;
            if (abs > 10 && abs < 20) return forms[2];
            if (last > 1 && last < 5) return forms[1];
            if (last === 1) return forms[0];
            return forms[2];
        },
        async save() {
            await this.actSaveCourse();
        },
        autoSaveStart() {
            let saveInterval = 1000; // интервал сохранения
            // таймер запоминания последнего изменения
            this.lastChangeDataTimerId = setInterval(async () => {
                // сравнить текущий хеш данных блока с последним запомненным
                let newHash = this.getHashParams;
                if (newHash !== this.lastChangeDataHash) {
                    // если изменился - запомнить время изменения и хеш
                    this.lastChangeDataTm = performance.now();
                    this.lastChangeDataHash = newHash;
                }
            }, saveInterval / 2); // дважды в секунду проверять изменение данных

            // таймер автосохранения
            this.timerSaveId = setInterval(async () => {
                // блок не сохранен и последние изменения данных были более секунды назад
                if (!this.isSaved && (performance.now() - this.lastChangeDataTm) > saveInterval) {
                    await this.save();
                }
            }, saveInterval);
        },
        autoSaveStop() {
            if (this.timerSaveId) {
                clearInterval(this.timerSaveId);
                this.timerSaveId = false;
            }
            if (this.lastChangeDataTimerId) {
                clearInterval(this.lastChangeDataTimerId);
                this.lastChangeDataTimerId = false;
            }
        },
    },
    async mounted() {
        // остановить автоматическое сохранение
        this.autoSaveStop();

        await this.actReqwestCourse({id: this.courseId});

        // Автоматическое сохранение. Проверка изменений 1 раз в секунду
        this.autoSaveStart();
    },
    beforeUnmount() {
        // Уйти со страницы можно быстрее, чем сработает автосохранение:
        // отправляем набранное, данные для запроса читаются синхронно
        this.actSaveCourse();
    },
    unmounted() {
        // остановить автоматическое сохранение
        this.autoSaveStop();
    },
}
</script>

<style scoped lang="scss">
</style>
