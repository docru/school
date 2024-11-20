<script>
import {mapActions, mapGetters, mapMutations} from "vuex";
import ModulList from "./ModuleList.vue";
import ModuleList from "./ModuleList.vue";

export default {
    name: 'StudyProgram',
    components: {ModuleList, ModulList},
    data() {
        return {
            tab: null,
            dialogCreateModule: false,
            newModuleName: '',
            dialogCreateLesson: false,
            moduleForMewLesson: false,
            newLessonName: '',
            curLessonId: false,
        }
    },
    computed: {
        ...mapGetters('methodologist', ['getCourse', 'getStudyProgram', 'getLessons']),
        courseId() {
            return this.$route.params.idCourse;
        },
        curLesson: {
            get() {
                return this.getLessons[this.curLessonId] ?? false;
            },
            set(data) {
                this.setLesson(data);
            },
        },
    },
    methods: {
        ...mapMutations('methodologist', ['setStudyProgram', 'setLesson']),
        ...mapActions('methodologist', [
            'actReqwestCourse',
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
            if (confirm(`Вы уверены, что хотите удалить модуль "${module.name}"?`)) {
                await this.actDeleteModule(module);
            }
        },
        openDialogCreateLesson(module) {
            this.moduleForMewLesson = module;
            this.dialogCreateLesson = true;
        },
        async createLesson() {
            await this.actCreateLesson({
                courseId: this.courseId,
                moduleId: this.moduleForMewLesson,
                lessonName: this.newLessonName
            });
            this.newLessonName = '';
            this.dialogCreateLesson = false;
        },
        async deleteLesson(lesson) {
            if (confirm(`Вы уверены, что хотите удалить урок "${lesson.name}"?`)) {
                await this.actDeleteLesson(lesson);
                if (lesson.id === this.curLesson.id) {
                    delete this.curLesson;
                    this.curLesson = false;
                }
            }
        },
        chooseLesson(lesson) {
            this.curLessonId = lesson.id;
        },
    },
}
</script>

<template>
    <v-card variant="text">
        <v-card-title>
            <div class="tw-flex">
                <div>Программа курса</div>
                <v-spacer></v-spacer>
                <div class="tw-flex tw-gap-3">
                    <v-btn @click="dialogCreateModule=true">Создать модуль</v-btn>
                    <v-dialog v-model="dialogCreateModule" fullscreen>
                        <v-card>
                            <v-card-title>
                                <div class="tw-flex">
                                    <div>Создать модуль</div>
                                    <v-spacer></v-spacer>
                                    <v-icon @click="dialogCreateModule=false">mdi-close</v-icon>
                                </div>
                            </v-card-title>
                            <v-card-text>
                                <v-text-field
                                    label="Название модуля"
                                    @keyup.enter="createModule()"
                                    v-model="newModuleName"
                                    autofocus
                                />
                            </v-card-text>
                            <v-card-actions>
                                <v-btn @click="createModule()">Создать модуль</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </div>
            </div>
        </v-card-title>
        <v-card-text>
            <div class="tw-flex tw-gap-3 ">
                <div class="tw-w-1/3" style="height: calc(100vh - 300px); overflow: auto">
                    <module-list
                        :getStudyProgram="getStudyProgram"
                        :curLesson="curLesson"
                        @openDialogCreateLesson=openDialogCreateLesson
                        @deleteModule=deleteModule
                        @chooseLesson="chooseLesson"
                        @deleteLesson="deleteLesson"
                        @setStudyProgram="setStudyProgram"
                    />
                    <v-dialog v-model="dialogCreateLesson" fullscreen>
                        <v-card>
                            <v-card-title>
                                <div class="tw-flex">
                                    <div>Создать урок</div>
                                    <v-spacer></v-spacer>
                                    <v-icon @click="dialogCreateLesson=false">mdi-close</v-icon>
                                </div>
                            </v-card-title>
                            <v-card-text>
                                <v-text-field
                                    label="Название урока"
                                    v-model="newLessonName"
                                    @keyup.enter="createLesson()"
                                    autofocus
                                />
                            </v-card-text>
                            <v-card-actions>
                                <v-btn @click="createLesson()">Создать урок</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </div>
                <div class="tw-w-2/3">
                    <v-card v-if="!curLesson">
                        <v-card-text>
                            Не выбран урок
                        </v-card-text>
                    </v-card>
                    <v-card v-else hover elevation="2" style="padding: 5px 10px;">
                        <v-card-title>
                            <div class="tw-flex">
                                <div>{{ curLesson.name }}</div>
                            </div>
                        </v-card-title>
                        <v-tabs
                            v-model="tab"
                            bg-color="primary"
                            density="compact"
                        >
                            <v-tab value="methodical">План урока</v-tab>
                            <v-tab value="abstract">Конспект</v-tab>
                            <v-tab value="two">Задания на уроке</v-tab>
                            <v-tab value="three">Домашнее задание</v-tab>
                        </v-tabs>

                        <v-card-text>
                            <v-window v-model="tab">
                                <v-window-item value="methodical">
                                    <v-text-field
                                        label="Название урока"
                                        v-model="curLesson.name"
                                        variant="outlined"
                                    />
                                    <v-md-editor
                                        v-model="curLesson.methodical_description "
                                        height="400px"
                                    ></v-md-editor>
                                    <!--                                    <v-textarea-->
                                    <!--                                        label="Методическое описание урока"-->
                                    <!--                                        variant="outlined"-->
                                    <!--                                        v-model="curLesson.methodical_description"-->
                                    <!--                                    ></v-textarea>-->
                                </v-window-item>
                                <v-window-item value="abstract">
                                    <v-md-editor
                                        v-model="curLesson.abstract"
                                        height="400px"
                                    ></v-md-editor>
                                    <!--                  <v-textarea-->
                                    <!--                      label="Конспект урока"-->
                                    <!--                      variant="outlined"-->
                                    <!--                      v-model="curLesson.abstract"-->
                                    <!--                  ></v-textarea>-->
                                </v-window-item>

                                <v-window-item value="two">
                                    <v-card>
                                        <v-card-title>Задание на уроке</v-card-title>
                                        <div
                                            style="margin: 15px; background-color: darkseagreen; border-radius: 4px; padding: 15px">
                                            <h3>Задание №1</h3>
                                            <p class="tw-p-3 tw-bg-white  ">
                                                Разбейтесь по парам и расскажите друг другу ...
                                            </p>
                                        </div>
                                    </v-card>
                                </v-window-item>

                                <v-window-item value="three">
                                    Домашнее задание
                                </v-window-item>
                            </v-window>
                        </v-card-text>
                    </v-card>
                </div>
            </div>


        </v-card-text>
    </v-card>

</template>

<style scoped lang="scss">
.less {
    cursor: pointer;
    padding: 5px 10px;
    border: #9197A3 1px solid;
    border-radius: 4px;
    @apply tw-my-2;
    &:hover {
        background: rgba(239, 238, 238, 0.59);
    }
}

.active-lesson {
    background-color: #d0d0d0;
}
</style>
