<script>
import {mapGetters} from "vuex";
import ModuleDaysList from "./ModuleDaysList.vue";

export default {
    name: 'CourseSchedule',
  components: {ModuleDaysList},
  computed:{
    ...mapGetters('courses', ['getCourse', 'getStudyProgram']),
  }

}
</script>

<template>
  <module-days-list
      :getStudyProgram="JSON.parse(JSON.stringify(getStudyProgram))"
  />
</template>

<style scoped lang="scss">

</style>
