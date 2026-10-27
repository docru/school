<script>
import draggable from 'vuedraggable';

/**
 * Редактор блоков задания в уроке: домашнее задание или задание на уроке.
 * Вид задаётся пропом kind, устройство блоков одинаковое.
 *
 * Блок сохраняется сам через секунду после последней правки — как курс.
 * Создание, удаление и перенос сохраняются сразу.
 */
const TYPES = [
    {type: 'memorize', title: 'Выучить наизусть', icon: 'mdi-brain'},
    {type: 'question', title: 'Вопрос с ответом', icon: 'mdi-comment-question-outline'},
    {type: 'video', title: 'Посмотреть видео', icon: 'mdi-play-circle-outline'},
    {type: 'text', title: 'Текст', icon: 'mdi-text-box-outline'},
];

export default {
    name: 'TaskEditor',
    components: {draggable},
    props: {
        lessonId: {type: [Number, String], required: true},
        kind: {type: String, default: 'homework'},
    },
    data() {
        return {
            types: TYPES,
            tasks: [],
            loading: false,
            saveTimers: {},
        }
    },
    computed: {
        isMobile() {
            return this.$vuetify.display.name === 'sm';
        },
        dragDelay() {
            return this.isMobile ? 120 : 0;
        },
    },
    watch: {
        lessonId: 'load',
        kind: 'load',
    },
    methods: {
        typeTitle(type) {
            return TYPES.find(t => t.type === type)?.title ?? type;
        },
        typeIcon(type) {
            return TYPES.find(t => t.type === type)?.icon ?? 'mdi-shape-outline';
        },
        async load() {
            if (!this.lessonId) return;
            this.loading = true;
            try {
                const res = await this.$store.dispatch('methodologist/actRequestTasks', {
                    lessonId: this.lessonId,
                    kind: this.kind,
                });
                if (res !== false) this.tasks = res;
            } finally {
                this.loading = false;
            }
        },
        async addTask(type) {
            const res = await this.$store.dispatch('methodologist/actCreateTask', {
                lesson_id: this.lessonId,
                kind: this.kind,
                type,
            });
            if (res !== false) this.tasks = res;
        },
        // Правка блока уходит на сервер через секунду после последнего нажатия
        touch(task) {
            clearTimeout(this.saveTimers[task.id]);
            this.saveTimers[task.id] = setTimeout(() => this.saveTask(task), 1000);
        },
        async saveTask(task) {
            await this.$store.dispatch('methodologist/actSaveTask', {
                id: task.id,
                title: task.title,
                body: task.body,
                url: task.url,
            });
        },
        async removeTask(task) {
            const ok = await this.$confirm({
                title: 'Удалить блок?',
                text: `«${task.title}» и ответы учеников на него будут удалены.`,
                okText: 'Удалить',
                color: 'error',
            });
            if (!ok) return;
            const res = await this.$store.dispatch('methodologist/actDeleteTask', task.id);
            if (res !== false) this.tasks = res;
        },
        async onReorder() {
            const res = await this.$store.dispatch('methodologist/actReorderTasks', {
                ids: this.tasks.map(t => t.id),
            });
            if (res !== false) this.tasks = res;
        },
    },
    created() {
        this.load();
    },
    unmounted() {
        Object.values(this.saveTimers).forEach(clearTimeout);
    },
}
</script>

<template>
    <div class="task-editor">
        <!-- Чем добавлять блок -->
        <div class="tw-flex tw-flex-wrap tw-gap-2 tw-mb-3">
            <v-btn
                v-for="t in types"
                :key="t.type"
                variant="outlined"
                color="primary"
                size="small"
                :prepend-icon="t.icon"
                @click="addTask(t.type)"
            >{{ t.title }}</v-btn>
        </div>

        <v-progress-linear v-if="loading && !tasks.length" indeterminate color="primary"/>

        <div v-else-if="!tasks.length" class="tw-p-8 tw-text-center">
            <v-icon size="40" color="muted">mdi-clipboard-text-outline</v-icon>
            <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted tw-mt-2">
                Блоков пока нет — добавьте первый кнопкой выше
            </div>
        </div>

        <!-- Блоки переносятся за ручку; нумерация пересчитывается на сервере -->
        <draggable
            v-else
            :list="tasks"
            item-key="id"
            handle=".drag-handle"
            :delay="dragDelay"
            :delay-on-touch-only="true"
            @end="onReorder"
        >
            <template #item="{element: task, index}">
                <div class="section-card">
                    <div class="section-card__header">
                        <v-icon size="18" color="muted" class="drag-handle tw-cursor-grab tw-shrink-0">mdi-drag</v-icon>
                        <span class="row-num">{{ index + 1 }}</span>
                        <div class="tw-min-w-0 tw-flex-1">
                            <div class="bp-overline tw-flex tw-items-center tw-gap-1">
                                <v-icon size="14">{{ typeIcon(task.type) }}</v-icon>
                                {{ typeTitle(task.type) }}
                            </div>
                        </div>
                        <v-btn
                            icon="mdi-delete-outline"
                            variant="text"
                            color="error"
                            size="small"
                            density="comfortable"
                            title="Удалить блок"
                            class="tw-shrink-0"
                            @click="removeTask(task)"
                        />
                    </div>

                    <div class="tw-p-3 tw-flex tw-flex-col tw-gap-3">
                        <v-text-field
                            v-model="task.title"
                            label="Название"
                            variant="outlined"
                            hide-details
                            @update:model-value="touch(task)"
                        />

                        <v-text-field
                            v-if="task.type === 'video'"
                            v-model="task.url"
                            label="Ссылка на видео (YouTube, VK)"
                            placeholder="https://youtu.be/…"
                            variant="outlined"
                            hide-details
                            @update:model-value="touch(task)"
                        />

                        <v-textarea
                            v-model="task.body"
                            :label="task.type === 'question' ? 'Описание вопроса' : 'Текст'"
                            variant="outlined"
                            rows="3"
                            auto-grow
                            hide-details
                            @update:model-value="touch(task)"
                        />

                        <div v-if="task.type === 'question'" class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted">
                            Ученик увидит поле для ответа, учитель — ответ в списке проверки
                        </div>
                    </div>
                </div>
            </template>
        </draggable>
    </div>
</template>
