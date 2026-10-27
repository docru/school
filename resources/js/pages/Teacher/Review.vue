<template>
    <div class="review-page">
        <div v-if="!isMobile" class="tw-flex tw-items-start tw-justify-between tw-gap-3 tw-mb-4">
            <div class="tw-min-w-0">
                <h1 class="adf-heading tw-text-2xl tw-leading-8 tw-m-0">Проверка</h1>
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

        <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center tw-gap-3 tw-mb-3">
            <v-select
                v-model="groupId"
                :items="groups"
                item-title="name"
                item-value="id"
                label="Группа"
                hide-details
                class="!tw-my-0 md:tw-max-w-sm tw-w-full"
            />
            <FilterChips v-model="filter" :items="filterItems" :counts="counts"/>
        </div>

        <v-progress-linear v-if="loading && !answers.length" indeterminate color="primary" class="tw-mb-4"/>

        <div v-if="filtered.length">
            <!-- Разбор прямо в списке: заходить в каждого ученика не нужно -->
            <div v-for="item in filtered" :key="item.id" class="section-card">
                <div class="section-card__header">
                    <div class="tw-min-w-0 tw-flex-1">
                        <div class="tw-text-sm tw-font-medium tw-truncate">{{ item.user_name }}</div>
                        <div class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted tw-truncate">
                            {{ kindTitle(item.kind) }} · {{ item.lesson_name }} · {{ item.question }}
                        </div>
                    </div>
                    <v-chip size="small" label :color="item.status === 'checked' ? 'success' : 'primary'" class="tw-shrink-0">
                        {{ item.status === 'checked' ? 'проверено' : 'сдано' }}
                    </v-chip>
                </div>

                <div class="tw-p-3 tw-flex tw-flex-col tw-gap-3">
                    <div class="answer-block">
                        <div class="bp-overline tw-mb-1">Ответ</div>
                        <div class="tw-text-sm tw-whitespace-pre-line">{{ item.answer }}</div>
                    </div>

                    <v-textarea
                        v-model="item.comment"
                        label="Комментарий ученику"
                        variant="outlined"
                        rows="2"
                        auto-grow
                        hide-details
                    />

                    <div class="tw-flex tw-justify-end tw-gap-2">
                        <v-btn
                            v-if="item.status === 'checked'"
                            variant="text"
                            color="muted"
                            size="small"
                            :loading="savingId === item.id"
                            @click="check(item, false)"
                        >Снять отметку</v-btn>
                        <v-btn
                            color="primary"
                            size="small"
                            prepend-icon="mdi-check"
                            :loading="savingId === item.id"
                            @click="check(item, true)"
                        >{{ item.status === 'checked' ? 'Сохранить' : 'Проверено' }}</v-btn>
                    </div>
                </div>
            </div>
        </div>

        <v-card v-else-if="!loading">
            <div class="tw-p-8 tw-text-center">
                <v-icon size="40" color="muted">mdi-check-all</v-icon>
                <div class="adf-heading tw-text-lg tw-mt-2 tw-mb-1">
                    {{ answers.length ? 'Ничего не найдено' : 'Сданных работ нет' }}
                </div>
                <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted">
                    {{ answers.length ? 'Измените фильтр' : 'Здесь появятся ответы, сданные учениками' }}
                </div>
            </div>
        </v-card>
    </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import FilterChips from "../../components/FilterChips.vue";

export default {
    name: 'Review',
    components: {FilterChips},
    data() {
        return {
            groupId: null,
            answers: [],
            loading: false,
            savingId: null,
            filter: 'submitted',
            filterItems: [
                {title: 'Ждут проверки', val: 'submitted'},
                {title: 'Проверено', val: 'checked'},
                {title: 'Д/З', val: 'homework'},
                {title: 'На уроке', val: 'classwork'},
                {title: 'Все', val: 'all'},
            ],
        }
    },
    computed: {
        ...mapGetters('teacher', {teacherGroups: 'getGroups'}),
        isMobile() {
            return this.$vuetify.display.name === 'sm';
        },
        groups() {
            return this.teacherGroups.map(g => ({id: g.id, name: g.name}));
        },
        counts() {
            return {
                all: this.answers.length,
                submitted: this.answers.filter(a => a.status === 'submitted').length,
                checked: this.answers.filter(a => a.status === 'checked').length,
                homework: this.answers.filter(a => a.kind === 'homework').length,
                classwork: this.answers.filter(a => a.kind === 'classwork').length,
            };
        },
        filtered() {
            if (this.filter === 'all') return this.answers;
            if (['homework', 'classwork'].includes(this.filter)) {
                return this.answers.filter(a => a.kind === this.filter);
            }
            return this.answers.filter(a => a.status === this.filter);
        },
        subtitle() {
            const n = this.counts.submitted;
            if (!this.answers.length) return 'Сданных работ нет';
            return n ? `${n} ждут проверки` : 'Всё проверено';
        },
    },
    watch: {
        groupId: 'load',
    },
    methods: {
        ...mapActions('teacher', ['actRequestGroups']),
        // В один список приходят оба вида заданий — различаем их подписью
        kindTitle(kind) {
            return kind === 'classwork' ? 'На уроке' : 'Д/З';
        },
        async load() {
            if (!this.groupId) return;
            this.loading = true;
            try {
                const res = await this.$store.dispatch('teacher/actRequestAnswers', {groupId: this.groupId});
                if (res !== false) this.answers = res;
            } finally {
                this.loading = false;
            }
        },
        async check(item, checked) {
            this.savingId = item.id;
            try {
                const res = await this.$store.dispatch('teacher/actCheckAnswer', {
                    id: item.id,
                    checked,
                    comment: item.comment,
                });
                if (res !== false) await this.load();
            } finally {
                this.savingId = null;
            }
        },
    },
    async created() {
        await this.actRequestGroups();
        this.groupId = this.groups[0]?.id ?? null;
    }
}
</script>

<style scoped lang="scss">
.answer-block {
    padding: 12px;
    border: 1px solid var(--bp-border);
    border-radius: var(--bp-radius-md, 8px);
    background: var(--bp-surface-muted);
}
</style>
