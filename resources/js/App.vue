<template>
  <v-app>
    <v-navigation-drawer
        app
        left
        absolute
        temporary
        v-model="drawer"
    >
      <v-list-item-group>

        <v-list-item v-for="item in menu_items" :key="item.id">
          <v-list-item-title>
            <router-link :to="item.url">{{ item.name }}</router-link>
          </v-list-item-title>
        </v-list-item>

      </v-list-item-group>

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
      <v-card class="ma-2 pa-2">
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
        id: 0,
        name: 'Главная ',
        url: '/'
      },
      {
        id: 1,
        name: 'Расписание',
        url: '/schedule'
      },
      {
        id: 2,
        name: 'Курсы',
        url: '#'
      },
      {
        id: 3,
        name: 'Домашка',
        url: '#'
      }
    ]
  })
}
</script>

<style scoped>

</style>