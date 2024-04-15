<template>
  <v-menu v-model="menu" :close-on-content-click="false" location="bottom">
    <template v-slot:activator="{ props }">
      <svg v-bind="props" width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M11.9783 3.38943C11.8009 2.04352 10.5351 1 9 1C7.46488 1 6.1991 2.04352 6.0217 3.38942C3.51939 4.41589 1.77882 6.69597 1.77882 9.34392V13.8837L0.108566 16.878C-0.227108 17.4798 0.256223 18.1905 1.00116 18.1905H6.17112C6.58289 19.2447 7.69397 20 9 20C10.306 20 11.4171 19.2447 11.8289 18.1905H16.9988C17.7438 18.1905 18.2271 17.4798 17.8914 16.878L16.2212 13.8837V9.34392C16.2212 6.69597 14.4806 4.4159 11.9783 3.38943ZM14.3287 14.5068L15.3741 16.381H2.62585L3.67127 14.5068C3.74179 14.3804 3.77853 14.2407 3.77853 14.0991V9.34392C3.77853 6.73444 6.11626 4.61905 9 4.61905C11.8837 4.61905 14.2215 6.73444 14.2215 9.34392V14.0991C14.2215 14.2407 14.2582 14.3804 14.3287 14.5068Z" fill="#6B7280"/>
        <rect x="10" width="9" height="9" rx="4.5" fill="#EE4D4D" v-if="!isMessages"/>
      </svg>
    </template>
    <v-card min-width="300">
      <v-list>
        <template v-for="i in [1,2,3,]">
          <v-list-item
              title="Алексей Русаков"
              subtitle="Срочно поправить выгрузку по статистике ..."
          >
            <template v-slot:append>
              <v-btn
                  variant="text"
                  :class="fav ? 'text-red' : ''"
                  icon="mdi-close"
                  @click="fav = !fav"
              ></v-btn>
            </template>
          </v-list-item>
          <v-divider></v-divider>
        </template>
      </v-list>
    </v-card>
  </v-menu>
</template>
<script>
export default {
  props: {
    isMessages:{
      type:Boolean,
      default:false
    }
  },
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