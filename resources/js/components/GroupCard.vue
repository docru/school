<script>
/**
 * Карточка группы: статус, прогресс по занятиям, состав, даты.
 * Одна на всех, кому нужен список групп — администратору, учителю, ученику.
 * Данные приходят одинаковые: Group::summary() на бэкенде.
 */
export default {
    name: 'GroupCard',
    props: {
        group: {type: Object, required: true},
        // куда ведёт карточка
        to: {type: Object, default: null},
        // ученику состав группы и список учителей не нужны
        roster: {type: Boolean, default: true},
    },
    computed: {
        statusTitle() {
            return this.group.status === 'open' ? 'Идёт' : 'В архиве';
        },
        statusColor() {
            return this.group.status === 'open' ? 'success' : 'muted';
        },
        progress() {
            if (!this.group.days_total) return 0;
            return Math.min(100, Math.round(this.group.days_closed / this.group.days_total * 100));
        },
    },
    methods: {
        fmtDate(value) {
            if (!value) return '—';
            const [y, m, d] = String(value).slice(0, 10).split('-');
            return `${d}.${m}.${y}`;
        },
    },
}
</script>

<template>
    <v-card class="group-card" :to="to">
        <div class="tw-p-4 tw-flex tw-flex-col tw-gap-4 tw-h-full">
            <!-- Название, курс, статус -->
            <div class="tw-flex tw-items-start tw-justify-between tw-gap-3">
                <div class="tw-min-w-0">
                    <div class="adf-heading tw-text-xl tw-leading-7 tw-truncate">{{ group.name }}</div>
                    <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted tw-truncate">
                        {{ group.course_name || 'Курс не задан' }}
                    </div>
                </div>
                <v-chip size="small" label :color="statusColor" class="tw-shrink-0">{{ statusTitle }}</v-chip>
            </div>

            <!-- Прогресс по учебным дням -->
            <div>
                <div class="tw-flex tw-justify-between tw-items-baseline tw-mb-1">
                    <span class="bp-overline">Занятия</span>
                    <span class="tw-text-sm">
                        <span class="tw-font-medium">{{ group.days_closed }}</span>
                        <span class="tw-text-text-muted dark:tw-text-dark-text-muted"> из {{ group.days_total }}</span>
                    </span>
                </div>
                <v-progress-linear
                    :model-value="progress"
                    height="4"
                    rounded
                    color="primary"
                    bg-color="border"
                    bg-opacity="1"
                />
            </div>

            <!-- Показатели -->
            <div class="tw-grid tw-gap-3" :class="roster ? 'tw-grid-cols-3' : 'tw-grid-cols-2'">
                <div v-if="roster">
                    <div class="bp-overline">Ученики</div>
                    <div class="tw-text-sm tw-mt-0.5">
                        <span class="tw-font-medium">{{ group.disciples_active }}</span>
                        <span v-if="group.disciples_expelled" class="tw-text-text-muted dark:tw-text-dark-text-muted">
                            · отчисл. {{ group.disciples_expelled }}
                        </span>
                    </div>
                </div>
                <div>
                    <div class="bp-overline">Старт</div>
                    <div class="tw-text-sm tw-mt-0.5">{{ fmtDate(group.first_day) }}</div>
                </div>
                <div>
                    <div class="bp-overline">{{ group.open_day ? 'Текущее' : 'Последнее' }}</div>
                    <div class="tw-text-sm tw-mt-0.5">{{ fmtDate(group.open_day || group.last_day) }}</div>
                </div>
            </div>

            <!-- Учителя -->
            <div v-if="roster" class="tw-mt-auto tw-pt-3 tw-border-t tw-border-border dark:tw-border-dark-border">
                <div class="bp-overline">Учителя</div>
                <div class="tw-text-sm tw-mt-0.5" v-if="group.teachers?.length">{{ group.teachers.join(', ') }}</div>
                <div class="tw-text-sm tw-mt-0.5 tw-text-text-muted dark:tw-text-dark-text-muted" v-else>
                    не назначены
                </div>
            </div>
        </div>
    </v-card>
</template>

<style scoped lang="scss">
.group-card {
    transition: border-color .15s ease, transform .15s ease;

    &:hover {
        border-color: var(--bp-accent) !important;
    }
}
</style>
