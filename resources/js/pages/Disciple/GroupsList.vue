<template>
    <div class="disciple-groups">
        <!-- Шапка (только десктоп): на мобильном название раздела уже есть в хлебных крошках -->
        <div v-if="!isMobile" class="tw-flex tw-items-start tw-justify-between tw-gap-3 tw-mb-4">
            <div class="tw-min-w-0">
                <h1 class="adf-heading tw-text-2xl tw-leading-8 tw-m-0">Мои курсы</h1>
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

        <div v-if="groups.length" class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 xl:tw-grid-cols-3 tw-gap-4">
            <!-- roster: ученику состав группы и список учителей не показываем -->
            <GroupCard
                v-for="g in groups"
                :key="g.id"
                :group="g"
                :roster="false"
                :to="{name: 'DiscipleDetailGroup', params: {id: g.id}}"
            />
        </div>

        <v-card v-else-if="!load">
            <div class="tw-p-8 tw-text-center">
                <v-icon size="40" color="muted">mdi-school-outline</v-icon>
                <div class="adf-heading tw-text-lg tw-mt-2 tw-mb-1">Курсов пока нет</div>
                <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted">
                    Курс появится здесь, когда вас зачислят в группу
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
        ...mapGetters('disciple', {groups: 'getGroups', load: 'getLoad'}),
        isMobile() {
            return this.$vuetify.display.name === 'sm';
        },
        subtitle() {
            const n = this.groups.length;
            if (!n) return 'Курсов пока нет';
            return `${n} ${this.plural(n, ['курс', 'курса', 'курсов'])}`;
        },
    },
    methods: {
        ...mapActions('disciple', ['actRequestGroups']),
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
