<template>
  <v-card>
    <v-card-title>
      Создаем юзера
    </v-card-title>
    <v-card-text >
      <div style="display: flex; gap: 5px;">
        <v-text-field density="compact" variant="outlined" v-model="phone"></v-text-field>
        <v-btn color="primary">Создать пользователя</v-btn>
      </div>

    </v-card-text>
  </v-card>


</template>

<script>
export default {
  name: "UserCreated",
  data() {
    return {
      phone: ''
    }
  }
}
</script>

<style scoped>

</style>