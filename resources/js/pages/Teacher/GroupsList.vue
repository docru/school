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
        <div v-if="groups.length" class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 xl:tw-grid-cols-3 tw-gap-4">
            <GroupCard
                v-for="g in groups"
                :key="g.id"
                :group="g"
                :to="{name: 'TeacherDetailGroup', params: {id: g.id}}"
            />
        </div>

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
import GroupCard from "../../components/GroupCard.vue";

export default {
    name: 'GroupsList',
    components: {GroupCard},
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
    },
    created() {
        this.actRequestGroups();
    }
}
</script>

