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
    name: 'CoursesDetail',
    components: {StudyProgram, CourseSchedule},
    data() {
        return {
            tab: 'studyProgram',
        }
    },
    computed: {
        ...mapGetters('courses', ['getCourse', 'getStudyProgram']),
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
        ...mapActions('courses', ['actReqwestCourse']),
    },
    async mounted() {
        await this.actReqwestCourse({id: this.courseId});
    }
}
</script>

<style scoped lang="scss">

</style>
