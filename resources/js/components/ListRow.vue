<script>
/**
 * Строка списка: человек, курс, группа, урок.
 * Единственное представление на все разделы — раньше одна и та же строка
 * была скопирована по страницам под разными именами классов.
 *
 * Слоты: lead — метка слева (точка входа, номер), subtitle — строка под именем,
 * actions — кнопки справа (по умолчанию стрелка, если строка кликабельна).
 */
export default {
    name: 'ListRow',
    props: {
        title: {type: String, default: ''},
        clickable: {type: Boolean, default: false},
        // отчислен или удалён: строка приглушается
        muted: {type: Boolean, default: false},
        // замечание, например много пропусков
        warning: {type: Boolean, default: false},
        // плотный режим: уроки внутри карточки модуля или учебного дня
        dense: {type: Boolean, default: false},
        // выбранная строка (например, открытый урок)
        active: {type: Boolean, default: false},
        // разрешить заголовку перенос на вторую строку вместо обрезки
        wrapTitle: {type: Boolean, default: false},
    },
    emits: ['click'],
}
</script>

<template>
    <div
        class="list-row"
        :class="{
            'list-row--clickable': clickable,
            'list-row--muted': muted,
            'list-row--warning': warning,
            'list-row--dense': dense,
            'list-row--active': active,
        }"
        @click="clickable ? $emit('click') : null"
    >
        <slot name="lead"/>
        <div class="tw-min-w-0 tw-flex-1">
            <div
                :class="[
                    wrapTitle ? 'tw-line-clamp-2' : 'tw-truncate',
                    dense ? 'tw-text-sm' : 'tw-text-[15px] tw-font-medium',
                ]"
            >{{ title }}</div>
            <div class="tw-text-xs tw-mt-0.5 tw-truncate tw-text-text-muted dark:tw-text-dark-text-muted">
                <slot name="subtitle"/>
            </div>
        </div>
        <slot name="actions">
            <v-icon v-if="clickable" size="20" color="muted" class="tw-shrink-0">mdi-chevron-right</v-icon>
        </slot>
    </div>
</template>

<style scoped lang="scss">
.list-row {
    display: flex;
    align-items: center;
    gap: 12px;
    min-height: 56px;
    padding: 8px 8px 8px 16px;
    border-bottom: 1px solid var(--bp-border);

    &:last-child {
        border-bottom: 0;
    }

    &--clickable {
        cursor: pointer;

        &:active {
            background: var(--bp-surface-muted);
        }
    }

    &--muted {
        opacity: .6;
    }

    &--active {
        background: var(--bp-surface-muted);
        box-shadow: inset 3px 0 0 var(--bp-accent);
    }

    &--dense {
        min-height: 44px;
        gap: 8px;
        padding: 4px 6px 4px 10px;
    }

    /* Замечание помечается полосой слева, а не цветом всей строки */
    &--warning {
        box-shadow: inset 3px 0 0 var(--bp-warning, #f59e0b);
    }
}
</style>
