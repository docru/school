<template>
  <v-app>
    <snack-bar/>
    <v-navigation-drawer
        v-if="validProfile"
        app
        left
        absolute
        temporary
        v-model="drawer"
        class="pa-3"
    >

      <v-list-subheader>Учебный процесс</v-list-subheader>
      <v-list-item
          v-for="item in menu_items"
          :key="item"
          :to="{name:item.routName}"
          link
          :title="item.name"
      />

      <v-divider></v-divider>
      <v-list-subheader>Администрирование</v-list-subheader>
      <v-list-item
          v-for="item in menu_items_admin"
          :key="item"
          :to="{name:item.routName}"
          link
          :title="item.name"
      />

      <v-divider></v-divider>
      <v-list-subheader>Методология</v-list-subheader>
      <v-list-item
          v-for="item in menu_items_metod"
          :key="item"
          :to="{name:item.routName}"
          link
          :title="item.name"
      />

      <v-divider></v-divider>
      <v-list-subheader>СуперАдминистрирование</v-list-subheader>
      <v-list-item
          v-for="item in  menu_items_superadmin"
          :key="item"
          :to="{name:item.routName}"
          link
          :title="item.name"
      />

    </v-navigation-drawer>
    <v-app-bar
        app
        color="primary"
        v-if="validProfile"
    >
      <v-app-bar-nav-icon @click="drawer = !drawer">
      </v-app-bar-nav-icon>
      <v-app-bar-title>
        <h3 class=" white--text">
          {{$vuetify.display.name === 'sm' ? 'Школа':'ЦХМ школа'}}
        </h3>
      </v-app-bar-title>
      <v-spacer></v-spacer>
      <router-link class="tw-mx-3" :to="{name:'Profile'}">{{getProfile?.name}} ({{getProfile?.nicname}})</router-link>


      <div>

      </div>
    </v-app-bar>
    <v-main>
      <v-card variant="text" class="ma-2 pa-2" style="min-height: 100%">
        <router-view></router-view>
      </v-card>
    </v-main>
  </v-app>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import SnackBar from "./components/Snackbar.vue";

export default {
  name: 'App',
  components: {SnackBar},
  data: () => ({
    drawer: false,
    menu_items: [
      {
        name: 'Главная ',
        routName: 'Home'
      },
      {
        name: 'Мои курсы ',
        routName: 'Home'
      },
      {
        name: 'Мои задания ',
        routName: 'Home'
      },

    ],
    menu_items_admin: [
      {
        name: 'Пользователи',
        routName: 'Users'
      },
      {
        name: 'Группы',
        routName: 'GroupsList'
      },
      {
        name: 'Учебный план',
        routName: 'Plan'
      },

    ],
    menu_items_metod: [
      {
        name: 'Уроки ',
        routName: 'Users'
      },
      {
        name: 'Курсы ',
        routName: 'CoursesList'
      },
    ],
    menu_items_superadmin: [
      {
        name: 'Пользователи ',
        routName: 'Users'
      },

    ],
  }),
  methods:{
    routePoint(){
      if(!this.validProfile){
        this.$router.push({
          name:'FormNicName'
        })
      }
    },
    ...mapActions('profile',['ACT_GET_Profile'])
  },
  computed:{
    validProfile(){
      return true
      return this.getProfile?.nickname && this.getProfile?.name
    },
    ...mapGetters('profile',['getLoad','getProfile'])
  },
  mounted() {
    this.routePoint()
  },
  created() {
    this.ACT_GET_Profile()
  }

}
</script>

<style scoped>

</style>
