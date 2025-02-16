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
                <v-tab value="group" v-if="!getProfile.roles.includes('disciple')">Группа</v-tab>
            </v-tabs>
        </v-card-title>

        <v-divider></v-divider>
        <v-card-text>

            <v-window v-model="tab">
                <v-window-item value="lessons">
                    <v-data-table
                        :mobile="true"
                        fixed-header
                        density="compact"
                        :items="courseSchoolDays"
                        items-per-page="100"
                        :headers="headers"
                        :hide-default-footer="true"
                        disable-pagination
                    >
                        <template #bottom></template>
                        <template v-slot:item.order="{item}">
                            <span class="tw-text-[12px]">
                                {{ item.order }}. {{ groupSchoolDay(item.id, 'date') ?? '-' }}
                            </span>
                        </template>
                        <template v-slot:headers v-if="$vuetify.display.name === 'sm'"></template>

                        <template #item="{ item,columns }" v-if="$vuetify.display.name === 'sm'">
                            <div v-if="$vuetify.display.name === 'sm'"
                                 class="tw-border tw-rounded tw-border-[rgba(221,218,218,0.49)] tw-mt-2 tw-p-2"
                                 :class="{
                                // 'tw-bg-[grey]':!item.attendance
                                }">
                                <div class="tw-flex tw-justify-between">
                                    {{ item.order }}. {{ groupSchoolDay(item.id, 'date') ?? '-' }}

                                    <template v-if="groupSchoolDay(item.id, 'date')">
                                        <span v-if="attendance(item.id)" class="tw-text-[12px]">
                                          Присутств.
                                        </span>
                                        <span v-else class="tw-text-[12px]">
                                          Отсутств.
                                        </span>
                                    </template>
                                </div>
                                Уроки:
                                <div v-for="les in item.lessons" :key="les.id"
                                     style="color: lightskyblue; margin: 5px 0;">
                                    <template v-if="groupSchoolDay(item.id, 'status') === 'close'">
                                        <router-link
                                            style="border-bottom: 1px dashed lightskyblue;"
                                            :to="{path:`/disciple/lesson/${group?.group?.id}/${les.id}`}">
                                            {{ les.name }}
                                        </router-link>
                                    </template>
                                    <template v-else>
                                        {{ les.name }}
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template v-slot:item.lessons="{item}">
                            <div
                                class="tw-my-1"
                                v-for="(lesson,index) in item.lessons ?? []"
                                :key="lesson.id"
                            >
                                <template v-if="groupSchoolDay(item.id, 'status') === 'close'">
                                <span class="tw-text-[lightskyblue]"
                                      style="border-bottom: 1px dashed lightskyblue;cursor: pointer "
                                      @click="$router.push({name: 'DiscipleLesson', params: {groupId: group.group.id, lessonId:lesson.id}})"
                                >
                                    {{ index + 1 }}. {{ lesson.name }}
                                </span>
                                </template>
                                <template v-else>
                                    <span class="tw-text-[grey]">
                                        {{ index + 1 }}. {{ lesson.name }}
                                    </span>
                                </template>

                            </div>
                        </template>

                        <template v-slot:item.attendance="{item}">
                            <template v-if="groupSchoolDay(item.id, 'date')">
                                <v-chip v-if="attendance(item.id)" color="secondary" density="compact">
                                    Присутств.
                                </v-chip>
                                <v-chip v-else color="red" density="compact">
                                    Отсутств.
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
        ...mapGetters('profile', ['getProfile']),
        ...mapGetters('disciple', {
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
                    width: "130px",
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
        ...mapActions('disciple', ['actRequestGroup',]),
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
            let gsds = this.groupSchoolDays[groupSchoolDayId];
            return Object.values(this.attendances).indexOf(gsds.id) > -1;
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
