<script>
import {mapActions, mapGetters} from "vuex";

export default {
    name: 'Lesson',
    computed:{
        ...mapGetters('disciple', {lesson: 'getLesson'})
    },
    methods:{
        ...mapActions('disciple', ['actRequestLesson']),
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
