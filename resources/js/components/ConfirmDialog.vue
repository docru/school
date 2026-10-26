<template>
    <v-dialog
        :model-value="isConfirm.open"
        width="360"
        max-width="calc(100vw - 32px)"
        @update:model-value="val => !val && closeConfirm(false)"
    >
        <v-card class="confirm-dialog tw-p-5">
            <div class="adf-heading tw-text-xl tw-leading-7 tw-mb-2">{{ isConfirm.title }}</div>
            <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted" v-if="isConfirm.text">
                {{ isConfirm.text }}
            </div>
            <div class="tw-flex tw-justify-end tw-gap-2 tw-mt-5">
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
