<script>
import {mapActions, mapGetters} from "vuex";
import LessonView from "../../components/LessonView.vue";

export default {
    name: 'Lesson',
    components: {LessonView},
    data() {
        return {
            // Плана урока ученик не видит: бэкенд отдаёт ему только конспект
            tabs: [
                {value: 'abstract', title: 'Конспект', field: 'abstract'},
            ],
        }
    },
    computed: {
        ...mapGetters('disciple', {lesson: 'getLesson'}),
        backTo() {
            return {name: 'DiscipleDetailGroup', params: {id: this.$route.params.groupId}};
        },
    },
    methods: {
        ...mapActions('disciple', ['actRequestLesson']),
    },
    created() {
        this.actRequestLesson(this.$route.params);
    }
}
</script>

<template>
    <LessonView :lesson="lesson?.lesson ?? null" :tabs="tabs" :back-to="backTo"/>
</template>
