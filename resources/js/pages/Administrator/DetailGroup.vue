<template>
    <v-card class="tw-p-[5px] md:tw-p-[10px]">
        <div
            class="tw-flex tw-w-[280px] md:tw-w-full tw-flex-col md:tw-flex-row tw-justify-start md:tw-justify-between tw-gap-2 md:tw-gap-0">
            <div class="tw-text-[18px] tw-font-bold">
                Группа "{{ getGroup.name }}". Курс "{{ getCourse.name }}"
            </div>

            <v-btn
                :class="{'tw-my-2':$vuetify.display.name === 'sm'}"
                density="comfortable"
                variant="outlined"
                color="primary"
                @click="closeSchoolDay()"
                v-if="existsOpenDay"
            >
                Закрыть день
            </v-btn>
            <v-btn
                :class="{'tw-my-2':$vuetify.display.name === 'sm'}"
                density="comfortable"
                variant="outlined"
                color="primary"
                @click="dialogDateAdd = true"
                v-else-if="!finishedCourse"
            >
                Добавить день
            </v-btn>
            <v-chip
                color="red"
                v-else
            >
                Курс завершен
            </v-chip>
        </div>


        <div>
            Учителя:
            <v-btn
                :class="$vuetify.display.name !== 'sm' ? 'ml-5' : 'ml-1'"
                color="green"
                :icon="$vuetify.display.name !== 'sm' ? 'mdi-account-plus' :'mdi-plus'"
                @click="dialogTeacher = true"
                variant="flat"
                density="compact"
                :size="$vuetify.display.name !== 'sm' ? 'x-large' : 'small'"
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
        <v-divider/>

        <div class="tw-text-[18px] tw-font-bold" v-if="$vuetify.display.name === 'sm'">
            Ученики
            <v-btn
                class="ml-2"
                color="green"
                icon="mdi-plus"
                @click="dialogDisciple = true"
                variant="flat"
                density="compact"
                size="small"
                v-if="!finishedCourse"
            ></v-btn>
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

            <template v-slot:item="{index, item, columns}" v-if="$vuetify.display.name === 'sm'">

                <v-expansion-panels>
                    <v-expansion-panel class="tw-my-1 tw-border tw-border-[rgba(221,218,218,0.49)]" elevation="0">
                        <v-expansion-panel-title expand-icon="mdi-menu-down">
                            <div class="tw-flex tw-justify-start tw-items-center">
                                <v-btn
                                    class="tw-mr-2"
                                    color="red"
                                    icon="mdi-close"
                                    @click="removeDisciple(item.id)"
                                    variant="text"
                                    density="compact"
                                    size="small"
                                    v-if="!finishedCourse"
                                ></v-btn>
                                <div class="tw-text-[16px] tw-font-[500]">{{ item.name }}</div>

                            </div>
                        </v-expansion-panel-title>
                        <v-expansion-panel-text>
                            <div v-for="(day,ind) in headersNotEmpty">

                                <div class="tw-flex tw-gap-1 tw-justify-between tw-items-center tw-my-1"
                                     :class="{ activeSlot:groupsSchoolDays[day.id]?.status === 'open' }" v-if="day">

                                    <div class="tw-flex tw-gap-2 tw-items-center">
                                        <div>{{ columns[ind].title }}</div>
                                        <v-tooltip location="top">
                                            <template v-slot:activator="{ props }">
                                                <v-icon v-bind="props" color="grey" size="small">mdi-book-open-outline
                                                </v-icon>
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


                                    <div v-if="(gsd=groupsSchoolDays[day.id])">
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
                                    </div>
                                </div>
                            </div>

                        </v-expansion-panel-text>
                    </v-expansion-panel>
                </v-expansion-panels>
            </template>

            <template v-slot:headers v-if="$vuetify.display.name === 'sm'"></template>

            <template v-slot:[`header.name`]="{column}" v-for="(day,index) in headersNotEmpty"
                      v-if="$vuetify.display.name !== 'sm'">
                УЧЕНИКИ
                <v-btn
                    class="ml-5"
                    color="green"
                    icon="mdi-account-plus"
                    @click="dialogDisciple = true"
                    variant="flat"
                    density="compact"
                    size="x-large"
                    v-if="!finishedCourse"
                ></v-btn>
            </template>

            <template v-slot:[`header.day${day?.id}`]="{column}" v-for="(day,index) in headersNotEmpty">
                <div
                    :class="{ activeSlot:groupsSchoolDays[day.id]?.status == 'open' }"
                    @click="openDataEditDialog(day.id)"
                >
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

            <template v-slot:item.name="{item, index}">
                <div v-if="item.status != 'expelled'" class="tw-flex tw-place-content-between tw-items-center">
                    <div class="tw-justify-self-start tw-text-left">
                        {{ index + 1 }}. {{ item.name }}
                        <Code
                            v-if="item.phone"
                            :code="'+' + item.phone"
                            density="compact"
                        />
                    </div>
                    <div class="tw-justify-self-end tw-mr-4">
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
                </div>
                <div class="tw-text-gray tw-flex tw-place-content-between tw-items-center" v-else>
                    <div class="tw-justify-self-start tw-text-left">
                        {{ index + 1 }}. {{ item.name }}
                        <Code
                            v-if="item.phone"
                            :code="'+' + item.phone"
                            density="compact"
                        />
                    </div>
                    <div class="tw-justify-self-end tw-mr-4">
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
                </div>

            </template>
        </v-data-table>
    </v-card>
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

    <v-dialog v-model="dialogDateAdd" width="400">
        <v-card>
            <v-card-title class="tw-pb-0">
                <div class="tw-w-full tw-flex tw-justify-between ">
                    <h6>Добавить учебный день</h6>
                    <v-icon @click="dialogDateAdd=false">mdi-close</v-icon>
                </div>
                <v-card-text class="tw-py-0">
                    <v-date-picker
                        v-model="newDate"
                        show-adjacent-months
                        :weekdays="[1, 2, 3, 4, 5, 6, 0]"
                        weeks-in-month="dynamic"
                        title=""
                        hide-header
                        locale="ru"
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

    <v-dialog v-model="dialogDateEdit" width="400">
        <v-card>
            <v-card-title class="tw-pb-0">
                <div class="tw-w-full tw-flex tw-justify-between ">
                    <h6>Изменить учебный день</h6>
                    <v-icon @click="dialogDateEdit=false">mdi-close</v-icon>
                </div>
                <v-card-text class="tw-py-0">
                    <v-date-picker
                        v-model="editDate"
                        show-adjacent-months
                        :weekdays="[1, 2, 3, 4, 5, 6, 0]"
                        weeks-in-month="dynamic"
                        title=""
                        hide-header
                        locale="ru"
                    />
                </v-card-text>
            </v-card-title>
            <v-card-actions>
                <v-btn
                    text="Сохранить"
                    @click="editSchoolDay"
                />
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import UserChoose from './UserChoose.vue';
import Code from "../../components/Code.vue";

export default {
    name: "DetailGroup",
    components: {Code, UserChoose,},
    data() {
        return {
            dialogDisciple: false,
            dialogTeacher: false,
            dialogDateAdd: false,
            newDate: new Date(),
            dialogDateEdit: false,
            editDay: null,
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
                let name = (disciple.surname ?? '') + ' ' + (disciple.name ?? '');
                disciples.push({
                    id: disciple.id,
                    name,
                    phone: disciple.phone,
                    status: disciple.status,
                    expelled_at: disciple.expelled_at,
                });
            }
            return disciples;
        },
        groupsSchoolDays() {
            return this.getGroup.groups_school_day ?? {};
        },
        editDate: {
            get() {
                return new Date(this.groupsSchoolDays[this.editDay].dateOrigin);
            },
            set(val) {
                this.groupsSchoolDays[this.editDay].dateOrigin = val;
            },
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
            'actChangeGroupSchoolDay',
            'actCloseGroupSchoolDay',
            'actRequestGroupUsers',
            'actRemoveUserFromGroup',
            'actRestoreUserToGroup',
            'actRequestAttendances',
            'actSetAttendance',
            'actReqwestCourse',
        ]),
        async addSchoolDay() {
            this.dialogDateAdd = false;
            await this.actAddGroupSchoolDay({groupId: this.getGroup.id, date: this.newDate});
            this.newDate = new Date();
        },
        async closeSchoolDay() {
            await this.actCloseGroupSchoolDay({groupId: this.getGroup.id});
        },
        openDataEditDialog(id) {
            if (this.groupsSchoolDays[id]) {
                this.editDay = id;
                this.dialogDateEdit = true;
            }
        },
        async editSchoolDay() {
            this.dialogDateEdit = false;
            let gsd = this.groupsSchoolDays[this.editDay];
            await this.actChangeGroupSchoolDay({
                groupId: this.getGroup.id,
                dayId: gsd.id,
                date: gsd.dateOrigin
            });
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

.v-date-picker-month {
    padding: 0px 12px !important;
}

.v-date-picker-month__day {
    height: 30px !important;
}

</style>
