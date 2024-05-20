<script>
import {mapActions, mapGetters} from "vuex";
import draggable from 'vuedraggable'

export default {
    name: 'CourseSchedule',
    components: {draggable},
    data() {
        return {
            tab: null,
            dialogCreateSchoolDay: false,
            newModuleName: '',
            dialogCreateLesson: false,
            moduleForMewLesson: false,
            newLessonName: '',
            curLesson: false,
        }
    },
    computed: {
        ...mapGetters('courses', [
            'getCourse',
            'getStudyProgram',
            'getSchedule',
            'getLessons',
        ]),
        courseId() {
            return this.$route.params.idCourse;
        },
        unselectedLessons() {
            let res = [];
            for (const module of this.getStudyProgram) {
                let moduleLessons = [];
                for (const lesson of module.lessons) {
                    if (!lesson.school_day) {
                        moduleLessons.push(lesson);
                    }
                }
                let moduleN = {
                    module: module.module,
                    lessons: moduleLessons,
                };
                res.push(moduleN);
            }
            return res;
        },
        schedul() {
            let schoolDays = {};
            for (const day of this.getSchedule) {
                schoolDays[day.id] = [];
            }

            let lessonsAll = this.getLessons;
            for (const l in lessonsAll) {
                let lesson = lessonsAll[l];
                if (lesson.school_day) {
                    schoolDays[lesson.school_day].push(lesson);
                }
            }

            for (const d in schoolDays) {
                let schoolDay = schoolDays[d];
                schoolDay.sort(function compare(a, b) {
                    if (a.school_day_order < b.school_day_order) {
                        return -1;
                    }
                    if (a.school_day_order > b.school_day_order) {
                        return 1;
                    }
                    return 0;
                });
            }
            return schoolDays;
        },
    },
    methods: {
        ...mapActions('courses', [
            'actCreateCourseSchoolDay',
            'actDeleteCourseSchoolDay',
        ]),
        createSchoolDay() {
            this.actCreateCourseSchoolDay({course_id: this.courseId});
        },
        async deleteCourseSchoolDay(day) {
            if (confirm('Вы уверены, что хотите удалить учебный день?')) {
                await this.actDeleteCourseSchoolDay(day);
            }
        },
        clone(i){
            console.log(i)
        },
        onEnd(e) {
            let dayId = e.to.dataset.dayId;
            let lessonId = e.item.dataset.lessonId;
            let newIndex = e.newIndex;

            let lesson = this.getLessons[lessonId];

            let lessons = [];
            let lessonsAll = this.getLessons;
            for (const i in lessonsAll) {
                if (lessonsAll[i]?.school_day == dayId && lessonsAll[i]?.id != lessonId) {
                    lessons.push(lessonsAll[i]);
                }
            }
            lessons.sort(function compare(a, b) {
                if (a.school_day_order < b.school_day_order) {
                    return -1;
                }
                if (a.school_day_order > b.school_day_order) {
                    return 1;
                }
                return 0;
            }).splice(newIndex, 0, lesson);

            for (const order in lessons) {
                lessons[order].school_day_order = order;
            }

            lesson.school_day = dayId;
        },
        removeLesson(id) {
            this.getLessons[id].school_day = null;
            this.getLessons[id].school_day_order = null;
        },
    },
}
</script>

<template>
    <v-card variant="text">
        <v-card-title>
            <div class="tw-flex">
                <div>Расписание курса</div>
                <v-spacer></v-spacer>
                <div class="tw-flex tw-gap-3">
                    <v-btn @click="createSchoolDay()">Добавить учебный день</v-btn>
                </div>
            </div>
        </v-card-title>
        <v-card-text>
            <div class="tw-flex tw-gap-3">
                <div class="tw-w-1/3" style="height: calc(100vh - 300px); overflow: auto">
                    <v-card hover elevation="0"
                            style="padding: 5px 10px; margin: 15px 0 "
                            v-for="moduleItem in getStudyProgram"
                            :key="moduleItem.id">
                        <v-card-title>
                            <div class="tw-flex">
                                Модуль {{ moduleItem.module.name }}
                            </div>
                        </v-card-title>
                        <v-card-text>
                            <draggable
                                :group="{ name: moduleItem.module.name, pull: 'clone', put: false}"
                                :list="moduleItem.lessons"
                                item-key=""
                                handle=".item"
                                @end="onEnd"

                            >
                                <template #item="{element}">
                                    <div
                                        class="lesson"
                                        :class="{'item':!element.school_day}"
                                        :data-lesson-id="element.id"
                                    >
                                        <div class="tw-flex tw-justify-between">
                                            <div>урок: {{ element.name }}</div>
                                        </div>
                                    </div>
                                </template>
                            </draggable>
                        </v-card-text>
                        <v-divider/>
                    </v-card>
                </div>
                <div class="tw-w-2/3">
                    <v-card hover elevation="2" style="padding: 5px 10px; margin: 15px 0 ">
                        <v-card-title>
                            <div class="tw-flex">
                                <div>Расписание</div>
                            </div>
                        </v-card-title>


                        <v-card-text>
                            <v-card v-for="day in getSchedule" :key="day.id">
                                <v-card-title>
                                    <div class="tw-flex">
                                        <div class="">{{ day.order }}</div>
                                        <v-spacer/>
                                        <v-btn
                                            icon="mdi-trash-can-outline"
                                            size="small"
                                            variant="text"
                                            @click="deleteCourseSchoolDay(day)"
                                        />
                                    </div>
                                </v-card-title>
                                <v-card-text>
                                    <draggable
                                        class="dragArea list-group w-full"
                                        :data-day-id="day.id"
                                        :list="schedul[day.id]"
                                        :group="{ name: 'to_' + day.id, put: true }"
                                        @end="onEnd"
                                        item-key=""
                                    >
                                        <template #item="{element}">
                                            <div class="lesson" :data-lesson-id="element.id">
                                                <div class="tw-flex tw-justify-between">
                                                    <div>урок: {{ element.name }}</div>
                                                    <v-icon
                                                        size="small"
                                                        color="grey"
                                                        @click="removeLesson(element.id)"
                                                    >mdi-trash-can-outline
                                                    </v-icon>
                                                </div>
                                            </div>
                                        </template>
                                    </draggable>
                                </v-card-text>
                            </v-card>
                        </v-card-text>
                    </v-card>
                </div>
            </div>


        </v-card-text>
    </v-card>

</template>

<style scoped lang="scss">
.lesson {
    @apply tw-w-[98%]   tw-m-2 tw-p-2;
    border: 1px solid rgba(128, 128, 128, 0.42);
    border-radius: 4px;

    &:hover {
        background-color: rgba(187, 189, 193, 0.2);
    }

}
.item {
    background-color: rgba(92, 165, 205, 0.25);
}

.not-draggable {
    cursor: no-drop;
}
</style>
