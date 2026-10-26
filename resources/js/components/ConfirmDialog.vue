<template>
    <v-dialog
        :model-value="isConfirm.open"
        width="360"
        max-width="calc(100vw - 32px)"
        @update:model-value="val => !val && closeConfirm(false)"
    >
        <v-card class="confirm-dialog">
            <div class="confirm-dialog__title adf-heading">{{ isConfirm.title }}</div>
            <div class="confirm-dialog__text" v-if="isConfirm.text">{{ isConfirm.text }}</div>
            <div class="confirm-dialog__actions">
                <v-btn variant="text" color="muted" @click="closeConfirm(false)">{{ isConfirm.cancelText }}</v-btn>
                <v-btn :color="isConfirm.color" @click="closeConfirm(true)">{{ isConfirm.okText }}</v-btn>
            </div>
        </v-card>
    </v-dialog>
</template>

<script>
import {mapGetters, mapMutations} from "vuex";

// Глобальное окно подтверждения: вызывается через this.$confirm({title, text, okText, color})
export default {
    name: "ConfirmDialog",
    computed: {
        ...mapGetters('app', ['isConfirm']),
    },
    methods: {
        ...mapMutations('app', ['closeConfirm']),
    },
}
</script>

<style lang="scss">
/* Отступы задаём здесь, а не tw-утилитами: Vuetify обнуляет padding у .v-card и его CSS подключается позже Tailwind */
.confirm-dialog.v-card {
    padding: 20px 20px 16px;

    .confirm-dialog__title {
        font-size: 20px;
        line-height: 28px;
    }

    .confirm-dialog__text {
        margin-top: 8px;
        font-size: 14px;
        line-height: 20px;
        color: var(--bp-text-muted);
    }

    .confirm-dialog__actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 20px;
    }
}
</style>
