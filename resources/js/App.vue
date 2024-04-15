<template>
  <v-app>
    <v-navigation-drawer
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


    </v-navigation-drawer>
    <v-app-bar
        app
        color="primary"
    >
      <v-app-bar-nav-icon @click="drawer = !drawer">
      </v-app-bar-nav-icon>
      <v-app-bar-title>
        <h3 class="text-h4 white--text">
          ЦХМ школа
        </h3>
      </v-app-bar-title>
    </v-app-bar>
    <v-main>
      <v-card variant="text" class="ma-2 pa-2" style="min-height: 100%">
        <router-view></router-view>
      </v-card>

    </v-main>

  </v-app>
</template>

<script>
export default {
  name: 'App',
  data: () => ({
    drawer: false,
    menu_items: [
      {
        name: 'Главная ',
        routName: 'Home'
      },
      {
        name: 'Расписание ',
        routName: 'Schedule'
      },

    ],
    menu_items_admin: [
      {
        name: 'Пользователи ',
        routName: 'Users'
      }
    ],
  })
}
</script>

<style scoped>

</style>
