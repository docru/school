<template>
  <v-card>
    <v-card-title>
      Пользователи
    </v-card-title>
    <v-card-text>
      <div style="display: flex;  align-items:start;gap: 15px;  margin-bottom: 25px">
        <div class="w-30">
          <v-text-field type="number" v-model="phone"></v-text-field>
          <div style="display: flex; gap: 10px">
            <v-checkbox hide-details density="compact" v-model="role.value" v-for="role in getRoles" top :label="role.name"/>
          </div>

        </div>

        <v-btn v-if="getRoles" color="primary" :disabled="!getRoles.filter((el)=>el.value).length || !phone" @click="create">Создать пользователя</v-btn>
      </div>
      <div v-if="getUsers">
        <v-data-table
            :headers="usersHeaders"
            :items="getUsers"
        >
          <template v-slot:item.link="{item}">
<Code :code="`http://localhost/login/${item.entry_code}`" />

          </template>

        </v-data-table>
      </div>
    </v-card-text>
  </v-card>


</template>

<script>
import {mapActions, mapGetters} from "vuex";
import Code from "../components/Code.vue";
import {th} from "vuetify/locale";

export default {
  name: "Users",
  components: {Code},
  data() {
    return {
      phone: '',
      usersHeaders: [
        {title: 'id', key: 'id'},
        {title: 'Имя', key: 'name'},
        {title: 'Nic', key: 'nicname'},
        {title: 'Телефон', key: 'phone'},
        // {title: 'Обновлен', key: 'updated_at'},
        // {title: 'Обновлен', key: 'updated_at'},
        // {title: 'link', key: 'link'},
      ]
    }
  },
  methods: {
    create(){
      this.ACT_GET_UserCreate({
        phone:this.phone,
        roles:this.getRoles.filter((el)=>el.value).map((elem)=>{return elem.name})
      })
    },
    ...mapActions('users', ['ACT_GET_User','ACT_GET_UserRoles','ACT_GET_UserCreate'])
  },
  computed: {
    ...mapGetters('users', ['getUsers','getRoles'])
  },
  created() {
    this.ACT_GET_User()
    this.ACT_GET_UserRoles()
  }
}
</script>

<style scoped>

</style>