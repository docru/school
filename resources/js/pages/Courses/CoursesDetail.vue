<template>
<v-card>
  <v-card-title>
    <div class="tw-flex">
      <div>
        <div>
          Название курса {{$route.params.idCourse }}
        </div>
        <div>Описание</div>
      </div>
      <v-spacer></v-spacer>
      <div class="tw-text-sm tw-text-zinc-500">
        <div>Методист: Иван Иванов</div>
        <div>Количество часов: 2</div>
        <div>Кличество уроков: 1</div>
      </div>
    </div>

  </v-card-title>
  <v-divider></v-divider>
  <v-card-text>

    <v-card variant="text">
     <v-card-title>
     <div class="tw-flex">
       <div>
         Программа курса
       </div>
       <v-spacer></v-spacer>
       <div class="tw-flex tw-gap-3">
         <v-btn>Создать модуль</v-btn>

       </div>
     </div>
     </v-card-title>
     <v-card-text>
       <div class="tw-flex tw-gap-3">
         <div class="tw-w-1/3">
           <v-card hover elevation="2"    style="padding: 5px 10px; margin: 15px 0 "  v-for="c in [1,2,3]">
             <v-card-title >
               <div class="tw-flex">
               Модуль {{ c }}
               <v-spacer/>
                 <v-btn  variant="text" size="small" >создать урок</v-btn>

               </div>
             </v-card-title>
             <div v-for="lesson in [1,2,3,4,5,6]"
                  class="less tw-flex">
               <div>
                 урок {{ lesson }} Название
               </div>
               <v-spacer></v-spacer>
               <div class="tw-flex tw-gap-5">
                 <div>3 часа</div>
                 <div @click="dialog=true"><v-icon size="small" color="grey" >mdi-pencil</v-icon></div>
               </div>
             </div>
           </v-card>
         </div>
         <div class="tw-w-2/3">
           <v-card hover elevation="2"    style="padding: 5px 10px; margin: 15px 0 "  >
             <v-card-title>Название урока</v-card-title>
             <v-tabs
                 v-model="tab"
                 bg-color="primary"
             >
               <v-tab value="one">План урока</v-tab>
               <v-tab value="two">Задания на уроке</v-tab>
               <v-tab value="three">Домашнее задание</v-tab>
             </v-tabs>

             <v-card-text>
               <v-window v-model="tab">
                 <v-window-item value="one">
                   План урока <br>
                   I.            Организация начала урока.
                   <br>
                   ─ Начинаем урок. Ребята, как вы думаете, нужно ли человеку быть грамотным? Зачем? (Это нужно для того, чтобы спокойно и уверенно чувствовать себя среди грамотных людей)<br>

                   ─ Что значит быть грамотным? (Писать без ошибок, правильно, точно, ясно выражать свои мысли)<br>

                   ─ А вы хотите этому научиться? Наш урок вам в этом поможет.<br><br>

                   II.            Введение в тему урока. Постановка целей.<br>

                   На доске: Снова пересказываем и рассказываем.<br>

                   Учебники закрыты.<br>

                   ─ Прочитайте тему урока. Запишите с красной строки в тетрадь.<br>

                   ─ Назовите глаголы.<br>

                   ─ Разберите по составу.<br>

                   ─ Чем похожи, чем различаются составы слов "пересказываем" и "рассказываем"? (Одинаковые корни, суффиксы, окончания, различные приставки)<br>

                   ─ Одинаково ли значение глаголов?<br>

                   Пересказывать – рассказать, изложить своими словами.<br>


                   <div  style="margin: 15px; background-color: darkseagreen; border-radius: 4px; padding: 15px">
                     <h4>Выполнить задание №1</h4>
                   </div>
 ....
                 </v-window-item>

                 <v-window-item value="two">
                   <v-card>
                     <v-card-title>Задание на уроке</v-card-title>
                     <div  style="margin: 15px; background-color: darkseagreen; border-radius: 4px; padding: 15px">
                       <h3>Задание №1</h3>
                       <p class="tw-p-3 tw-bg-white  ">
                         Разбейтесь по парам и расскажите друг другу ...
                       </p>
                     </div>
                   </v-card>
                 </v-window-item>

                 <v-window-item value="three">
                   Домашнее задание
                 </v-window-item>
               </v-window>
             </v-card-text>
           </v-card>
         </div>
       </div>



     </v-card-text>
   </v-card>
  </v-card-text>
</v-card>
<v-dialog v-model="dialog" fullscreen >
  <v-card>
    <v-card-title>
    <div class="tw-flex">
      <div>
        Редактирование урока
      </div>
      <v-spacer></v-spacer>
      <v-icon @click="dialog=false">mdi-close</v-icon>
    </div>
    </v-card-title>
  </v-card>
</v-dialog>

</template>
<script >
export default {
  data(){
    return{
      tab:null,
      dialog:false
    }
  }
}
</script>



<style scoped lang="scss">
.less{
  cursor: pointer;
  padding: 5px 10px ;
  border: #9197A3 1px solid;
  border-radius: 4px;
  @apply tw-my-2;
  &:hover{
    background: rgba(239, 238, 238, 0.59);
  }
}
</style>