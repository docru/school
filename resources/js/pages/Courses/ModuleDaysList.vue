<script>
import draggable from 'vuedraggable'

let idGlobal = 8;
export default {
  components: {
    draggable,
  },
  name: "ModuleDaysList",
  data() {
    return {
      calendar: [],
      drag: false,
      controlOnStart: true,
      myArray: [
        {id: 1, name: 'nnnn1'},
        {id: 2, name: 'nnnn2'},
        {id: 3, name: 'nnnn3'},
        {id: 4, name: 'nnnn4'},
      ]
    }
  },
  watch: {
    getStudyProgram: {
      handler(val) {
        if (val) {
          this.$emit('setStudyProgram', val)
        }

      }, deep: true
    }
  },
  computed: {
    less() {
      if (!this.calendar.length) return []
      return this.calendar?.map(el => {
        return el.lessons.map(ele => ele.id).splice(',')
      }).flat()
    }
  },
  methods: {
    clone({name}) {
      return {name, id: idGlobal++};
    },
    pullFunction(val) {
      console.log(val)
      // return this.controlOnStart ? "clone" : true;
    },
  },
  props: {
    getStudyProgram: {
      default: []
    },
    curLesson: {
      default: null
    }
  }
}
</script>

<template>
  <div class="tw-flex tw-w-full tw-gap-3">
    <div class="tw-w-1/3">
      <draggable
          :group="{ pull: false,put: false }"
          :list="getStudyProgram"

      >
        <template #item="{element}">
          <div class="courseList">
            <div class="course-header">
              <h2>Модуль {{ element.module.name }} / order: {{ element.module.order }}</h2>
              <v-spacer/>

            </div>
            <div>
              <draggable
                  handle=".item"
                  :group="{
                pull: 'clone',
        name: element.module.name,

        put: true }"
                  :list="element.lessons"

              >
                <template #item="{element}">
                  <div
                      :class="{'item':!less.includes(element.id)}"
                       @click="$emit('chooseLesson',element)"
                       class="lesson">
                    <div class="tw-flex tw-justify-between">
                      <div>урок: {{ element.name }} / order: {{ element.order }}</div>
                      <div>
                        <div @click="$emit('deleteLesson',element)">
                          <v-icon size="small" color="grey">mdi-trash-can-outline</v-icon>
                        </div>
                      </div>
                    </div>
                  </div>
                </template>
              </draggable>
            </div>
          </div>
        </template>
      </draggable>
    </div>
    <div class="tw-w-full">
      <div class="tw-m-2" style="text-align: right">
        <v-btn @click="calendar.push({name:'День '+(calendar.length+1),lessons:[]})">Создать учебный день</v-btn>
      </div>

      <div class="tw-p-2 tw-m-1 tw-w-[95%] tw-min-h-[100px] "
           style="background-color:rgba(216,228,236,0.3);border:1px rgba(128,128,128,0.19) solid; border-radius: 4px"
           v-for="day in calendar">
        <div class="tw-flex tw-justify-between tw-items-center">
          <h2>{{ day.name }}</h2>
          <v-icon>mdi-close</v-icon>

        </div>
        <draggable

            :group="{ pull: true,put: true }"
            :list="day.lessons"
        >
          <template #item="{element}">
            <div class="tw-flex tw-justify-between tw-m-1 tw-p-2" style="border:1px grey solid; border-radius: 4px">
              урок: {{ element.name }}
              <!--          <v-icon>mdi-close</v-icon>-->
            </div>
          </template>
        </draggable>

      </div>
      <pre>
{{ calendar }}
    </pre>
    </div>
  </div>
</template>

<style scoped lang="scss">
.courseList {
  border: 1px solid rgba(128, 128, 128, 0.27);
  border-radius: 4px;
  padding: 5px;
  margin-bottom: 5px;

  &:hover {
    background-color: rgba(187, 189, 193, 0.05);
  }

}

.lesson {
  @apply tw-w-[98%]   tw-m-2 tw-p-2;
  border: 1px solid rgba(128, 128, 128, 0.42);
  border-radius: 4px;

  &:hover {
    background-color: rgba(187, 189, 193, 0.2);
  }

}

.item {
  background-color: rgba(92, 165, 205, 0.25);
}

.not-draggable {
  cursor: no-drop;
}

.course-header {
  @apply tw-flex tw-justify-between tw-items-center
}

.active-lesson {
  background-color: #d0d0d0;
}
</style>