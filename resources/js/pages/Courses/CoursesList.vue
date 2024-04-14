<script>
import {mapActions, mapGetters, mapMutations} from "vuex";

export default {
    name: 'CoursesList',
    data() {
        return {
            nameCourse: ''
        }
    },
    computed: {
        ...mapGetters('courses', ['getCourses']),
    },
    methods: {
        ...mapMutations('app', ['setSnackBar']),
        ...mapActions('courses', ['actReqwestCourses', 'actCreateCourse']),
        async createCourse() {
            if (this.nameCourse.length === 0) {
                this.setSnackBar({text: 'Задайте название курса'});
            } else {
                if (await this.actCreateCourse({nameCourse: this.nameCourse})) {
                    this.nameCourse = '';
                }
            }
        },
    },
    created() {
        this.actReqwestCourses();
    }
}
</script>

<template>
    <div class="main">
        <h1>Курсы</h1>
        <v-card>
            <v-card-text>
                <div style="display: flex; gap: 5px;">
                    <v-text-field density="compact" variant="outlined" v-model="nameCourse"></v-text-field>
                    <v-btn color="primary" @click="createCourse()">Создать курс</v-btn>
                </div>
            </v-card-text>
        </v-card>

        {{ getCourses }}
    </div>
</template>

<style scoped>

</style>
