<template>
  <v-card v-if="getUsers">
    <v-card-title>
      Посещаемость
    </v-card-title>
    <v-card-text>
      <v-divider class="tw-my-3" color="blue"/>
      <div v-if="getUsers">
        <div class="tw-flex tw-flex-col md:tw-flex-row tw-justify-between">
          <div class="tw-w-full md:tw-w-1/3">
            <v-text-field
                hide-spin-buttons
                clearable
                type="number"
                label="Поиск по номеру телефона"
                v-model="search"
            ></v-text-field>
          </div>
        </div>
        <v-data-table
            :headers="usersHeaders"
            :items="searchItems"
            :loading="getLoad"
            density="compact"
        >
          <template v-slot:item.actions="{ item }">
            <!--             параметр отвечающий за посещение, должен формироваться на бэке-->
            <v-switch v-model="item.att" color="success" hide-details hide-spin-buttons> </v-switch>
          </template>

          <template v-slot:loading>
            <v-skeleton-loader type="table-row@10"></v-skeleton-loader>
          </template>
          <template v-slot:item.phone="{item}">
            {{ item.phone.replace(/([\w]|.){7}/, '*******') }}
          </template>
        </v-data-table>
      </div>
    </v-card-text>
  </v-card>
  <Loading v-else/>


</template>

<script>
import {mapActions, mapGetters} from "vuex";
import Code from "../../components/Code.vue";
import Loading from "../../components/Loading.vue";
import ICode from "../../components/icon/ICode.vue";
import IVast from "../../components/icon/IVast.vue";
import IModeration from "../../components/icon/IModeration.vue";
import IMore from "../../components/icon/IMore.vue";

export default {
  name: "Disciples",
  components: {IMore, IModeration, IVast, ICode, Loading, Code},
  data() {
    return {
      search: '',
      phone: '',
      switch: true,
      usersHeaders: [
        {title: 'Курс', key: 'course'},
        {title: 'Посещаемость', sortable: false, key: 'actions'},
        {title: 'Имя', key: 'name'},
        {title: 'Псевдоним', key: 'nicname'},
        {title: 'Телефон', key: 'phone'},
      ]
    }
  },
  methods: {
    create() {
      this.actUserCreate({phone: this.phone, role: 'disciple'});
    },
    ...mapActions('users', ['actReqwestUsers', 'actUserCreateLink']),
  },
  computed: {
    searchItems() {
      let result = this.getUsers.map(el => {
        return {...el, att: false}
      });

      // if (!this.search) return result

      if (this.search)
        result = result.filter((el) => {
          return el.phone.search(this.search) > -1
        })

      return result;
    },
    ...mapGetters('users', ['getUsers', 'getLoad'])
  },
  created() {
    this.actReqwestUsers({role: 'disciple'});
  }
}
</script>

<style scoped>

</style>
