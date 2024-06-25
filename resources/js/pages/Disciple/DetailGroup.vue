<template>
    <v-card>
        <v-card-title>
            <div class="tw-flex tw-justify-between">
                                <div>Группа "{{ group?.group?.name }}". Курс "{{ group?.course?.name }}"</div>
            </div>
        </v-card-title>


        <!--        Учителя:-->
        <!--        <span v-if="Object.keys(getTeachers).length">-->
        <!--            <v-chip-->
        <!--                v-for="teacher in getTeachers"-->
        <!--                :key="teacher.id"-->
        <!--                :model-value="true"-->
        <!--                class="ma-2"-->
        <!--                color="teal"-->
        <!--            >-->
        <!--              {{ [teacher.name, teacher.nicname, teacher.phone].join(' ') }}-->
        <!--            </v-chip>-->
        <!--        </span>-->


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
                    @click="$router.push({name: 'DiscipleLesson', params: {groupId: group.group.id, lessonId:lesson.id}})"
                >
                    {{lesson.name}}
                </v-chip>
            </template>

        </v-data-table>
    </v-card>
</template>

<script>
import {mapActions, mapGetters} from "vuex";

export default {
    name: "DetailGroup",
    data() {
        return {}
    },
    computed: {
        ...mapGetters('disciple', {
            group: 'getGroup',
            courseSchoolDays: 'getCourseSchoolDay',
            groupSchoolDays: 'getGroupSchoolDay',
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
