<template>
    <v-card>
        <v-card-title>
            <div class="tw-flex tw-justify-between">
                <div>Группа "{{ getGroup.name }}". Курс "{{ getCourse.name }}"</div>
                <v-btn color="primary" @click="dialogDate = true">Добавить день</v-btn>
                <v-btn color="primary" @click="closeSchoolDay()">Закрыть день</v-btn>
                <v-btn color="primary" @click="dialogDisciple = true">Добавить ученика</v-btn>
                <v-btn color="primary" @click="dialogTeacher = true">Добавить учителя</v-btn>
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

        Учителя:
        <span v-if="Object.keys(getTeachers).length">
            <v-chip
                v-for="teacher in getTeachers"
                :key="teacher.id"
                :model-value="true"
                class="ma-2"
                color="teal"
                closable
                @click:close="removeTeacher(teacher.id)"
            >
              {{ [teacher.name, teacher.nicname, teacher.phone].join(' ') }}
            </v-chip>
        </span>
        <span v-else>В группе пока нет учителей</span>
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
            :item-class="itemRowBackground"
        >

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
                        :style="{ color: day.key !== now ? 'grey' :'black', }"
                    >
                        {{ groupsSchoolDays[day.id].date }}
                    </div>
                    <div v-else>?</div>
                </div>
            </template>

            <template v-slot:[`item.day${day?.id}`]="{item}" v-for="(day,index) in headersNotEmpty">
                <div
                    :class="{ activeSlot:groupsSchoolDays[day.id]?.status == 'open' }"
                    v-if="groupsSchoolDays[day.id]"
                >
                    <v-checkbox
                        color="green"
                        hide-details
                        density="compact"
                        style="text-align: center"
                        v-model="attendance"
                        :disabled="groupsSchoolDays[day.id]?.status != 'open'"
                        :value="day?.id + '_' + item?.id"
                        @change="actSetAttendance({groupSchoolDayId: day.id, userId: item.id})"
                    />
                </div>
            </template>

            <template v-slot:item.name="{item}">
               {{ item.name }}

                <v-btn
                    class="ml-5"
                    color="red"
                    icon="mdi-account-remove-outline"
                    @click="removeDisciple(item.id)"
                    variant="text"
                    density="compact"
                ></v-btn>
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
        ...mapGetters('groups', ['getGroup', 'getDisciples', 'getTeachers', 'getAttendances']),
        ...mapGetters('courses', ['getCourse', 'getLessons', 'getStudyProgram', 'getSchedule']),
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
                    key: 'day' + day.order,
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
                let name = (disciple.name ?? '') + ' ' + (disciple.nickname ?? '') + ' [' + disciple.phone + ']';
                disciples.push({id: disciple.id, name});
            }
            return disciples;
        },
        groupsSchoolDays() {
            return this.getGroup.groups_school_day ?? {};
        },
        lastDay() {
            let res = 0;
            for (const k in this.groupsSchoolDays) {
                res = k;
            }
            return res;
        },
        attendance: {
            get() {
                return this.getAttendances;
            },
            set(val) {
            },
        },
    },
    methods: {
        ...mapActions('groups', [
            'actRequestGroup',
            'actAddGroupSchoolDay',
            'actCloseGroupSchoolDay',
            'actRequestGroupUsers',
            'actRemoveUserFromGroup',
            'actRequestAttendances',
            'actSetAttendance',
        ]),
        ...mapActions('courses', ['actReqwestCourse']),
        itemRowBackground: function (item) {
            return 'style-1'
            console.log(item)
            return item[this.now] ? 'style-1' : 'style-2'
        },
        async addSchoolDay() {
            this.dialogDate = false;
            await this.actAddGroupSchoolDay({groupId: this.getGroup.id, date: this.newDate})
            this.newDate = new Date();
        },
        async closeSchoolDay(){
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
.style-1 {
    background-color: rgb(215, 215, 44) !important;
}

.style-2 {
    background-color: rgb(114, 114, 67) !important;
}

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
