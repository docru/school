<script>
/**
 * Блоки задания: домашнего или на уроке.
 *
 * mode="view" — учитель, только ознакомление.
 * mode="fill" — ученик: на вопросах появляется поле ответа, оно сохраняется
 * само через секунду после набора, внизу кнопка «Сдать на проверку».
 * После сдачи правка закрыта, виден статус и комментарий учителя.
 */
const TYPE_META = {
    memorize: {title: 'Выучить наизусть', icon: 'mdi-brain'},
    question: {title: 'Вопрос', icon: 'mdi-comment-question-outline'},
    video: {title: 'Видео', icon: 'mdi-play-circle-outline'},
    text: {title: 'Текст', icon: 'mdi-text-box-outline'},
};

export default {
    name: 'TaskList',
    props: {
        tasks: {type: Array, default: () => []},
        // {task_id: {answer, status, comment, checked_at}}
        answers: {type: Object, default: () => ({})},
        mode: {type: String, default: 'view'},
        lessonId: {type: [Number, String], default: null},
        groupId: {type: [Number, String], default: null},
        kind: {type: String, default: 'homework'},
    },
    emits: ['reload'],
    data() {
        return {
            drafts: {},
            saveTimers: {},
            saving: false,
        }
    },
    computed: {
        questions() {
            return this.tasks.filter(t => t.type === 'question');
        },
        // Сдавать нечего, если вопросов нет
        canSubmit() {
            if (this.mode !== 'fill' || !this.questions.length) return false;
            if (this.submitted) return false;
            return this.questions.every(t => (this.draftOf(t) ?? '').trim().length > 0);
        },
        submitted() {
            return this.questions.length > 0
                && this.questions.every(t => ['submitted', 'checked'].includes(this.answers[t.id]?.status));
        },
        checked() {
            return this.questions.length > 0
                && this.questions.every(t => this.answers[t.id]?.status === 'checked');
        },
        statusLabel() {
            if (this.checked) return 'Проверено';
            if (this.submitted) return 'Сдано, ждёт проверки';
            return '';
        },
    },
    methods: {
        meta(type) {
            return TYPE_META[type] ?? {title: type, icon: 'mdi-shape-outline'};
        },
        draftOf(task) {
            return this.drafts[task.id] ?? this.answers[task.id]?.answer ?? '';
        },
        isLocked(task) {
            return ['submitted', 'checked'].includes(this.answers[task.id]?.status);
        },
        // YouTube показываем встроенным плеером, остальное — ссылкой
        embedUrl(url) {
            if (!url) return null;
            const m = String(url).match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/))([\w-]{6,})/);
            return m ? `https://www.youtube.com/embed/${m[1]}` : null;
        },
        onInput(task, value) {
            this.drafts[task.id] = value;
            clearTimeout(this.saveTimers[task.id]);
            this.saveTimers[task.id] = setTimeout(() => this.saveDraft(task), 1000);
        },
        async saveDraft(task) {
            await this.$store.dispatch('disciple/actSaveAnswer', {
                task_id: task.id,
                group_id: this.groupId,
                answer: this.draftOf(task),
            });
        },
        async submit() {
            const ok = await this.$confirm({
                title: 'Сдать на проверку?',
                text: 'После сдачи изменить ответы будет нельзя.',
                okText: 'Сдать',
            });
            if (!ok) return;

            this.saving = true;
            try {
                // Несохранённые черновики отправляем до сдачи
                Object.values(this.saveTimers).forEach(clearTimeout);
                for (const task of this.questions) {
                    await this.saveDraft(task);
                }
                const res = await this.$store.dispatch('disciple/actSubmitAnswers', {
                    lesson_id: this.lessonId,
                    group_id: this.groupId,
                    kind: this.kind,
                });
                if (res !== false) this.$emit('reload');
            } finally {
                this.saving = false;
            }
        },
    },
    unmounted() {
        Object.values(this.saveTimers).forEach(clearTimeout);
    },
}
</script>

<template>
    <div class="task-list">
        <div v-if="!tasks.length" class="tw-p-8 tw-text-center">
            <v-icon size="40" color="muted">mdi-clipboard-text-outline</v-icon>
            <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted tw-mt-2">
                Заданий нет
            </div>
        </div>

        <template v-else>
            <!-- Состояние работы целиком -->
            <div v-if="statusLabel" class="tw-mb-3">
                <v-chip size="small" label :color="checked ? 'success' : 'primary'">{{ statusLabel }}</v-chip>
            </div>

            <div v-for="(task, index) in tasks" :key="task.id" class="section-card">
                <div class="section-card__header">
                    <span class="row-num">{{ index + 1 }}</span>
                    <div class="tw-min-w-0 tw-flex-1">
                        <div class="bp-overline tw-flex tw-items-center tw-gap-1">
                            <v-icon size="14">{{ meta(task.type).icon }}</v-icon>
                            {{ meta(task.type).title }}
                        </div>
                        <div class="tw-text-sm tw-font-medium">{{ task.title }}</div>
                    </div>
                </div>

                <div class="tw-p-3 tw-flex tw-flex-col tw-gap-3">
                    <div v-if="task.body" class="tw-text-sm tw-whitespace-pre-line">{{ task.body }}</div>

                    <!-- Видео -->
                    <template v-if="task.type === 'video' && task.url">
                        <div v-if="embedUrl(task.url)" class="video-frame">
                            <iframe :src="embedUrl(task.url)" allowfullscreen loading="lazy"/>
                        </div>
                        <v-btn
                            v-else
                            variant="outlined"
                            color="primary"
                            size="small"
                            prepend-icon="mdi-open-in-new"
                            :href="task.url"
                            target="_blank"
                            class="tw-self-start"
                        >Открыть видео</v-btn>
                    </template>

                    <!-- Ответ ученика -->
                    <template v-if="task.type === 'question' && mode === 'fill'">
                        <v-textarea
                            :model-value="draftOf(task)"
                            @update:model-value="onInput(task, $event)"
                            label="Ваш ответ"
                            variant="outlined"
                            rows="3"
                            auto-grow
                            hide-details
                            :readonly="isLocked(task)"
                            :class="{'tw-opacity-70': isLocked(task)}"
                        />
                        <div v-if="answers[task.id]?.comment" class="comment-block">
                            <div class="bp-overline tw-mb-1">Комментарий учителя</div>
                            <div class="tw-text-sm tw-whitespace-pre-line">{{ answers[task.id].comment }}</div>
                        </div>
                    </template>

                    <!-- Учителю в уроке показываем только сам вопрос: ответы в разделе проверки -->
                    <div
                        v-else-if="task.type === 'question'"
                        class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted"
                    >Ответы учеников — в разделе «Проверка»</div>
                </div>
            </div>

            <div v-if="mode === 'fill' && questions.length && !submitted" class="tw-mt-4">
                <v-btn
                    color="primary"
                    block
                    :disabled="!canSubmit"
                    :loading="saving"
                    @click="submit"
                >Сдать на проверку</v-btn>
                <div v-if="!canSubmit" class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted tw-mt-2 tw-text-center">
                    Ответьте на все вопросы
                </div>
            </div>
        </template>
    </div>
</template>

<style scoped lang="scss">
/* Плеер держит пропорции 16:9 и не вылезает за ширину экрана */
.video-frame {
    position: relative;
    width: 100%;
    padding-top: 56.25%;
    border-radius: var(--bp-radius-md, 8px);
    overflow: hidden;

    iframe {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }
}

.comment-block {
    padding: 12px;
    border: 1px solid var(--bp-border);
    border-radius: var(--bp-radius-md, 8px);
    background: var(--bp-surface-muted);
}
</style>
