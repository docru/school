<template>
  <div class="IChartLine chart_type_btn">
    <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M3.57121 10.9169C3.68415 8.87404 4.60399 6.95974 6.12835 5.5951C7.65272 4.23047 9.65676 3.52727 11.6996 3.64022" stroke="#6D7488" stroke-linecap="round" stroke-linejoin="round"/>
      <path d="M1 1.5V13.5H13" stroke="#6D7488" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </div>
</template>
<script>
export default {

}
</script>

<style lang="scss">
.chart_type_btn_active.IChartLine svg path{
  @apply tw-transition-all;
  stroke: #60A5FA;
}
</style>