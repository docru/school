<template>
    <div>
        <v-card>
            <v-card-title>
                <div class="tw-flex">
                    <div>
                        <div>
                            Название курса: <strong>{{ getCourse?.name }}</strong>
                        </div>
                    </div>
                    <v-spacer></v-spacer>
                    <div class="tw-text-sm tw-text-zinc-500">
                        <div>Количество уроков: {{ cntLessons }}</div>
                        <div>Количество часов: {{ cntHours }}</div>
                    </div>
                    <v-icon
                        @click="save()"
                        style="margin-left: 10px;"
                        icon="mdi-content-save-outline"
                        :color="isSaved ? 'success' : 'error'"
                    />
                </div>
                <v-tabs
                    v-model="tab"
                    bg-color=""
                >
                    <v-tab value="studyProgram">Программа</v-tab>
                    <v-tab value="courseSchedule">Расписание</v-tab>
                </v-tabs>

            </v-card-title>
            <v-divider></v-divider>
            <v-card-text>

                <v-window v-model="tab">
                    <v-window-item value="studyProgram">
                        <StudyProgram/>
                    </v-window-item>
                    <v-window-item value="courseSchedule">
                        <CourseSchedule/>
                    </v-window-item>
                </v-window>

            </v-card-text>
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
        }
    },
    computed: {
        ...mapGetters('methodologist', ['getCourse', 'getStudyProgram', 'isSaved']),
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
                    let hours = this.getStudyProgram[k].lessons[l].hours;
                    cnt = cnt + hours;
                }
            }
            return cnt;
        },
    },
    methods: {
        ...mapActions('methodologist', ['actReqwestCourse', 'actSaveCourse']),
        async save() {
            await this.actSaveCourse();
        },
        autoSaveStart() {
            this.timerSaveId = setInterval(() => {
                if (!this.isSaved) {
                    this.save();
                }
            }, 1000);
        },
        autoSaveStop() {
            if (this.timerSaveId) {
                clearInterval(this.timerSaveId);
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
    unmounted() {
        // остановить автоматическое сохранение
        this.autoSaveStop();
    },
}
</script>

<style scoped lang="scss">

</style>
