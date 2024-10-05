<template>
    <v-card>
        <v-card-title>
            <div class="md:tw-flex tw-justify-between">
                <div>Группа "{{ getGroup.name }}". Курс "{{ getCourse.name }}"</div>

                <v-btn
                    density="comfortable"
                    variant="outlined"
                    color="primary"
                    @click="closeSchoolDay()"
                    v-if="existsOpenDay"
                >
                    Закрыть день
                </v-btn>
                <v-btn
                    density="comfortable"
                    variant="outlined"
                    color="primary"
                    @click="dialogDate = true"
                    v-else-if="!finishedCourse"
                >
                    Добавить день
                </v-btn>
                <v-chip
                    class="ma-2"
                    color="red"
                    v-else
                >
                    Курс завершен
                </v-chip>
            </div>

        </v-card-title>

        <v-dialog v-model="dialogDisciple">
            <v-card>
                <v-card-title>
                    <div class="tw-w-full tw-flex tw-justify-between ">
                        <h6>Добавить ученика</h6>
                        <v-icon @click="dialogDisciple=false">mdi-close</v-icon>
                    </div>
                    <v-card-text>
                        <UserChoose @choosedUser="dialogDisciple = false; loadUsers()" :group="getGroup"/>
                    </v-card-text>
                </v-card-title>
            </v-card>
        </v-dialog>
        <v-dialog v-model="dialogTeacher">
            <v-card>
                <v-card-title>
                    <div class="tw-w-full tw-flex tw-justify-between ">
                        <h6>Добавить учителя</h6>
                        <v-icon @click="dialogTeacher=false">mdi-close</v-icon>
                    </div>
                    <v-card-text>
                        <UserChoose @choosedUser="dialogTeacher = false; loadUsers()" role="teacher" :group="getGroup"/>
                    </v-card-text>
                </v-card-title>
            </v-card>
        </v-dialog>

        <v-dialog v-model="dialogDate" width="400">
            <v-card>
                <v-card-title>
                    <div class="tw-w-full tw-flex tw-justify-between ">
                        <h6>Добавить учебный день</h6>
                        <v-icon @click="dialogDate=false">mdi-close</v-icon>
                    </div>
                    <v-card-text>
                        <v-date-picker
                            v-model="newDate"
                            title=""
                            hide-header

                        />
                    </v-card-text>
                </v-card-title>
                <v-card-actions>
                    <v-btn
                        text="Создать"
                        @click="addSchoolDay"
                    />
                </v-card-actions>
            </v-card>
        </v-dialog>

        <div class="tw-mx-4">
            Учителя:
            <v-btn
                class="ml-5"
                color="green"
                icon="mdi-account-plus"
                @click="dialogTeacher = true"
                variant="text"
                density="compact"
                size="x-large"
                v-if="!finishedCourse"
            ></v-btn>
            <span v-if="Object.keys(getTeachers).length">
            <v-chip
                v-for="teacher in getTeachers"
                :key="teacher.id"
                :model-value="true"
                class="ma-2"
                color="teal"
                :closable="!finishedCourse"
                @click:close="removeTeacher(teacher.id)"
            >
              {{ [teacher.surname, teacher.name, teacher.phone].join(' ') }}
            </v-chip>
        </span>
            <span v-else>В группе пока нет учителей</span>
        </div>

        <v-data-table
            :items-per-page="-1"
            class="table-sh"
            height="800"
            fixed-header
            density="compact"
            :items="disciples"
            :headers="headers"
            :hide-default-footer="true"
            disable-pagination
        >

            <template v-slot:[`header.name`]="{column}" v-for="(day,index) in headersNotEmpty">
                УЧЕНИКИ
                <v-btn
                    class="ml-5"
                    color="green"
                    icon="mdi-account-plus"
                    @click="dialogDisciple = true"
                    variant="text"
                    density="compact"
                    size="x-large"
                    v-if="!finishedCourse"
                ></v-btn>
            </template>

            <template v-slot:[`header.day${day?.id}`]="{column}" v-for="(day,index) in headersNotEmpty">
                <div :class="{ activeSlot:groupsSchoolDays[day.id]?.status == 'open' }">
                    {{ column.title }} <br>
                    <v-tooltip location="top">
                        <template v-slot:activator="{ props }">
                            <v-icon v-bind="props" color="grey" size="small">mdi-book-open-outline</v-icon>
                        </template>
                        <span v-html="lessons(day.lessons)"></span>
                    </v-tooltip>
                    <div
                        v-if="groupsSchoolDays[day.id]?.date"
                        :style="{ color: groupsSchoolDays[day.id].status !== 'open' ? 'grey' :'black', }"
                    >
                        {{ groupsSchoolDays[day.id].date }}
                    </div>
                    <div v-else>?</div>
                </div>
            </template>

            <template
                v-slot:[`item.day${day?.id}`]="{item}"
                v-for="(day,index) in headersNotEmpty"
            >
                <template v-if="(gsd=groupsSchoolDays[day.id])">
                    <div
                        :class="{ activeSlot:gsd?.status === 'open' }"
                        v-if="!expelledDay(item, day)"
                    >
                        <v-checkbox
                            color="green"
                            hide-details
                            density="compact"
                            style="text-align: center"
                            v-model="attendance"
                            :disabled="gsd.status !== 'open' || item.status == 'expelled'"
                            :value="gsd.id + '_' + item.id"
                            @change="actSetAttendance({groupSchoolDayId: groupsSchoolDays[day.id].id, userId: item.id})"
                        />
                    </div>
                    <div v-else>-</div>
                </template>
            </template>

            <template v-slot:item.name="{item}">
                <div v-if="item.status != 'expelled'">
                    {{ item.name }}
                    <v-btn
                        class="ml-5"
                        color="red"
                        icon="mdi-account-remove-outline"
                        @click="removeDisciple(item.id)"
                        variant="text"
                        density="compact"
                        size="small"
                        v-if="!finishedCourse"
                    ></v-btn>
                </div>
                <div class="tw-text-gray" v-else>
                    {{ item.name }}
                    <v-btn
                        class="ml-5"
                        color="green"
                        icon="mdi-account-plus"
                        @click="restoreDisciple(item.id)"
                        variant="text"
                        density="compact"
                        size="small"
                        v-if="!finishedCourse"
                    ></v-btn>
                </div>

            </template>
        </v-data-table>
    </v-card>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import UserChoose from './UserChoose.vue';

export default {
    name: "DetailGroup",
    components: {UserChoose,},
    data() {
        return {
            dialogDisciple: false,
            dialogTeacher: false,
            dialogDate: false,
            newDate: new Date(),
            now: 'day3',
        }
    },
    computed: {
        ...mapGetters('administrator', [
            'getGroup',
            'getDisciples',
            'getTeachers',
            'getAttendances',
            'getCourse',
            'getLessons',
            'getStudyProgram',
            'getSchedule'
        ]),
        headers() {
            let headers = [{
                title: 'Имя',
                align: 'center',
                key: 'name',
                width: "300px",
                fixed: true,
                sortable: false,
                divider: true
            }];
            for (const dayId in this.getSchedule) {
                let day = this.getSchedule[dayId];
                headers.push({
                    title: day.order,
                    align: 'center',
                    key: 'day' + day.id,
                    id: day.id,
                    width: "20px",
                    sortable: false
                })
            }
            headers.push({title: '', key: '', sortable: false});
            return headers;
        },
        groupId() {
            return this.$route.params.id;
        },
        headersNotEmpty() {
            return this.headers.map((val) => {
                if (val.key !== 'name' && val.key !== '') {
                    val.lessons = this.lessonsDays[val.id]
                    return val;
                }
            });
        },
        lessonsDays() {
            let res = {};
            for (const l in this.getLessons) {
                let lesson = this.getLessons[l];
                let day = lesson.school_day;
                if (!!day) {
                    if (!res[day]) {
                        res[day] = [];
                    }
                    res[day].push(lesson.name);
                }
            }
            return res;
        },
        disciples() {
            let disciples = [];
            for (const d in this.getDisciples) {
                let disciple = this.getDisciples[d];
                let name = (disciple.surname ?? '') + ' ' + (disciple.name ?? '') + ' [' + disciple.phone + ']';
                disciples.push({id: disciple.id, name, status: disciple.status, expelled_at: disciple.expelled_at});
            }
            return disciples;
        },
        groupsSchoolDays() {
            return this.getGroup.groups_school_day ?? {};
        },
        existsOpenDay() {
            let res = false;
            for (const k in this.groupsSchoolDays) {
                if (this.groupsSchoolDays[k].status == 'open') {
                    res = true;
                    break;
                }
            }
            return res;
        },
        finishedCourse() {
            let cnt = 0;
            for (const k in this.groupsSchoolDays) {
                if (this.groupsSchoolDays[k].status == 'open') {
                    break;
                }
                cnt++;
            }
            return cnt >= Object.keys(this.getSchedule).length;
        },
        attendance: {
            get() {
                return this.getAttendances;
            },
            set() {
            },
        },
    },
    methods: {
        ...mapActions('administrator', [
            'actRequestGroup',
            'actAddGroupSchoolDay',
            'actCloseGroupSchoolDay',
            'actRequestGroupUsers',
            'actRemoveUserFromGroup',
            'actRestoreUserToGroup',
            'actRequestAttendances',
            'actSetAttendance',
            'actReqwestCourse',
        ]),
        async addSchoolDay() {
            this.dialogDate = false;
            await this.actAddGroupSchoolDay({groupId: this.getGroup.id, date: this.newDate})
            this.newDate = new Date();
        },
        async closeSchoolDay() {
            await this.actCloseGroupSchoolDay({groupId: this.getGroup.id})
        },
        lessons(lessons) {
            let res = [];
            for (const i in lessons) {
                res.push((1 + parseInt(i)) + '. ' + lessons[i])
            }
            return res.join('<br/>')
        },
        loadUsers() {
            this.actRequestGroupUsers({groupId: this.groupId});
        },
        removeTeacher(id) {
            if (confirm('Удалить учителя?')) {
                this.actRemoveUserFromGroup({groupId: this.groupId, userId: id});
            }
        },
        removeDisciple(id) {
            if (confirm('Отчислить ученика?')) {
                this.actRemoveUserFromGroup({groupId: this.groupId, userId: id});
            }
        },
        restoreDisciple(id) {
            if (confirm('Восстановить ученика?')) {
                this.actRestoreUserToGroup({groupId: this.groupId, userId: id});
            }
        },
        expelledDay(user, day) {
            if (user.status != 'expelled') { // не отчислен
                return false;
            }
            let expelled_at = Date.parse(user.expelled_at);
            let gsdTm = Date.parse(this.groupsSchoolDays[day.id].dateOrigin);
            return gsdTm > expelled_at;
        },
    },
    created() {
        this.loadUsers();
        this.actRequestGroup(this.groupId).then(() => {
            this.actReqwestCourse({id: this.getGroup.course_id});
        });
        this.actRequestAttendances({groupId: this.groupId});
    }
}
</script>


<style lang="scss">
.activeSlot {
    background-color: rgba(66, 209, 197, 0.18) !important;
}

.expelled {
    background-color: grey !important;
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

<!--
// Добавить очередной день
// Список уроков в дне
// Добавить учителя
// Отобразиь список учителей
// Удалить учителя
// Отметить посещение
//Удалить/отчислить ученика из группы (если посетил хоть один урок)
отображение отчисленного ученика
-->
