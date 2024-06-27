<template>
    <v-card>
        <v-card-title>
            <div class="tw-flex tw-justify-between">
                <div>Группа "{{ group?.group?.name }}". Курс "{{ group?.course?.name }}"</div>
            </div>
            <v-tabs
                v-model="tab"
                bg-color=""
            >
                <v-tab value="lessons">Уроки</v-tab>
                <v-tab value="group">Группа</v-tab>
            </v-tabs>
        </v-card-title>

        <v-divider></v-divider>
        <v-card-text>
            {{ attendances }}

            <v-window v-model="tab">
                <v-window-item value="lessons">
                    <v-data-table
                        class="table-sh"
                        height="800"
                        fixed-header
                        density="compact"
                        :items="courseSchoolDays"
                        :headers="headers"
                        :hide-default-footer="true"
                        disable-pagination
                    >

                        <template v-slot:item.order="{item}">
                            {{ item.order }}. {{ groupSchoolDay(item.id, 'date') ?? '-' }}
                        </template>

                        <template v-slot:item.lessons="{item}">
                            <v-chip
                                v-for="lesson in item.lessons ?? []"
                                :key="lesson.id"
                                @click="$router.push({name: 'TeacherLesson', params: {groupId: group.group.id, lessonId:lesson.id}})"
                            >
                                {{ lesson.name }}
                            </v-chip>
                        </template>

                        <template v-slot:item.attendance="{item}">
                            <template v-if="groupSchoolDay(item.id, 'date')">
                                <v-chip v-if="attendance(item.id)" color="secondary" density="compact">
                                    Присутствовал
                                </v-chip>
                                <v-chip v-else color="red" density="compact">
                                    Отсутствовал
                                </v-chip>
                            </template>
                        </template>

                    </v-data-table>
                </v-window-item>


                <v-window-item value="group">
                    <h3>Учителя</h3>
                    <v-list v-if="teachers.length" :items="teachers" density="compact"></v-list>
                    <div v-else>Нет учителей</div>
                    <h3>Ученики</h3>
                    <v-list v-if="disciples.length" :items="disciples" density="compact"></v-list>
                    <div v-else>Нет учеников</div>
                </v-window-item>
            </v-window>

        </v-card-text>
    </v-card>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import StudyProgram from "../Methodologist/StudyProgram.vue";
import CourseSchedule from "../Methodologist/CourseSchedule.vue";

export default {
    name: "DetailGroup",
    components: {CourseSchedule, StudyProgram},
    data() {
        return {
            tab: 'lessons',
        }
    },
    computed: {
        ...mapGetters('teacher', {
            group: 'getGroup',
            courseSchoolDays: 'getCourseSchoolDay',
            groupSchoolDays: 'getGroupSchoolDay',
            getDisciples: 'getDisciples',
            getTeachers: 'getTeachers',
            attendances: 'getAttendances',
        }),
        headers() {
            let headers = [
                {
                    title: '',
                    align: 'left',
                    key: 'order',
                    width: "100px",
                    fixed: true,
                    sortable: false,
                    divider: true
                },
                {
                    title: 'Уроки',
                    align: 'left',
                    key: 'lessons',
                    // width: "300px",
                    fixed: true,
                    sortable: false,
                    divider: true
                },
                {
                    title: 'Посещение',
                    align: 'center',
                    key: 'attendance',
                    width: "50px",
                    fixed: true,
                    sortable: false,
                    divider: true
                },
            ];
            return headers;
        },
        teachers() {
            let items = [];
            for (const k in this.getTeachers) {
                items.push({title: this.getTeachers[k].name ?? '-'})
            }
            return items;
        },
        disciples() {
            let items = [];
            for (const k in this.getDisciples) {
                items.push({title: this.getDisciples[k].name ?? '-'})
            }
            return items;
        },
    },
    methods: {
        ...mapActions('teacher', ['actRequestGroup',]),
        lessons(lessons) {
            if (!lessons) {
                return '';
            } else {
                let res = [];
                for (const i in lessons) {
                    res.push((1 + parseInt(i)) + '. ' + lessons[i].name)
                }
                return res.join('; ')
            }
        },
        groupSchoolDay(id, field) {
            return this.groupSchoolDays?.[id]?.[field];
        },
        attendance(groupSchoolDayId) {
            return this.attendances.indexOf(groupSchoolDayId) > -1;
        },
    },
    created() {
        this.actRequestGroup(this.$route.params.id);
    }
}
</script>

<style lang="scss">
.activeSlot {
    background-color: rgba(66, 209, 197, 0.18) !important;
}

.table-sh {
    .v-selection-control {
        align-items: center;
        contain: layout;
        display: flex;
        flex: 1 0;
        grid-area: control;
        position: relative;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
        justify-content: center;
    }

    table thead th {
        padding: 3px !important;
        margin: 3px !important;

        &:hover {
            background-color: rgba(194, 229, 234, 0.13) !important;
        }
    }

    table tbody td {
        padding: 0 !important;
        margin: 2px !important;

        &:hover {
            background-color: rgba(139, 208, 218, 0.13) !important;
        }
    }

}

.v-calendar-month__days > .v-calendar-month__day {
    min-height: auto !important;
}

.v-calendar-weekly__day-alldayevents-container {
    min-height: 0;
}

</style>
