<script>
import {mapActions, mapGetters} from "vuex";

export default {
    name: "Home",
    computed: {
        ...mapGetters('users', ['getHome'])
    },
    methods: {
        ...mapActions('users', ['actReqwestHome']),
    },
    created() {
        this.actReqwestHome();
    }
}
</script>

<template>
    <div class="main">
        <h1>Кабинет</h1>
        {{ getHome }}
    </div>

</template>

<style scoped lang="scss">
.main {
    width: 100%;
    height: 100vh;
    justify-content: center;
    align-items: center;
    display: flex;
    flex-direction: column;

    h6 {
        color: gray;
        font-size: 20px;
    }
}
</style>
