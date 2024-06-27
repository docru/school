<script>
import {mapActions, mapGetters} from "vuex";

export default {
    name: 'Lesson',
    computed:{
        ...mapGetters('teacher', {lesson: 'getLesson'})
    },
    methods:{
        ...mapActions('teacher', ['actRequestLesson']),
    },
    created() {
        this.actRequestLesson(this.$route.params);
    }
}
</script>

<template>
<div class="">
    {{$route.params}} {{lesson}}
</div>
</template>

<style scoped lang="scss">

</style>
