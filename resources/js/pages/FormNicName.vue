<template>
  <div class="tw-m-auto tw-w-full tw-h-[90vh]
              tw-flex tw-justify-center
              tw-items-start
">

    <div class="tw-w-[300px] tw-flex tw-flex-col tw-gap-2">
      <v-text-field v-model="getProfile.name" type="text" label="Имя"></v-text-field>
      <v-text-field v-model="getProfile.nicname" type="text" label="NIC"></v-text-field>
      <v-textarea v-model="getProfile.nickname_description" label="Описание"></v-textarea>
      <v-btn>Сохранить</v-btn>
    </div>

  </div>
</template>
<script>
import {mapGetters} from "vuex";

export default {
  data:()=>{
    return {

    }
  },
  computed:{
    ...mapGetters('profile',['getProfile'])
  }
}

</script>

<style scoped lang="scss">

</style>