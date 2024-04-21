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
  methods:{
    clone({ name }) {
      return { name, id: idGlobal++ };
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


  <div v-for="module in getStudyProgram " :key="module.module.id" class=" br-red tw-my-1">
    <div class="tw-flex tw-my-2">
      Модуль {{ module.module.name }}
      <v-spacer/>
      <v-menu>
        <template v-slot:activator="{ props }">
          <v-btn v-bind="props" variant="text" icon="mdi-plus" size="small"/>
        </template>
        <v-list>
          <v-list-item>
            <v-list-item-title>
              <v-btn
                  @click="openDialogCreateLesson(moduleItem.module.id)"
                  text="создать урок"
                  density="compact"
                  variant="text"
              />
            </v-list-item-title>
            <v-list-item-title v-if="!!moduleItem.module.id">
              <v-btn
                  @click="deleteModule(moduleItem.module)"
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
        name: module.module.name,
        pull: pullFunction  ,
        put: true }"
          :list="module.lessons"

      >
        <template #item="{element}">
          <div class="tw-w-2/3 tw-h-[45px]  tw-m-2 tw-p-2" style="border: 1px solid grey">
            урок: {{ element.name }}
          </div>
        </template>
      </draggable>
    </div>
  </div>
  <pre>
                {{ getStudyProgram }}
                </pre>

  <!--  <v-card hover elevation="0"-->
  <!--          style="padding: 5px 10px; margin: 15px 0 "-->
  <!--          v-for="moduleItem in getStudyProgram"-->
  <!--          :key="moduleItem.id">-->
  <!--    <v-card-title>-->
  <!--      <div class="tw-flex">-->
  <!--        Модуль {{ moduleItem.module.name }}-->
  <!--        <v-spacer/>-->
  <!--        <v-menu>-->
  <!--          <template v-slot:activator="{ props }">-->
  <!--            <v-btn v-bind="props" variant="text" icon="mdi-plus" size="small"/>-->
  <!--          </template>-->
  <!--          <v-list>-->
  <!--            <v-list-item>-->
  <!--              <v-list-item-title>-->
  <!--                <v-btn-->
  <!--                    @click="openDialogCreateLesson(moduleItem.module.id)"-->
  <!--                    text="создать урок"-->
  <!--                    density="compact"-->
  <!--                    variant="text"-->
  <!--                />-->
  <!--              </v-list-item-title>-->
  <!--              <v-list-item-title v-if="!!moduleItem.module.id">-->
  <!--                <v-btn-->
  <!--                    @click="deleteModule(moduleItem.module)"-->
  <!--                    text="удалить модуль"-->
  <!--                    density="compact"-->
  <!--                    variant="text"-->
  <!--                    color="red"-->
  <!--                />-->
  <!--              </v-list-item-title>-->
  <!--            </v-list-item>-->
  <!--          </v-list>-->
  <!--        </v-menu>-->
  <!--      </div>-->
  <!--    </v-card-title>-->
  <!--    <div-->
  <!--        v-for="lesson in moduleItem.lessons"-->
  <!--        :key="lesson.id"-->
  <!--        class="less tw-flex"-->
  <!--        :class="(curLesson.id == lesson.id) ? 'active-lesson' : ''"-->
  <!--        @click="chooseLesson(lesson)"-->
  <!--    >-->
  <!--      <div>урок <strong>"{{ lesson.name }}"</strong></div>-->
  <!--      <v-spacer></v-spacer>-->
  <!--      <div class="tw-flex tw-gap-5">-->
  <!--        <div>{{ lesson.hours }}ч.</div>-->
  <!--        <div @click="deleteLesson(lesson)">-->
  <!--          <v-icon size="small" color="grey">mdi-trash-can-outline</v-icon>-->
  <!--        </div>-->
  <!--      </div>-->
  <!--    </div>-->
  <!--    <v-divider/>-->
  <!--  </v-card>-->
  <!--    <pre>-->
  <!--      {{ getStudyProgram }}-->
  <!--    </pre>-->
</template>

<style scoped lang="scss">
.active-lesson {
  background-color: #d0d0d0;
}
</style>