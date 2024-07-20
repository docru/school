<script>
import {mapActions, mapGetters} from "vuex";
import {MdPreview} from "md-editor-v3";

export default {
    name: 'Lesson',
  components: {MdPreview},
  data(){
      return {
        tab:null
      }
  },
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
  <v-tabs
      v-model="tab"
      bg-color="primary"
  >
    <v-tab value="plan">План урока</v-tab>
    <v-tab value="con">Конспект</v-tab>
  </v-tabs>

  <v-window v-model="tab">
    <v-window-item value="plan">
      <MdPreview :modelValue="lesson?.lesson?.methodical_description" />
    </v-window-item>
    <v-window-item value="con">
      <MdPreview :modelValue="lesson?.lesson?.abstract" />
    </v-window-item>
  </v-window>

</div>
</template>

<style scoped lang="scss">

</style>
