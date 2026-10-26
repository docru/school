<script>
import {mapActions, mapGetters} from "vuex";
import swipeSheet from "../../mixins/swipeSheet.js";
import ModuleList from "./ModuleList.vue";
import LessonEditor from "./LessonEditor.vue";

export default {
    name: 'StudyProgram',
    components: {ModuleList, LessonEditor},
    mixins: [swipeSheet],
    data() {
        return {
            dialogCreateModule: false,
            newModuleName: '',
            dialogCreateLesson: false,
            moduleIdForNewLesson: null,
            newLessonName: '',
            curLessonId: false,
            lessonDialog: false,
        }
    },
    computed: {
        ...mapGetters('methodologist', ['getStudyProgram', 'getLessons']),
        courseId() {
            return this.$route.params.idCourse;
        },
        // Объект урока из стора: правки полей уходят автосохранением курса
        curLesson() {
            return this.getLessons[this.curLessonId] ?? false;
        },
    },
    methods: {
        ...mapActions('methodologist', [
            'actCreateModule',
            'actDeleteModule',
            'actCreateLesson',
            'actDeleteLesson',
        ]),
        async createModule() {
            await this.actCreateModule({courseId: this.courseId, moduleName: this.newModuleName});
            this.newModuleName = '';
            this.dialogCreateModule = false;
        },
        async deleteModule(module) {
            if (await this.$confirm({title: 'Удалить модуль?', text: `«${module.name}» будет удалён из программы.`, okText: 'Удалить', color: 'error'})) {
                await this.actDeleteModule(module);
            }
        },
        openDialogCreateLesson(moduleId) {
            this.moduleIdForNewLesson = moduleId;
            this.openDialog('dialogCreateLesson');
        },
        async createLesson() {
            await this.actCreateLesson({
                courseId: this.courseId,
                moduleId: this.moduleIdForNewLesson,
                lessonName: this.newLessonName
            });
            this.newLessonName = '';
            this.dialogCreateLesson = false;
        },
        async deleteLesson(lesson) {
            if (await this.$confirm({title: 'Удалить урок?', text: `«${lesson.name}» будет удалён из программы.`, okText: 'Удалить', color: 'error'})) {
                const wasOpen = this.curLessonId === lesson.id;
                await this.actDeleteLesson(lesson);
                if (wasOpen) {
                    this.curLessonId = false;
                    this.lessonDialog = false;
                }
            }
        },
        chooseLesson(lesson) {
            this.curLessonId = lesson.id;
            // На телефоне редактор открывается отдельным экраном, на десктопе он уже рядом со списком
            if (this.isMobile) {
                this.openDialog('lessonDialog');
            }
        },
    },
}
</script>

<template>
    <div class="study-program tw-p-3 md:tw-p-4">
        <!-- На телефоне заголовок не нужен: он повторяет название вкладки -->
        <div v-if="!isMobile" class="tw-flex tw-items-center tw-justify-between tw-gap-3 tw-mb-3">
            <div class="adf-heading tw-text-lg tw-leading-6">Программа курса</div>
            <v-btn
                v-if="!isMobile"
                color="primary"
                variant="outlined"
                prepend-icon="mdi-plus"
                @click="openDialog('dialogCreateModule')"
            >Создать модуль</v-btn>
        </div>
        <v-btn
            v-if="isMobile"
            color="primary"
            variant="outlined"
            prepend-icon="mdi-plus"
            block
            class="tw-mb-3"
            @click="openDialog('dialogCreateModule')"
        >Создать модуль</v-btn>

        <!-- Пустое состояние -->
        <div v-if="!getStudyProgram.length" class="tw-p-8 tw-text-center">
            <v-icon size="40" color="muted">mdi-file-tree-outline</v-icon>
            <div class="adf-heading tw-text-lg tw-mt-2 tw-mb-1">Программа пустая</div>
            <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted">
                Начните с модуля — внутри него появятся уроки
            </div>
        </div>

        <div v-else class="tw-flex tw-flex-col md:tw-flex-row tw-gap-3">
            <div class="md:tw-w-1/3 program-pane">
                <module-list
                    :getStudyProgram="getStudyProgram"
                    :curLessonId="curLessonId"
                    :isMobile="isMobile"
                    @openDialogCreateLesson="openDialogCreateLesson"
                    @deleteModule="deleteModule"
                    @chooseLesson="chooseLesson"
                    @deleteLesson="deleteLesson"
                />
            </div>

            <!-- Редактор урока рядом со списком (десктоп) -->
            <div v-if="!isMobile" class="md:tw-w-2/3 program-pane">
                <LessonEditor v-if="curLesson" :lesson="curLesson"/>
                <div v-else class="tw-p-8 tw-text-center">
                    <v-icon size="40" color="muted">mdi-gesture-tap</v-icon>
                    <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted tw-mt-2">
                        Выберите урок в списке слева
                    </div>
                </div>
            </div>
        </div>

        <!-- Редактор урока отдельным экраном (телефон) -->
        <v-dialog v-if="isMobile" v-model="lessonDialog" fullscreen transition="dialog-bottom-transition">
            <v-card v-if="curLesson" class="lesson-dialog" :style="sheetStyle">
                <!-- Свайп вниз закрывает окно только за шапку: внутри редактора жест нужен для ввода -->
                <div
                    class="lesson-dialog__head"
                    data-swipe-close="lessonDialog"
                    @touchstart.passive="sheetTouchStart"
                    @touchmove="sheetTouchMove"
                    @touchend="sheetTouchEnd"
                    @touchcancel="sheetTouchEnd"
                >
                    <div class="sheet-handle"></div>
                    <div class="tw-flex tw-items-center tw-justify-between tw-gap-2 tw-px-4 tw-pb-2">
                        <div class="tw-min-w-0">
                            <div class="bp-overline">Урок</div>
                            <div class="adf-heading tw-text-lg tw-leading-6 tw-truncate">{{ curLesson.name }}</div>
                        </div>
                        <v-btn icon="mdi-close" size="small" density="comfortable" variant="text" color="muted" class="tw-shrink-0 tw--mr-1" @click="lessonDialog = false"/>
                    </div>
                </div>
                <div class="lesson-dialog__body tw-px-3 tw-pb-3">
                    <LessonEditor :lesson="curLesson"/>
                </div>
            </v-card>
        </v-dialog>

        <!-- Создание модуля -->
        <v-dialog v-model="dialogCreateModule" width="420" max-width="calc(100vw - 32px)">
            <v-card class="edit-dialog">
                <div class="tw-flex tw-items-center tw-justify-between tw-gap-2 tw-mb-2">
                    <div class="adf-heading tw-text-xl tw-leading-7">Создать модуль</div>
                    <v-btn icon="mdi-close" size="small" density="comfortable" variant="text" color="muted" class="tw--mr-1" @click="dialogCreateModule = false"/>
                </div>
                <v-text-field
                    label="Название модуля"
                    v-model="newModuleName"
                    autofocus
                    @keyup.enter="createModule()"
                />
                <div class="tw-flex tw-justify-end tw-gap-2 tw-mt-4">
                    <v-btn variant="text" color="muted" @click="dialogCreateModule = false">Отмена</v-btn>
                    <v-btn color="primary" :disabled="!newModuleName.trim()" @click="createModule()">Создать</v-btn>
                </div>
            </v-card>
        </v-dialog>

        <!-- Создание урока -->
        <v-dialog v-model="dialogCreateLesson" width="420" max-width="calc(100vw - 32px)">
            <v-card class="edit-dialog">
                <div class="tw-flex tw-items-center tw-justify-between tw-gap-2 tw-mb-2">
                    <div class="adf-heading tw-text-xl tw-leading-7">Создать урок</div>
                    <v-btn icon="mdi-close" size="small" density="comfortable" variant="text" color="muted" class="tw--mr-1" @click="dialogCreateLesson = false"/>
                </div>
                <v-text-field
                    label="Название урока"
                    v-model="newLessonName"
                    autofocus
                    @keyup.enter="createLesson()"
                />
                <div class="tw-flex tw-justify-end tw-gap-2 tw-mt-4">
                    <v-btn variant="text" color="muted" @click="dialogCreateLesson = false">Отмена</v-btn>
                    <v-btn color="primary" :disabled="!newLessonName.trim()" @click="createLesson()">Создать</v-btn>
                </div>
            </v-card>
        </v-dialog>
    </div>
</template>

<style scoped lang="scss">
/* На десктопе колонки прокручиваются независимо: вычитается шапка приложения,
   шапка курса и строка вкладок. На телефоне прокручивается страница целиком. */
@media (min-width: 768px) {
    .program-pane {
        max-height: calc(100vh - 280px);
        overflow-y: auto;
    }
}

.lesson-dialog.v-card {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.lesson-dialog__head {
    flex: 0 0 auto;
    border-bottom: 1px solid var(--bp-border);
}

/* Тело не прокручивается само: высоту целиком забирает редактор,
   у него своя прокрутка — иначе получаются две вложенные полосы и обрезанный текст */
.lesson-dialog__body {
    display: flex;
    flex-direction: column;
    flex: 1 1 auto;
    min-height: 0;
    overflow: hidden;
}
</style>
