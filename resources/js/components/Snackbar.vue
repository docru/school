<template>
  <div v-for="(bar,ind) in isArraySnackBars.filter((el)=>el.open!== false)" :key="bar">
    <v-snackbar
        min-width="100"
       
        max-width="300"
        v-model="bar.open"
        :timeout="isSnackBarDefault.timeout"
        location="bottom right"
        :color="bar.color"
        transition="slide-x-reverse-transition"
        :style="{marginBottom: (60*ind)+'px'}"
        style="bottom: 20px"
    >
      {{ bar.text }}
    </v-snackbar>
  </div>
</template>

<script>
import {mapGetters} from "vuex";

export default {
  name: "snackBar",
  computed: {
    ...mapGetters('app', ['isArraySnackBars','isSnackBarDefault']),
  }
}
</script>
