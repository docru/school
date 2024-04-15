<template>
  <div class="code-components" @click.stop="saveBuffer">
    <div >
      <code>
        {{ code }}
      </code>
      <transition name="fade">
        <div class="tw-mx-2" style="font-size:10px; color:#a5a4a4" v-if="show">скопировано в буфер обмена </div>
      </transition>
    </div>
    <div class="tw-flex tw-items-center">
      <slot name="before"></slot>
      <div class="hover-copy" @mouseover="mover" @mouseleave="mleave" @click.stop="saveBuffer">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
              d="M13.3333 6H7.33333C6.59695 6 6 6.59695 6 7.33333V13.3333C6 14.0697 6.59695 14.6667 7.33333 14.6667H13.3333C14.0697 14.6667 14.6667 14.0697 14.6667 13.3333V7.33333C14.6667 6.59695 14.0697 6 13.3333 6Z"
              :stroke="fill" stroke-linecap="round" stroke-linejoin="round"/>
          <path
              d="M3.33331 10H2.66665C2.31302 10 1.97389 9.85952 1.72384 9.60947C1.47379 9.35942 1.33331 9.02028 1.33331 8.66666V2.66666C1.33331 2.31304 1.47379 1.9739 1.72384 1.72385C1.97389 1.4738 2.31302 1.33333 2.66665 1.33333H8.66665C9.02027 1.33333 9.35941 1.4738 9.60946 1.72385C9.8595 1.9739 9.99998 2.31304 9.99998 2.66666V3.33333"
              :stroke="fill" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <slot name="default"></slot>
      <slot name="after"></slot>
    </div>
  </div>

</template>
<script>
export default {
  name:'Code',
  data() {
    return {
      fill: '#CFD3DB',
      show:false
    }
  },
  props: {
    code: {
      type: String,
      default: ''
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
<style scoped lang="scss">
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
.hover-copy {
  cursor: pointer;
}

.code-components {
  word-break: break-all;
  cursor: pointer;
  @apply
  tw-justify-between
  tw-flex
  tw-items-center
  tw-px-3
  tw-min-h-[40px] tw-bg-[#EFF6FF] tw-p-1 tw-rounded-md tw-my-2;
}
</style>