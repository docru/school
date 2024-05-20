<template>
    <v-card>
        <v-card-title>
            <div class="tw-flex tw-justify-between">
                <div>Группа "{{ getGroup.name }}". Курс "{{ getCourse.name }}"</div>
                <v-btn color="primary" @click="dialogDate = true">Добавить день</v-btn>
                <v-btn color="primary" @click="dialog = true">Добавить ученика</v-btn>
            </div>
        </v-card-title>

        <v-dialog v-model="dialog">
            <v-card>
                <v-card-title>
                    <div class="tw-w-full tw-flex tw-justify-between ">
                        <h6>Добавить ученика</h6>
                        <v-icon @click="dialog=false">mdi-close</v-icon>
                    </div>
                    <v-card-text>
                        <DisciplesChoose @choosedUser="dialog = false" :group="getGroup"/>
                    </v-card-text>
                </v-card-title>
            </v-card>
        </v-dialog>

        <v-dialog v-model="dialogDate" width="400">
            <v-card>
                <v-card-title>
                    <div class="tw-w-full tw-flex tw-justify-between ">
                        <h6>Добавить учебный день</h6>
                        <v-icon @click="dialog=false">mdi-close</v-icon>
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

            <template v-slot:[`header.${it?.key}`]="{column}" v-for="(it,index) in headersNotEmpty">
                <div :class="{ activeSlot:it.id == lastDay }">
                    {{ column.title }} <br>
                    <v-tooltip location="top">
                        <template v-slot:activator="{ props }">
                            <v-icon v-bind="props" color="grey" size="small">mdi-book-open-outline</v-icon>
                        </template>
                        {{it.lessons}}
                        <span v-html="'1. 10 заповедей<br> 2. Значение креста'"></span>
                    </v-tooltip>
                    <div
                        v-if="groupsSchoolDays[it.id]?.date"
                        :style="{ color: it.key !== now ? 'grey' :'black', }"
                    >
                        {{ groupsSchoolDays[it.id].date }}
                    </div>
                    <div></div>
                </div>
            </template>

            <template v-slot:[`item.${it?.key}`]="{item}" v-for="(it,index) in headersNotEmpty">
                <div :class="{ activeSlot:it.id == lastDay }" v-if="groupsSchoolDays[it.id]">
                    <v-checkbox
                        :disabled="it !== now"
                        v-model="item[it.key]"
                        color="green"
                        hide-details
                        density="compact"
                        style="text-align: center"
                    />
                </div>

            </template>


        </v-data-table>
{{lessonsDays}}

    </v-card>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import DisciplesChoose from './DisciplesChoose.vue';

export default {
    name: "DetailGroup",
    components: {DisciplesChoose,},
    data() {
        return {
            dialog: false,
            dialogDate: false,
            newDate: new Date(),
            now: 'day3',
            items: [
                {name: 'Иван Петров', day1: true, day2: true, day3: true, day4: true, day5: true,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: true,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: true,},
                {name: 'Иван Иванов', day1: false, day2: true, day3: false, day4: true, day5: true,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: true,},
                {name: 'Иван Иванов', day1: true, day2: false, day3: true, day4: true, day5: true,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: false, day5: true,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: false, day4: false, day5: true,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: true,},
                {name: 'Иван Петров', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
                {name: 'Иван Петров', day1: true},
            ],
        }
    },
    computed: {
        ...mapGetters('groups', ['getGroup', 'getDisciples']),
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
                disciples.push({name: (disciple.name ?? '') + ' ' + (disciple.nickname ?? '') + ' [' + disciple.phone + ']'})
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
    },
    methods: {
        ...mapActions('groups', ['actRequestGroup', 'actRequestDisciples', 'actAddGroupSchoolDay']),
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
    },
    created() {
        let groupId = this.$route.params.id;
        this.actRequestDisciples(groupId);
        this.actRequestGroup(groupId).then(() => {
            this.actReqwestCourse({id: this.getGroup.course_id})
        });
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
        //background-color: #FC5A5A!important;
        //border: 1px solid indianred!important;/
        padding: 3px !important;
        margin: 3px !important;

        &:hover {
            background-color: rgba(194, 229, 234, 0.13) !important;
        }
    }

    table tbody td {
        //background-color: #FC5A5A!important;
        //border: 1px solid indianred!important;/
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
//Добавить очередной день
Список уроков в дне
Добавить учителя
Отобразиь список учителей
Удалить учителя
Удалиь ученика из группы (толькол на этапе формирования)
Отчислить ученика из группы
Отметить посещение
-->
