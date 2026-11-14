<template>
    <div class="homework-page">
        <div v-if="!isMobile" class="tw-flex tw-items-start tw-justify-between tw-gap-3 tw-mb-4">
            <div class="tw-min-w-0">
                <h1 class="adf-heading tw-text-2xl tw-leading-8 tw-m-0">Домашние задания</h1>
                <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted">{{ subtitle }}</div>
            </div>
            <v-btn
                icon="mdi-refresh"
                variant="text"
                color="muted"
                size="small"
                :loading="loading"
                title="Обновить"
                class="tw-shrink-0"
                @click="load"
            />
        </div>

        <FilterChips v-model="filter" :items="filterItems" :counts="counts" class="tw-mb-3"/>

        <v-progress-linear v-if="loading && !items.length" indeterminate color="primary" class="tw-mb-4"/>

        <v-card v-if="filtered.length">
            <ListRow
                v-for="item in filtered"
                :key="item.lesson_id + '_' + item.group_id"
                :title="item.lesson_name"
                :muted="item.status === 'checked'"
                clickable
                @click="openLesson(item)"
            >
                <template #subtitle>
                    <span v-if="item.date">{{ fmtDate(item.date) }}</span>
                    <span> · {{ item.group_name }}</span>
                    <span v-if="item.questions"> · ответов {{ item.answered }} из {{ item.questions }}</span>
                </template>
                <template #actions>
                    <v-chip size="small" label :color="statusColor(item.status)" class="tw-shrink-0">
                        {{ statusTitle(item.status) }}
                    </v-chip>
                </template>
            </ListRow>
        </v-card>

        <v-card v-else-if="!loading">
            <div class="tw-p-8 tw-text-center">
                <v-icon size="40" color="muted">mdi-home-edit-outline</v-icon>
                <div class="adf-heading tw-text-lg tw-mt-2 tw-mb-1">
                    {{ items.length ? 'Ничего не найдено' : 'Заданий пока нет' }}
                </div>
                <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted">
                    {{ items.length ? 'Измените фильтр' : 'Домашнее задание появится после занятия' }}
                </div>
            </div>
        </v-card>
    </div>
</template>

<script>
import ListRow from "../../components/ListRow.vue";
import FilterChips from "../../components/FilterChips.vue";

const TITLES = {
    new: 'не начато',
    draft: 'черновик',
    submitted: 'сдано',
    checked: 'проверено',
    noQuestions: 'к ознакомлению',
};

export default {
    name: 'Homework',
    components: {ListRow, FilterChips},
    data() {
        return {
            items: [],
            loading: false,
            filter: 'todo',
            filterItems: [
                {title: 'К выполнению', val: 'todo'},
                {title: 'Сдано', val: 'submitted'},
                {title: 'Проверено', val: 'checked'},
                {title: 'Все', val: 'all'},
            ],
        }
    },
    computed: {
        isMobile() {
            return this.$vuetify.display.name === 'sm';
        },
        counts() {
            return {
                all: this.items.length,
                todo: this.items.filter(i => ['new', 'draft'].includes(i.status)).length,
                submitted: this.items.filter(i => i.status === 'submitted').length,
                checked: this.items.filter(i => i.status === 'checked').length,
            };
        },
        filtered() {
            if (this.filter === 'all') return this.items;
            if (this.filter === 'todo') return this.items.filter(i => ['new', 'draft'].includes(i.status));
            return this.items.filter(i => i.status === this.filter);
        },
        subtitle() {
            const n = this.counts.todo;
            if (!this.items.length) return 'Заданий пока нет';
            return n ? `${n} ждут выполнения` : 'Всё сдано';
        },
    },
    methods: {
        statusTitle(status) {
            return TITLES[status] ?? status;
        },
        statusColor(status) {
            if (status === 'checked') return 'success';
            if (status === 'submitted') return 'primary';
            if (status === 'draft') return 'warning';
            return 'muted';
        },
        fmtDate(value) {
            if (!value) return '—';
            const [y, m, d] = String(value).slice(0, 10).split('-');
            return `${d}.${m}.${y}`;
        },
        openLesson(item) {
            this.$router.push({
                name: 'DiscipleLesson',
                params: {groupId: item.group_id, lessonId: item.lesson_id},
            });
        },
        async load() {
            this.loading = true;
            try {
                const res = await this.$store.dispatch('disciple/actRequestHomework');
                if (res !== false) this.items = res;
            } finally {
                this.loading = false;
            }
        },
    },
    created() {
        this.load();
    }
}
</script>
