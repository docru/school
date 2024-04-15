<template>
  <v-menu v-model="menu" :close-on-content-click="false" location="bottom">
    <template v-slot:activator="{ props }">
      <svg width="20" v-bind="props"  height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M10.0006 1.30998H9.99998C5.20064 1.30998 1.30998 5.20064 1.30998 9.99998C1.30998 14.7993 5.20064 18.69 9.99998 18.69C14.7993 18.69 18.69 14.7993 18.69 9.99998V9.99936C18.6841 5.20276 14.7972 1.31581 10.0006 1.30998ZM0.5 9.99998C0.5 4.75331 4.75331 0.5 9.99998 0.5C15.2467 0.5 19.5 4.75331 19.5 9.99998C19.5 15.2467 15.2467 19.5 9.99998 19.5C4.75331 19.5 0.5 15.2467 0.5 9.99998Z" fill="#60A5FA" stroke="#60A5FA"/>
        <path d="M13.4037 7.16636L13.4039 7.16703C13.8515 9.02509 12.7259 10.8985 10.8753 11.3757L10.5001 11.4725V11.8599V12.4999H9.50004V10.8899C9.50004 10.6768 9.67112 10.5035 9.88352 10.5C11.1368 10.5386 12.2372 9.66988 12.4898 8.44056L12.4957 8.41167L12.4982 8.38229C12.6155 7.00257 11.5921 5.78903 10.2124 5.67176C8.99025 5.56787 7.89852 6.359 7.58357 7.49996H6.53305C6.72805 6.09003 7.76836 4.91678 9.185 4.5776L9.06857 4.09134L9.185 4.5776C11.0649 4.12749 12.9536 5.2865 13.4037 7.16636Z" fill="#60A5FA" stroke="#60A5FA"/>
        <path d="M9.5 15.5V14.5H10.5V15.5H9.5Z" fill="#60A5FA" stroke="#60A5FA"/>
      </svg>
    </template>
    <v-card class="mx-auto my-1" max-width="450" elevation="16">
      <v-card-item>
        <v-card-title> Есть вопрос? Поможем разобраться </v-card-title>
        <v-card-subtitle> Для решения проблемы обратитесь к личному менеджеру. </v-card-subtitle>
        <v-card-text> Просто напишите в чат в правом нижнем углу экрана или отправьте сообщение через нашего Telegram-бота  </v-card-text>
        <v-card
            color="#e5e7eb"
            elevation="0"
            class="mx-auto"
            max-width="500"
            title="Орехов Александр"
            subtitle=" E-mail | Telegram"
        >
          <template v-slot:prepend>
            <v-avatar size="45">
              <v-img
                  src="https://cdn.vuetifyjs.com/images/john.png"
                  alt="John"
              ></v-img>
            </v-avatar>
          </template>
        </v-card>
        <v-card-text>
          Если решить вопрос не удаётся или менеджер действует неэффективно — отправьте запрос напрямую руководителю отдела.
        </v-card-text>

        <v-expansion-panels>
          <v-expansion-panel>
            <v-expansion-panel-title>Связаться с руководителем</v-expansion-panel-title>
            <v-expansion-panel-text>
              <v-textarea clearable label="Опишите проблему" variant="outlined"></v-textarea>
              <v-btn
                  block
                  rounded="xs"
                  color="primary"

              >
                Отправить сообщение
              </v-btn>
            </v-expansion-panel-text>
          </v-expansion-panel>
        </v-expansion-panels>
      </v-card-item>


    </v-card>
  </v-menu>



</template>
<script>
export default {
  data(){
    return {
      fav: true,
      menu: false,
      message: false,
      hints: true,
    }
  }
}
</script>

<style scoped lang="scss">

</style>