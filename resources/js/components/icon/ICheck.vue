<template>
  <div class="tw-flex tw-m-2 tw-gap-2 tw-cursor-pointer" @click="update">
    <div v-if="!isEnd">{{label}}</div>
    <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
      <rect x="1.6665" y="1.5" width="18" height="18" rx="5" fill=""
            :stroke="modelValue? '#3DD598' : '#CFD3DB'" stroke-width="2"/>
      <path v-if="modelValue" fill-rule="evenodd" clip-rule="evenodd"
            d="M8.36902 9.2801L10.1319 11.0203L13.5619 6.86358C13.9134 6.43759 14.5436 6.37721 14.9696 6.72871C15.3956 7.08021 15.456 7.71049 15.1045 8.13648L10.9788 13.1365C10.6049 13.5895 9.92297 13.6243 9.50495 13.2117L6.96399 10.7034C6.57095 10.3155 6.56685 9.6823 6.95483 9.28925C7.34282 8.89621 7.97597 8.89211 8.36902 9.2801Z"
            fill="#3DD598"/>
    </svg>
    <div v-if="isEnd">{{label}}</div>
  </div>


</template>

<script>
export default {
  props: {
    modelValue: {
      type: Boolean,
      default: false
    },
    label: {
      type: String,
      default: ''
    },
    isEnd: {
      type: Boolean,
      default: true
    },
  },
  methods:{
    update(){
      // alert(1)
      this.$emit('update:modelValue', !this.modelValue)
    }
  }
}
</script>

<style scoped lang="scss">

</style>