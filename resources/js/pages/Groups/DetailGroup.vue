<template>

<!--  {{ getGroup }}-->
<!--  <br><br>-->
<!--  {{ getCourse }}-->
<!--  <br><br>-->
<!--  детальная страница группы {{ $route.params.id }} <br>-->
<!--  &lt;!&ndash;  <pre>&ndash;&gt;-->
<!--  &lt;!&ndash;  {{ items }}&ndash;&gt;-->
<!--  &lt;!&ndash;  </pre>&ndash;&gt;-->


  <v-data-table
      :items-per-page="-1"
      class="table-sh"
      height="800"
      fixed-header
      density="compact"
      :items="items"
      :headers="headers"
      :hide-default-footer="true"
      disable-pagination
      :item-class="itemRowBackground"
  >

    <template v-slot:[`item.${it}`]="{item}"
              v-for="(it,index) in headers.map((val)=>{if(val.key !== 'name' &&  val.key !=='') return val.key })"
    >
      <div :class="{
                activeSlot:it == now
                }">
        <v-checkbox
            :disabled="it !== now"
            v-model="item[it]" color="green" hide-details density="compact" style="text-align: center"/>
      </div>

    </template>

    <template v-slot:[`header.${it}`]=" {column}"
              v-for="(it,index) in headers.map((val)=>{if(val.key !== 'name'   &&  val.key !=='') return val.key })">
      <div :class="{
                activeSlot:it == now
                }">
        {{ column.title }} <br>
        <v-tooltip location="top">
          <template v-slot:activator="{ props }">
            <v-icon v-bind="props" color="grey" size="small">mdi-book-open-outline</v-icon>
          </template>
          <span v-html="'1. 10 заповедей<br> 2. Значение креста'"></span>
        </v-tooltip>
        <div :style="{
        color: it !== now ? 'grey' :'black',

      }">12.04
        </div>
      </div>
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
      now: 'day3',
      items: [
        {name: 'Иван Петров', day1: true, day2: true, day3: true, day4: true, day5: true,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: true,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: true,},
        {name: 'Иван Иванов', day1: false, day2: true, day3: false, day4: true, day5: true,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: true,},
        {name: 'Иван Иванов', day1: true, day2: false, day3: true, day4: true, day5: true,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: false, day5: true,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: false, day4: false, day5: true,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: true,},
        {name: 'Иван Петров', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Иванов', day1: true, day2: true, day3: true, day4: true, day5: false,},
        {name: 'Иван Петров', day1: true},
      ],
      headers: [
        {title: 'Имя', align: 'center', key: 'name', width: "300px", fixed: true, sortable: false, divider: true},
        {title: '1', align: 'center', key: 'day1', width: "20px", sortable: false},
        {title: '2', align: 'center', key: 'day2', width: "20px", sortable: false},
        {title: '3', align: 'center', key: 'day3', width: "20px", sortable: false},
        {title: '4', align: 'center', key: 'day4', width: "20px", sortable: false},
        {title: '5', align: 'center', key: 'day5', width: "20px", sortable: false},
        {title: '6', align: 'center', key: 'day6', width: "20px", sortable: false},
        {title: '7', align: 'center', key: 'day7', width: "20px", sortable: false},
        {title: '8', align: 'center', key: 'day8', width: "20px", sortable: false},
        {title: '9', align: 'center', key: 'day9', width: "20px", sortable: false},
        {title: '10', align: 'center', key: 'day10', width: "20px", sortable: false},
        {title: '11', align: 'center', key: 'day11', width: "20px", sortable: false},
        {title: '12', align: 'center', key: 'day12', width: "20px", sortable: false},
        {title: '13', align: 'center', key: 'day13', width: "20px", sortable: false},
        {title: '14', align: 'center', key: 'day14', width: "20px", sortable: false},
        {title: '15', align: 'center', key: 'day15', width: "20px", sortable: false},
        {title: '16', align: 'center', key: 'day16', width: "20px", sortable: false},
        {title: '17', align: 'center', key: 'day17', width: "20px", sortable: false},
        {title: '18', align: 'center', key: 'day18', width: "20px", sortable: false},
        {title: '19', align: 'center', key: 'day19', width: "20px", sortable: false},
        {title: '20', align: 'center', key: 'day20', width: "20px", sortable: false},
        {title: '21', align: 'center', key: 'day21', width: "20px", sortable: false},
        {title: '22', align: 'center', key: 'day22', width: "20px", sortable: false},
        {title: '', key: ''},


      ]
    }
  },
  methods: {
    itemRowBackground: function (item) {
      return 'style-1'
      console.log(item)
      return item[this.now] ? 'style-1' : 'style-2'
    },
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


<style lang="scss">
.style-1 {
  background-color: rgb(215,215,44)!important;
}
.style-2 {
  background-color: rgb(114,114,67)!important;
}
.activeSlot {
  background-color: rgba(66, 209, 197, 0.18) !important;
}

.table-sh {
  .v-selection-control {
    align-items: center;
    contain: layout;
    display: flex;
    flex: 1 0;
    grid-area: control;
    position: relative;
    -webkit-user-select: none;
    -moz-user-select: none;
    user-select: none;
    justify-content: center;
  }

  table thead th {
    //background-color: #FC5A5A!important;
    //border: 1px solid indianred!important;/
    padding: 3px !important;
    margin: 3px !important;

    &:hover {
      background-color: rgba(194, 229, 234, 0.13) !important;
    }
  }

  table tbody td {
    //background-color: #FC5A5A!important;
    //border: 1px solid indianred!important;/
    padding: 0 !important;
    margin: 2px !important;

    &:hover {
      background-color: rgba(139, 208, 218, 0.13) !important;
    }
  }

}


</style>