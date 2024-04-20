<template>
    <v-card>
        <v-card-title>
            <div class="tw-flex tw-justify-between">
                <div>Курсы</div>
                <v-btn color="primary" @click="dialog = true">Создать курс</v-btn>
            </div>
        </v-card-title>
        <v-data-table v-if="getCourses"
                      :headers="courseHeaders"
                      :items="getCourses || []"
                      :loading="getLoad"
                      @click:row="goCourse"
        >
            <template v-slot:item.actions="{ item }">
                <v-icon
                    class="me-2"
                    size="small"
                    @click.stop="editItem(item)"
                >
                    mdi-pencil
                </v-icon>
                <v-icon
                    size="small"
                    @click.stop="deleteItem(item)"
                >
                    mdi-delete
                </v-icon>
            </template>
            <template v-slot:header.actions>
                <div style="float: right">
                    <svg class="tw-cursor-pointer" @click.stop="actReqwestCourses" width="17" height="16"
                         viewBox="0 0 17 16"
                         fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M1.59964 3.45737C3.80628 0.250428 8.08538 -0.94687 11.6901 0.811276C15.6603 2.74768 17.3091 7.53595 15.3727 11.5062C13.4363 15.4764 8.64798 17.1251 4.67776 15.1887C3.08138 14.4101 1.81733 13.1467 1.03718 11.5918C0.839085 11.197 0.998557 10.7163 1.39337 10.5182C1.78819 10.3201 2.26884 10.4796 2.46694 10.8744C3.09113 12.1184 4.10063 13.1275 5.379 13.751C8.55518 15.3001 12.3858 13.9811 13.9349 10.8049C15.484 7.62876 14.1651 3.79815 10.9889 2.24902C7.94449 0.764172 4.29884 1.91436 2.63736 4.80937H3.99909C4.44082 4.80937 4.79891 5.16746 4.79891 5.60918C4.79891 6.05091 4.44082 6.409 3.99909 6.409H0.799819C0.358091 6.409 0 6.05091 0 5.60918V2.40991C0 1.96818 0.358091 1.61009 0.799819 1.61009C1.24155 1.61009 1.59964 1.96818 1.59964 2.40991V3.45737Z"
                              fill="#B0BACD"/>
                    </svg>
                </div>

            </template>

        </v-data-table>

    </v-card>
    <v-dialog v-model="dialog" width="500">
        <v-card>
            <v-card-title>
                <div class="tw-w-full tw-flex tw-justify-between ">
                    <h6> Создание курса</h6>
                    <v-icon @click="dialog=false">mdi-close</v-icon>
                </div>
                <v-card-text>
                    <v-text-field label="Название курса" v-model="nameCourse"/>
                    <v-textarea label="Описание курса" v-model="description"/>
                    <v-btn block @click="createCourse" :disabled="!description||!nameCourse">Создать курс</v-btn>
                </v-card-text>

            </v-card-title>

        </v-card>
    </v-dialog>

</template>
<script>
import {mapActions, mapGetters, mapMutations} from "vuex";

export default {
    name: 'CoursesList',
    data() {
        return {
            courseHeaders: [
                {title: 'id', key: 'id'},
                {title: 'Название', key: 'name'},
                {title: 'Описание', key: 'description'},
                {title: 'Методист', key: 'metodist'},
                {title: 'Кол-во часов', key: 'time'},
                {title: 'Кол-во уроков', key: 'time'},
                {title: 'Заданий', key: ''},
                {title: 'Д/З', key: ''},
                {title: '', align: 'end', sortable: false, key: 'actions'},
            ],
            dialog: false,
            nameCourse: '',
            description: ""
        }
    },
    computed: {
        ...mapGetters('courses', ['getCourses', 'getLoad']),
    },
    methods: {
        goCourse(item, row) {
            this.$router.push({
                name: 'courseDetail',
                params: {idCourse: row.item.id}
            })
        },
        ...mapMutations('app', ['setSnackBar']),
        ...mapActions('courses', ['actReqwestCourses', 'actCreateCourse']),
        async createCourse() {
            if (this.nameCourse.length === 0) {
                this.setSnackBar({text: 'Задайте название курса'});
            } else {
                if (await this.actCreateCourse({
                    nameCourse: this.nameCourse,
                    description: this.description
                })) {
                    this.dialog = false
                    this.nameCourse = '';
                    this.description = '';
                }
            }
        },
    },
    created() {
        this.actReqwestCourses();
    }
}
</script>


<style scoped>

</style>
