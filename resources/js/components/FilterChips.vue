<script>
/**
 * Фильтр списка: лента чипов со счётчиками.
 * Не переносится и не сжимается — при нехватке ширины прокручивается вбок,
 * поэтому число фильтров можно наращивать без ломки раскладки.
 */
export default {
    name: 'FilterChips',
    props: {
        modelValue: {type: [String, Number], default: null},
        // [{title, val}]
        items: {type: Array, default: () => []},
        // {val: число}
        counts: {type: Object, default: () => ({})},
    },
    emits: ['update:modelValue'],
    computed: {
        value: {
            get() {
                return this.modelValue;
            },
            set(val) {
                this.$emit('update:modelValue', val);
            },
        },
    },
}
</script>

<template>
    <v-chip-group v-model="value" mandatory selected-class="filter-chip--on" class="filter-chips">
        <v-chip
            v-for="item in items"
            :key="item.val"
            :value="item.val"
            variant="outlined"
            size="small"
            class="filter-chip"
        >
            {{ item.title }}
            <span class="filter-chip__count" v-if="counts[item.val] !== undefined">{{ counts[item.val] }}</span>
        </v-chip>
    </v-chip-group>
</template>

<style scoped lang="scss">
.filter-chips {
    /* Отступы группы Vuetify сдвигали бы ленту относительно поля поиска */
    :deep(.v-slide-group__content) {
        gap: 8px;
        padding: 2px 0;
    }
}

.filter-chip {
    border-color: var(--bp-border-control);
    color: var(--bp-text-muted);
}

.filter-chip--on {
    background: var(--bp-surface-muted);
    color: var(--bp-text-primary);
    border-color: var(--bp-accent);

    .filter-chip__count {
        background: var(--bp-surface-card);
    }
}

.filter-chip__count {
    margin-left: 6px;
    padding: 0 6px;
    font-size: 12px;
    line-height: 16px;
    border-radius: 999px;
    background: var(--bp-surface-muted);
}
</style>
