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
                {value: 'classwork', title: 'Задания', kind: 'classwork'},
                {value: 'homework', title: 'Д/З', kind: 'homework'},
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
        reload() {
            this.actRequestLesson(this.$route.params);
        },
    },
    created() {
        this.reload();
    }
}
</script>

<template>
    <LessonView
        :lesson="lesson?.lesson ?? null"
        :tabs="tabs"
        :tasks="lesson?.tasks ?? {}"
        :answers="lesson?.answers ?? {}"
        mode="fill"
        :group-id="$route.params.groupId"
        :back-to="backTo"
        @reload="reload"
    />
</template>
