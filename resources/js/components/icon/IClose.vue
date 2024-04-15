<template>
  <svg class="icon_close" viewBox="0 0 10 10">
    <path fill-rule="evenodd" clip-rule="evenodd" d="M5.94 5L9.46 8.521a.667.667 0 0 1-.943.943L4.997 5.942 1.471 9.464a.667.667 0 0 1-.942-.944L4.055 5 .53 1.47A.667.667 0 1 1 1.473.53l3.525 3.528L8.516.545a.667.667 0 0 1 .942.943L5.94 5z"></path>
  </svg>
</template>
<script>
export default {

}
</script>

<style lang="scss">
.icon_close{
  width: 12px;
  height: 12px;
  fill: #D9D9D9;
  cursor: pointer;
  transition: linear 200ms;
  margin-left: auto;
  margin-top: 2px;

  &:hover{
    transition: linear 200ms;
    fill: #FC5A5A;
  }
}
</style>