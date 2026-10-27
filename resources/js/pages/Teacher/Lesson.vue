<script>
import {mapActions, mapGetters} from "vuex";
import LessonView from "../../components/LessonView.vue";

export default {
    name: 'Lesson',
    components: {LessonView},
    data() {
        return {
            tabs: [
                {value: 'plan', title: 'План урока', short: 'План', field: 'methodical_description'},
                {value: 'abstract', title: 'Конспект', field: 'abstract'},
                {value: 'classwork', title: 'Задания на уроке', short: 'Задания', kind: 'classwork'},
                {value: 'homework', title: 'Домашнее задание', short: 'Д/З', kind: 'homework'},
            ],
        }
    },
    computed: {
        ...mapGetters('teacher', {lesson: 'getLesson'}),
        backTo() {
            return {name: 'TeacherDetailGroup', params: {id: this.$route.params.groupId}};
        },
    },
    methods: {
        ...mapActions('teacher', ['actRequestLesson']),
    },
    created() {
        this.actRequestLesson(this.$route.params);
    }
}
</script>

<template>
    <!-- Учителю блоки только на ознакомление: ответы учеников в разделе «Проверка» -->
    <LessonView
        :lesson="lesson?.lesson ?? null"
        :tabs="tabs"
        :tasks="lesson?.tasks ?? {}"
        mode="view"
        :back-to="backTo"
    />
</template>
