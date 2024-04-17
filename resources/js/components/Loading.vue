<template>
  <div style="display: flex; height: 500px; justify-content: center; align-items: center">
    <v-progress-circular
        model-value=""
        :color="color"
        :size="size"
        indeterminate
    />
  </div>

</template>

<script>
export default {
  name:'Loading',
  data() {
    return {

    }
  },
  props: {
    loadind:{
      type: Boolean,
      default: false
    },
    size: {
      type: Number,
      default: 74
    },
    color: {
      type: String,
      default: 'blue-lighten-3'
    }
  },
  methods: {
    mleave(){
      this.fill = '#CFD3DB'
    },
    mover() {
      this.fill = '#8B9CBE'
    },
    saveBuffer(){
      navigator.clipboard.writeText(this.code);
      this.show = true
      setTimeout(()=>{
        this.show = false
      },1000)

    }
  }
}
</script>

<style scoped>

</style>
