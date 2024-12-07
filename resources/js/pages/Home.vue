<script>
import {mapActions, mapGetters} from "vuex";

export default {
    name: "Home",
    computed: {
        ...mapGetters('users', ['getHome'])
    },
    methods: {
        ...mapActions('users', ['actReqwestHome']),
    },
    created() {
        this.actReqwestHome();
    }
}
</script>

<template>
<!--  <v-divider v-if="getHome.disciple.length"></v-divider>-->
  <v-card
      v-for="dis in getHome.disciple" :key="dis.id"
      style="cursor: pointer"
      variant="outlined"
      class="tw-w-full md:tw-w-1/3 xl:tw-w-1/5"
      :color="dis.status === 'active' ? '#1867c0' : 'grey'"
      :title="dis.course.name"
      :subtitle="dis.group.name"
      :to="{name:'DiscipleDetailGroup',params:{id:dis.course.id}}"
  >
    <v-icon style="position: absolute; top:5px; right: 5px;">mdi-account-school</v-icon>
  </v-card>

  <v-divider ></v-divider>
<!--  {{getHome.teacher}}-->
  <v-card
      v-for="dis in getHome.teacher" :key="dis.id"
      style="cursor: pointer"
      variant="outlined"
      class="tw-w-full md:tw-w-1/3 xl:tw-w-1/5"
      :color="dis.status === 'active' ? '#1867c0' : 'grey'"
      :title="dis.course.name"
      :subtitle="dis.group.name"
      :to="{name:'TeacherDetailGroup',params:{id:dis.group.id}}"
  >
  <v-icon style="position: absolute; top:5px; right: 5px;">mdi-account-tie</v-icon>
  </v-card>


</template>

<style scoped lang="scss">
.main {
    width: 100%;
    height: 100vh;
    justify-content: center;
    align-items: center;
    display: flex;
    flex-direction: column;

    h6 {
        color: gray;
        font-size: 20px;
    }
}
</style>
