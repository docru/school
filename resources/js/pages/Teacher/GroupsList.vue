<template>
    <div class="teacher-groups">
        <!-- Шапка (только десктоп): на мобильном название раздела уже есть в хлебных крошках -->
        <div v-if="!isMobile" class="tw-flex tw-items-start tw-justify-between tw-gap-3 tw-mb-4">
            <div class="tw-min-w-0">
                <h1 class="adf-heading tw-text-2xl tw-leading-8 tw-m-0">Мои группы</h1>
                <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted">{{ subtitle }}</div>
            </div>
            <v-btn
                icon="mdi-refresh"
                variant="text"
                color="muted"
                size="small"
                :loading="load"
                title="Обновить список"
                class="tw-shrink-0"
                @click="actRequestGroups"
            />
        </div>

        <v-progress-linear v-if="load && !groups.length" indeterminate color="primary" class="tw-mb-4"/>

        <!-- Карточки: на телефоне таблица из двух колонок читалась как два значения без подписей -->
        <v-card v-if="groups.length">
            <div
                v-for="item in groups"
                :key="item.id"
                class="group-row"
                @click="openGroup(item)"
            >
                <div class="tw-min-w-0 tw-flex-1">
                    <div class="tw-text-[15px] tw-font-medium tw-truncate">{{ item.group.name }}</div>
                    <div class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted tw-truncate">
                        {{ item.course?.name || 'курс не указан' }}
                    </div>
                </div>
                <v-icon size="20" color="muted" class="tw-shrink-0">mdi-chevron-right</v-icon>
            </div>
        </v-card>

        <!-- Пустое состояние: раньше страница показывала только заголовок -->
        <v-card v-else-if="!load">
            <div class="tw-p-8 tw-text-center">
                <v-icon size="40" color="muted">mdi-account-group-outline</v-icon>
                <div class="adf-heading tw-text-lg tw-mt-2 tw-mb-1">Групп пока нет</div>
                <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted">
                    Группы появятся здесь, когда вас назначат учителем
                </div>
            </div>
        </v-card>
    </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";

export default {
    name: 'GroupsList',
    computed: {
        ...mapGetters('teacher', {groups: 'getGroups', load: 'getLoad'}),
        isMobile() {
            return this.$vuetify.display.name === 'sm';
        },
        subtitle() {
            const n = this.groups.length;
            if (!n) return 'Групп пока нет';
            return `${n} ${this.plural(n, ['группа', 'группы', 'групп'])}`;
        },
    },
    methods: {
        ...mapActions('teacher', ['actRequestGroups']),
        plural(n, forms) {
            const abs = Math.abs(n) % 100;
            const last = abs % 10;
            if (abs > 10 && abs < 20) return forms[2];
            if (last > 1 && last < 5) return forms[1];
            if (last === 1) return forms[0];
            return forms[2];
        },
        openGroup(item) {
            this.$router.push({name: 'TeacherDetailGroup', params: {id: item.group.id}});
        },
    },
    created() {
        this.actRequestGroups();
    }
}
</script>

<style scoped lang="scss">
.group-row {
    display: flex;
    align-items: center;
    gap: 12px;
    min-height: 56px;
    padding: 8px 12px 8px 16px;
    border-bottom: 1px solid var(--bp-border);
    cursor: pointer;

    &:last-child {
        border-bottom: 0;
    }

    &:active {
        background: var(--bp-surface-muted);
    }
}
</style>
