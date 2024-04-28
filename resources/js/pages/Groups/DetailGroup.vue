<template>

  {{ getGroup }}
  <br><br>
  {{ getCourse }}
  <br><br>
  детальная страница группы {{ $route.params.id }}


  <v-data-table
      density="compact"
      :items="items"
      :headers="headers">
    <template v-slot:item.day1="{item}">
      <v-checkbox v-model="item.day1" color="green"/>
    </template>
    <template v-slot:header.day1=" {column} ">

      {{ column.title }} <br>
      <v-tooltip  >
        <template v-slot:activator="{ props }">
          <div v-bind="props">Уроки</div>
        </template>
        <span v-html="'10 заповедей<br> 2 заповеди'"></span>
      </v-tooltip>
      12.04.2024
    </template>


  </v-data-table>

</template>

<script>
import {mapActions, mapGetters} from "vuex";
import {th} from "vuetify/locale";

export default {
  name: "DetailGroup",
  data() {
    return {
      items: [
        {name: 'Иван Иванов', day1: true},
        {name: 'Иван Иванов', day1: true},
        {name: 'Иван Иванов', day1: true},
        {name: 'Иван Иванов', day1: true},
        {name: 'Иван Иванов', day1: true},
        {name: 'Иван Иванов', day1: true},
        {name: 'Иван Иванов', day1: true},
        {name: 'Иван Иванов', day1: true},
        {name: 'Иван Иванов', day1: true},
        {name: 'Иван Иванов', day1: true},
        {name: 'Иван Иванов', day1: true},
        {name: 'Иван Иванов', day1: true},
        {name: 'Иван Иванов', day1: true},
        {name: 'Иван Иванов', day1: true},
        {name: 'Иван Иванов', day1: true},
      ],
      headers: [
        {title: 'Имя', key: 'name'},
        {title: '1', key: 'day1'},
        {title: '2', key: 'day1'},
        {title: '3', key: 'day1'},
        {title: '4', key: 'day1'},
        {title: '5', key: 'day1'},
        {title: '6', key: 'day1'},
        {title: '7', key: 'day1'},
        {title: '8', key: 'day1'},
        {title: '9', key: 'day1'},
        {title: '10', key: 'day1'},
        {title: '11', key: 'day1'},
        {title: '12', key: 'day1'},
        {title: '13', key: 'day1'},
        {title: '14', key: 'day1'},
        {title: '15', key: 'day1'},
        {title: '16', key: 'day1'},
        {title: '17', key: 'day1'},
        {title: '18', key: 'day1'},
        {title: '18', key: 'day1'},
        {title: '18', key: 'day1'},
        {title: '18', key: 'day1'},
        {title: '18', key: 'day1'},
        {title: '18', key: 'day1'},
        {title: '18', key: 'day1'},
        {title: '18', key: 'day1'},
        {title: '18', key: 'day1'},
        {title: '18', key: 'day1'},
        {title: '18', key: 'day1'},



      ]
    }
  },
  methods: {
    ...mapActions('groups', ['actGetGroup']),
    ...mapActions('courses', ['actReqwestCourse'])
  },
  computed: {
    ...mapGetters('groups', ['getGroup']),
    ...mapGetters('courses', ['getCourse'])
  },
  async created() {
    await this.actGetGroup(this.$route.params.id)
    await this.actReqwestCourse({id: this.getGroup.course_id})

  }
}
</script>


<style scoped lang="scss">

</style>