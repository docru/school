<template>
    <!-- Сводка по группе -->
    <v-card class="group-section tw-mb-4">
        <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-start md:tw-justify-between tw-gap-3">
            <div class="tw-min-w-0">
                <div class="tw-flex tw-flex-wrap tw-items-center tw-gap-2">
                    <h1 class="adf-heading tw-text-2xl tw-leading-8 tw-m-0 tw-break-words">{{ getGroup.name }}</h1>
                    <v-chip size="small" label :color="statusColor">{{ statusTitle }}</v-chip>
                </div>
                <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted tw-mt-1" v-if="getCourse.name">
                    Курс «{{ getCourse.name }}»
                </div>
            </div>

            <div class="tw-flex tw-flex-col md:tw-flex-row tw-gap-2 tw-shrink-0">
                <v-btn
                    color="primary"
                    prepend-icon="mdi-calendar-check-outline"
                    @click="closeSchoolDay()"
                    v-if="existsOpenDay"
                >
                    Закрыть день
                </v-btn>
                <v-btn
                    color="primary"
                    prepend-icon="mdi-calendar-plus-outline"
                    @click="dialogDateAdd = true"
                    v-else-if="!finishedCourse"
                >
                    Добавить день
                </v-btn>
                <template v-else>
                    <v-btn
                        variant="outlined"
                        prepend-icon="mdi-archive-arrow-down-outline"
                        @click="toArchive()"
                        v-if="getGroup.status !== 'archived'"
                    >
                        Отправить в архив
                    </v-btn>
                    <v-btn
                        variant="outlined"
                        prepend-icon="mdi-archive-arrow-up-outline"
                        @click="fromArchive()"
                        v-else
                    >
                        Вернуть из архива
                    </v-btn>
                </template>
            </div>
        </div>

        <!-- Ключевые показатели -->
        <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-3 tw-mt-4">
            <div class="group-stat">
                <div class="bp-overline">Ученики</div>
                <div class="group-stat__value">
                    {{ activeDisciplesCount }}
                    <span class="group-stat__note" v-if="expelledDisciplesCount">· отчисл. {{ expelledDisciplesCount }}</span>
                </div>
            </div>
            <div class="group-stat">
                <div class="bp-overline">Занятия</div>
                <div class="group-stat__value">
                    {{ closedDaysCount }} <span class="group-stat__note">из {{ totalDaysCount }}</span>
                </div>
                <v-progress-linear
                    :model-value="progress"
                    height="4"
                    rounded
                    color="primary"
                    bg-color="border"
                    bg-opacity="1"
                    class="tw-mt-2"
                />
            </div>
            <div class="group-stat">
                <div class="bp-overline">Старт</div>
                <div class="group-stat__value">{{ firstDayDate }}</div>
            </div>
            <div class="group-stat">
                <div class="bp-overline">{{ existsOpenDay ? 'Текущее' : 'Последнее' }}</div>
                <div class="group-stat__value">{{ lastDayDate }}</div>
            </div>
        </div>

        <!-- Учителя -->
        <div class="tw-mt-4 tw-pt-4 tw-border-t tw-border-border dark:tw-border-dark-border">
            <div class="tw-flex tw-items-center tw-justify-between tw-gap-2 tw-mb-2">
                <div class="bp-overline">Учителя</div>
                <v-btn
                    size="small"
                    variant="text"
                    color="primary"
                    prepend-icon="mdi-account-plus-outline"
                    @click="dialogTeacher = true"
                    v-if="!finishedCourse"
                >
                    Добавить
                </v-btn>
            </div>
            <div class="tw-flex tw-flex-wrap tw-gap-2" v-if="Object.keys(getTeachers).length">
                <v-chip
                    v-for="teacher in getTeachers"
                    :key="teacher.id"
                    :model-value="true"
                    :closable="!finishedCourse"
                    @click:close="removeTeacher(teacher.id)"
                >
                    {{ [teacher.surname, teacher.name].join(' ') }}
                    <span class="tw-ml-2 tw-text-text-muted dark:tw-text-dark-text-muted" v-if="teacher.phone">
                        +{{ teacher.phone }}
                    </span>
                </v-chip>
            </div>
            <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted" v-else>
                В группе пока нет учителей
            </div>
        </div>
    </v-card>

    <!-- Ученики и посещаемость -->
    <v-card class="group-section group-section--table">
        <div class="tw-flex tw-items-center tw-justify-between tw-gap-2 tw-mb-2">
            <h2 class="adf-heading tw-text-xl tw-leading-7 tw-m-0">
                Ученики
                <span class="tw-text-text-muted dark:tw-text-dark-text-muted">{{ activeDisciplesCount }}</span>
                <span class="tw-font-sans tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted tw-ml-1" v-if="expelledDisciplesCount">
                    + {{ expelledDisciplesCount }} отчисл.
                </span>
            </h2>
            <v-btn
                size="small"
                variant="text"
                color="primary"
                prepend-icon="mdi-account-plus-outline"
                @click="dialogDisciple = true"
                v-if="!finishedCourse"
            >
                Добавить
            </v-btn>
        </div>
        <div class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted tw-mb-3" v-if="$vuetify.display.name !== 'sm'">
            Отметки о посещении ставятся в открытом дне. Нажмите на номер занятия, чтобы изменить его дату.
        </div>

        <v-data-table
            :items-per-page="-1"
            class="table-sh"
            density="compact"
            :items="disciples"
            :headers="headers"
        >
            <template v-slot:bottom></template>
            <template v-slot:item="{index, item, columns}" v-if="$vuetify.display.name === 'sm'">
                <v-expansion-panels>
                    <v-expansion-panel
                        class="disciple-panel"
                        :class="{'disciple-panel--expelled': item.status === 'expelled'}"
                        elevation="0"
                    >
                        <v-expansion-panel-title expand-icon="mdi-menu-down" class="tw-px-3">
                            <div class="tw-min-w-0 tw-flex-1">
                                <div class="tw-text-[15px] tw-font-medium tw-truncate">{{ index + 1 }}. {{ item.name }}</div>
                                <div class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted tw-mt-0.5">
                                    <template v-if="item.status === 'expelled'">
                                        отчислен{{ item.expelled_at ? ' ' + fmtDate(item.expelled_at) : '' }}
                                    </template>
                                    <template v-else>
                                        посещено {{ attendedCount(item) }} из {{ closedDaysCount }}
                                    </template>
                                </div>
                            </div>
                        </v-expansion-panel-title>
                        <v-expansion-panel-text>
                            <div class="tw-flex tw-items-center tw-justify-between tw-gap-2 tw-mb-2">
                                <Code v-if="item.phone" :code="'+' + item.phone" density="compact"/>
                                <span v-else class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted">телефон не указан</span>
                                <v-btn
                                    v-if="!finishedCourse && item.status !== 'expelled'"
                                    size="small"
                                    variant="text"
                                    color="error"
                                    prepend-icon="mdi-account-remove-outline"
                                    @click="removeDisciple(item.id)"
                                >
                                    Отчислить
                                </v-btn>
                                <v-btn
                                    v-else-if="!finishedCourse"
                                    size="small"
                                    variant="text"
                                    color="success"
                                    prepend-icon="mdi-account-plus-outline"
                                    @click="restoreDisciple(item.id)"
                                >
                                    Вернуть
                                </v-btn>
                            </div>

                            <div
                                v-for="day in conductedDays"
                                :key="day.id"
                                class="day-row"
                                :class="{'day-row--open': groupsSchoolDays[day.id]?.status === 'open'}"
                            >
                                <div class="tw-flex tw-items-center tw-gap-2 tw-min-w-0">
                                    <span class="day-row__num">{{ day.title }}</span>
                                    <span class="tw-text-sm">{{ groupsSchoolDays[day.id].date }}</span>
                                    <span class="tw-text-xs tw-text-accent dark:tw-text-dark-accent" v-if="groupsSchoolDays[day.id].status === 'open'">открыт</span>
                                    <v-menu location="top">
                                        <template v-slot:activator="{ props }">
                                            <v-icon v-bind="props" color="muted" size="small">mdi-book-open-outline</v-icon>
                                        </template>
                                        <v-card class="tw-p-3 tw-text-sm tw-max-w-[300px]">
                                            <span v-html="lessons(day.lessons)"></span>
                                        </v-card>
                                    </v-menu>
                                </div>

                                <div v-if="(gsd=groupsSchoolDays[day.id])">
                                    <div
                                        v-if="!expelledDay(item, day)"
                                        @click="changeOld({groupSchoolDayId: gsd.id, userId: item.id})"
                                    >
                                        <v-checkbox
                                            color="primary"
                                            hide-details
                                            density="compact"
                                            v-model="attendance"
                                            :disabled="gsd.status !== 'open' || item.status == 'expelled'"
                                            :value="gsd.id + '_' + item.id"
                                            @change="actSetAttendance({groupSchoolDayId: gsd.id, userId: item.id})"
                                        />
                                    </div>
                                    <span v-else class="tw-text-text-muted dark:tw-text-dark-text-muted tw-pr-3">—</span>
                                </div>
                            </div>
                            <div class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted tw-mt-2" v-if="remainingDaysCount">
                                Впереди ещё {{ remainingDaysCount }} {{ plural(remainingDaysCount, ['занятие', 'занятия', 'занятий']) }}
                            </div>
                            <div class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted" v-if="!conductedDays.length">
                                Занятий ещё не было
                            </div>
                        </v-expansion-panel-text>
                    </v-expansion-panel>
                </v-expansion-panels>
            </template>

            <template v-slot:headers v-if="$vuetify.display.name === 'sm'"></template>

            <template v-slot:[`header.name`] v-if="$vuetify.display.name !== 'sm'">
                <span class="tw-pl-2">Ученик</span>
            </template>

            <template v-slot:[`header.day${day?.id}`]="{column}" v-for="(day,index) in headersNotEmpty">
                <div
                    :class="{ activeSlot:groupsSchoolDays[day.id]?.status == 'open' }"
                    @click="openDataEditDialog(day.id)"
                >
                    {{ column.title }} <br>
                    <v-tooltip location="top">
                        <template v-slot:activator="{ props }">
                            <v-icon v-bind="props" color="muted" size="small">mdi-book-open-outline</v-icon>
                        </template>
                        <span v-html="lessons(day.lessons)"></span>
                    </v-tooltip>
                    <div
                        v-if="groupsSchoolDays[day.id]?.date"
                        class="tw-text-[11px] tw-font-sans tw-font-normal"
                        :class="groupsSchoolDays[day.id].status === 'open' ? 'tw-text-accent dark:tw-text-dark-accent' : 'tw-text-text-muted dark:tw-text-dark-text-muted'"
                    >
                        {{ groupsSchoolDays[day.id].date }}
                    </div>
                    <div v-else class="tw-text-[11px] tw-text-text-muted dark:tw-text-dark-text-muted">—</div>
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
                        @click="changeOld({groupSchoolDayId: groupsSchoolDays[day.id].id, userId: item.id})"
                    >
                        <v-checkbox
                            color="primary"
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
                            color="error"
                            icon="mdi-account-remove-outline"
                            @click="removeDisciple(item.id)"
                            variant="text"
                            density="compact"
                            size="small"
                            v-if="!finishedCourse"
                        ></v-btn>
                    </div>
                </div>
                <div class="tw-text-text-muted dark:tw-text-dark-text-muted tw-flex tw-place-content-between tw-items-center" v-else>
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
                            color="success"
                            icon="mdi-account-plus-outline"
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
                    width: "36px",
                    minWidth: "36px",
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
                if (this.groupsSchoolDays[k].status === 'open') {
                    res = true;
                    break;
                }
            }
            return res;
        },
        finishedCourse() {
            let cnt = 0;
            for (const k in this.groupsSchoolDays) {
                if (this.groupsSchoolDays[k].status === 'open') {
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
        statusTitle() {
            return this.getGroup.status === 'archived' ? 'В архиве' : 'Идёт';
        },
        statusColor() {
            return this.getGroup.status === 'archived' ? 'muted' : 'success';
        },
        activeDisciplesCount() {
            return this.disciples.filter(d => d.status !== 'expelled').length;
        },
        expelledDisciplesCount() {
            return this.disciples.filter(d => d.status === 'expelled').length;
        },
        // Учебные дни группы, отсортированные по дате
        groupDaysSorted() {
            return Object.values(this.groupsSchoolDays)
                .slice()
                .sort((a, b) => String(a.dateOrigin).localeCompare(String(b.dateOrigin)));
        },
        closedDaysCount() {
            return this.groupDaysSorted.filter(d => d.status === 'close').length;
        },
        totalDaysCount() {
            return Object.keys(this.getSchedule).length;
        },
        remainingDaysCount() {
            return Math.max(this.totalDaysCount - this.groupDaysSorted.length, 0);
        },
        progress() {
            if (!this.totalDaysCount) return 0;
            return Math.round(this.closedDaysCount / this.totalDaysCount * 100);
        },
        firstDayDate() {
            return this.fmtDate(this.groupDaysSorted[0]?.dateOrigin);
        },
        lastDayDate() {
            const open = this.groupDaysSorted.find(d => d.status === 'open');
            const last = open ?? this.groupDaysSorted[this.groupDaysSorted.length - 1];
            return this.fmtDate(last?.dateOrigin);
        },
        // Дни курса, по которым у группы уже назначена дата (для мобильной раскладки)
        conductedDays() {
            return this.headersNotEmpty.filter(day => day && this.groupsSchoolDays[day.id]);
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
            'actSetGroupStatus',
        ]),
        changeOld(data) {
            if (confirm('Изменить посещение за прошедшую дату?')) {
                this.actSetAttendance(data);
            }
        },
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
        // Дата из 'Y-m-d' в 'd.m.Y'
        fmtDate(value) {
            if (!value) return '—';
            const [y, m, d] = String(value).slice(0, 10).split('-');
            return `${d}.${m}.${y}`;
        },
        plural(n, forms) {
            const abs = Math.abs(n) % 100;
            const last = abs % 10;
            if (abs > 10 && abs < 20) return forms[2];
            if (last > 1 && last < 5) return forms[1];
            if (last === 1) return forms[0];
            return forms[2];
        },
        // Сколько занятий посетил ученик
        attendedCount(item) {
            const suffix = '_' + item.id;
            return Object.values(this.attendance).filter(a => String(a).endsWith(suffix)).length;
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
        toArchive() {
            this.actSetGroupStatus({groupId: this.getGroup.id, status: 'archived'});
        },
        fromArchive() {
            this.actSetGroupStatus({groupId: this.getGroup.id, status: 'open'});
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
    background-color: rgba(139, 94, 60, 0.12) !important;
}

.group-section.v-card {
    padding: 16px;

    @media (min-width: 768px) {
        padding: 20px;
    }

    &--table {
        padding: 12px;

        @media (min-width: 768px) {
            padding: 16px;
        }
    }
}

.group-stat {
    padding: 12px;
    border: 1px solid var(--bp-border);
    border-radius: var(--bp-radius-md, 8px);
    background: var(--bp-surface-muted);

    &__value {
        font-size: 18px;
        line-height: 24px;
        font-weight: 500;
        margin-top: 4px;
    }

    &__note {
        font-size: 13px;
        font-weight: 400;
        color: var(--bp-text-muted);
    }
}

.disciple-panel.v-expansion-panel {
    margin-bottom: 8px;
    border: 1px solid var(--bp-border);
    border-radius: var(--bp-radius-md, 8px) !important;
    background: var(--bp-surface-card);

    &::after {
        display: none;
    }

    .v-expansion-panel-title {
        min-height: 52px;
    }

    .v-expansion-panel-text__wrapper {
        padding: 0 12px 12px;
    }

    &--expelled .v-expansion-panel-title {
        color: var(--bp-text-muted);
    }
}

.day-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    min-height: 40px;
    padding: 0 4px 0 8px;
    border-top: 1px solid var(--bp-border);

    &--open {
        background: rgba(139, 94, 60, 0.08);
        border-radius: var(--bp-radius-sm, 4px);
    }

    &__num {
        min-width: 24px;
        font-size: 12px;
        font-weight: 500;
        color: var(--bp-text-muted);
    }
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
        cursor: pointer;

        &:hover {
            background-color: var(--bp-surface-muted) !important;
        }
    }

    table tbody td {
        padding: 0 !important;

        &:hover {
            background-color: var(--bp-surface-muted) !important;
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
