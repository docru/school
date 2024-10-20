<script>
import {mapActions, mapGetters} from "vuex";
import {MdCatalog, MdPreview} from "md-editor-v3";

export default {
  name: 'Lesson',
  data(){
    return {
      id : 'preview-only',
      scrollElement:document.documentElement
    }
  },
  components: {MdCatalog, MdPreview},
  computed: {
    ...mapGetters('disciple', {lesson: 'getLesson'})
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
<!--  {{ $route.params }} {{ lesson }} <br>-->
  <h1>{{lesson?.lesson?.name}}</h1>
  <div class="tw-flex">
<!--    <MdCatalog :editorId="id" :scrollElement="scrollElement"-->
<!--                     style="position: fixed; "-->
<!--    />-->

    <MdPreview   :editorId="id" :modelValue="lesson?.lesson?.abstract" />


  </div>
</template>

<style scoped lang="scss">

</style>
