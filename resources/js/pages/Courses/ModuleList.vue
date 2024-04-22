<script>
import draggable from 'vuedraggable'

let idGlobal = 8;
export default {
  components: {
    draggable,
  },
  name: "ModuleList",
  data() {
    return {
      calendar:[
        {
          name: 1,
          event: []
        },{
          name: 2,
          event: []
        },{
          name: 3,
          event: []
        },
      ],
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
    getStudyProgram:{
      handler(val) {
      if(val){
        this.$emit('setStudyProgram', val)
      }

      }, deep: true
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

  <div>
    <draggable
        :group="{ pull: false,put: false }"
        :list="getStudyProgram"
    >
      <template #item="{element}">
        <div class="courseList">
          <div class="course-header">
            <h2>Модуль {{ element.module.name }} / order: {{ element.module.order }}</h2>
            <v-spacer/>
            <v-menu>
              <template v-slot:activator="{ props }">
                <v-btn v-bind="props" variant="text" icon="mdi-dots-vertical" size="small"/>
              </template>
              <v-list>
                <v-list-item>
                  <v-list-item-title>
                    <v-btn
                        @click="$emit('openDialogCreateLesson',element.module.id)"
                        text="создать урок"
                        density="compact"
                        variant="text"
                    />
                  </v-list-item-title>
                  <v-divider v-if="!!element.module.id"/>
                  <v-list-item-title v-if="!!element.module.id">
                    <v-btn
                        @click=""
                        text="редактировать модуль"
                        density="compact"
                        variant="text"
                    />
                  </v-list-item-title>
                  <v-list-item-title v-if="!!element.module.id">
                    <v-btn
                        @click="$emit('deleteModule',element.module)"
                        text="удалить модуль"
                        density="compact"
                        variant="text"
                        color="red"
                    />
                  </v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </div>
          <div>
            <draggable
                :group="{
        name: element.module.name,
        pull: pullFunction  ,
        put: true }"
                :list="element.lessons"

            >
              <template #item="{element}">
                <div @click="$emit('chooseLesson',element)"
                     class="lesson">
                  <div class="tw-flex tw-justify-between">
                    <div>урок: {{ element.name }} / order: {{element.order}}</div>
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




<!--  <div v-for="module in getStudyProgram " :key="module.module.id" class="courseList">-->
<!--    <div class="course-header">-->
<!--      <h2>Модуль {{ module.module.name }}</h2>-->
<!--      <v-spacer/>-->
<!--      <v-menu>-->
<!--        <template v-slot:activator="{ props }">-->
<!--          <v-btn v-bind="props" variant="text" icon="mdi-dots-vertical" size="small"/>-->
<!--        </template>-->
<!--        <v-list>-->
<!--          <v-list-item>-->
<!--            <v-list-item-title>-->
<!--              <v-btn-->
<!--                  @click="$emit('openDialogCreateLesson',module.module.id)"-->
<!--                  text="создать урок"-->
<!--                  density="compact"-->
<!--                  variant="text"-->
<!--              />-->
<!--            </v-list-item-title>-->
<!--            <v-divider v-if="!!module.module.id"/>-->
<!--            <v-list-item-title v-if="!!module.module.id">-->
<!--              <v-btn-->
<!--                  @click=""-->
<!--                  text="редактировать модуль"-->
<!--                  density="compact"-->
<!--                  variant="text"-->
<!--              />-->
<!--            </v-list-item-title>-->
<!--            <v-list-item-title v-if="!!module.module.id">-->
<!--              <v-btn-->
<!--                  @click="$emit('deleteModule',module.module)"-->
<!--                  text="удалить модуль"-->
<!--                  density="compact"-->
<!--                  variant="text"-->
<!--                  color="red"-->
<!--              />-->
<!--            </v-list-item-title>-->
<!--          </v-list-item>-->
<!--        </v-list>-->
<!--      </v-menu>-->
<!--    </div>-->
<!--    <div>-->
<!--      <draggable-->
<!--          :group="{-->
<!--        name: module.module.name,-->
<!--        pull: pullFunction  ,-->
<!--        put: true }"-->
<!--          :list="module.lessons"-->

<!--      >-->
<!--        <template #item="{element}">-->
<!--          <div @click="$emit('chooseLesson',element)"-->
<!--               class="lesson">-->
<!--            <div class="tw-flex tw-justify-between">-->
<!--              <div>урок: {{ element.name }} / order: {{element.order}}</div>-->
<!--              <div>-->
<!--                <div @click="$emit('deleteLesson',element)">-->
<!--                  <v-icon size="small" color="grey">mdi-trash-can-outline</v-icon>-->
<!--                </div>-->
<!--              </div>-->
<!--            </div>-->
<!--          </div>-->
<!--        </template>-->
<!--      </draggable>-->
<!--    </div>-->
<!--  </div>-->
<!--  <pre>-->
<!--                {{ getStudyProgram }}-->
<!--                </pre>-->


</template>

<style scoped lang="scss">
.courseList{
  border:1px solid rgba(128, 128, 128, 0.27);
  border-radius: 4px;
  padding: 5px;
  margin-bottom: 5px;
  &:hover{
    background-color: rgba(187, 189, 193, 0.05);
  }

}
.lesson{
  @apply tw-w-[98%]   tw-m-2 tw-p-2;
  border: 1px solid rgba(128, 128, 128, 0.42);
  border-radius: 4px;
  &:hover{
    background-color: rgba(187, 189, 193, 0.2);
  }

}
.course-header{
  @apply tw-flex tw-justify-between tw-items-center
}
.active-lesson {
  background-color: #d0d0d0;
}
</style>