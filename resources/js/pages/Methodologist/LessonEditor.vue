<script>
import {MdPreview} from "md-editor-v3";

// Панель markdown-редактора: на телефоне остаётся только то, что влезает в ширину экрана
const TOOLBAR_LEFT = 'undo redo clear | h bold italic strikethrough quote | ul ol table hr | link image code';
const TOOLBAR_LEFT_MOBILE = 'undo redo | h bold italic | ul ol | link';
const TOOLBAR_RIGHT = 'preview sync-scroll fullscreen';
const TOOLBAR_RIGHT_MOBILE = 'preview';

export default {
    name: 'LessonEditor',
    components: {MdPreview},
    props: {
        // Объект урока из стора: правки полей сразу попадают в курс и уходят автосохранением
        lesson: {type: Object, required: true},
    },
    data() {
        return {
            tab: 'methodical',
            // Урок открывается на просмотр, правка включается кнопкой
            editing: false,
        }
    },
    computed: {
        isMobile() {
            return this.$vuetify.display.name === 'sm';
        },
        tabs() {
            return this.isMobile
                ? [
                    {value: 'methodical', title: 'План'},
                    {value: 'abstract', title: 'Конспект'},
                    {value: 'tasks', title: 'Задания'},
                    {value: 'homework', title: 'Д/З'},
                ]
                : [
                    {value: 'methodical', title: 'План урока'},
                    {value: 'abstract', title: 'Конспект'},
                    {value: 'tasks', title: 'Задания на уроке'},
                    {value: 'homework', title: 'Домашнее задание'},
                ];
        },
        // Правятся только вкладки с текстом: «Задания» и «Д/З» ещё не подключены
        isEditable() {
            return this.tab === 'methodical' || this.tab === 'abstract';
        },
        // На телефоне сплит «редактор + превью» не влезает, оставляем только редактор
        editorMode() {
            return this.isMobile ? 'edit' : 'editable';
        },
        leftToolbar() {
            return this.isMobile ? TOOLBAR_LEFT_MOBILE : TOOLBAR_LEFT;
        },
        rightToolbar() {
            return this.isMobile ? TOOLBAR_RIGHT_MOBILE : TOOLBAR_RIGHT;
        },
        editorHeight() {
            return this.isMobile ? '100%' : '400px';
        },
        mdTheme() {
            return this.$vuetify.theme.current.dark ? 'dark' : 'light';
        },
    },
    watch: {
        // Со сменой вкладки и урока возвращаемся к просмотру, чтобы правка не включалась незаметно
        tab() {
            this.editing = false;
        },
        'lesson.id'() {
            this.tab = 'methodical';
            this.editing = false;
        },
    },
    methods: {
        // В колонке hours nullable double: пустое поле — это null, а не пустая строка
        setHours(value) {
            const hours = parseFloat(String(value).replace(',', '.'));
            this.lesson.hours = Number.isFinite(hours) && hours >= 0 ? hours : null;
        },
    },
}
</script>

<template>
    <div class="lesson-editor" :class="{'lesson-editor--fill': isMobile}">
        <v-tabs v-model="tab" color="primary" density="comfortable" :grow="isMobile">
            <v-tab v-for="t in tabs" :key="t.value" :value="t.value">{{ t.title }}</v-tab>
        </v-tabs>
        <v-divider/>

        <!-- Название урока нужно на десктопе: там нет шапки окна, как на телефоне -->
        <div class="lesson-editor__bar">
            <div class="tw-min-w-0 tw-flex-1">
                <div v-if="!isMobile" class="adf-heading tw-text-base tw-leading-6 tw-truncate">{{ lesson.name }}</div>
                <div class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted">
                    <span v-if="lesson.hours">{{ lesson.hours }} ч</span>
                    <span v-else>часы не заданы</span>
                </div>
            </div>
            <v-btn
                v-if="isEditable"
                size="small"
                variant="text"
                :color="editing ? 'success' : 'primary'"
                :prepend-icon="editing ? 'mdi-check' : 'mdi-pencil-outline'"
                class="tw-shrink-0"
                @click="editing = !editing"
            >{{ editing ? 'Готово' : 'Редактировать' }}</v-btn>
        </div>

        <!-- touch: горизонтальный свайп мешает выделять текст в редакторе -->
        <v-window v-model="tab" :touch="false">
            <v-window-item value="methodical">
                <div class="tab-pane">
                    <template v-if="editing">
                        <div class="tw-flex tw-flex-col md:tw-flex-row tw-gap-3 tw-shrink-0 tw-pt-2">
                            <v-text-field
                                label="Название урока"
                                v-model="lesson.name"
                                variant="outlined"
                                hide-details
                                class="tw-flex-1 !tw-my-0"
                            />
                            <v-text-field
                                label="Часов"
                                :model-value="lesson.hours"
                                @update:model-value="setHours"
                                type="number"
                                min="0"
                                step="0.5"
                                variant="outlined"
                                hide-details
                                class="md:tw-max-w-[120px] !tw-my-0"
                            />
                        </div>
                        <v-md-editor
                            v-model="lesson.methodical_description"
                            :mode="editorMode"
                            :left-toolbar="leftToolbar"
                            :right-toolbar="rightToolbar"
                            :height="editorHeight"
                        />
                    </template>
                    <div v-else class="lesson-editor__preview">
                        <MdPreview v-if="lesson.methodical_description" :modelValue="lesson.methodical_description" :theme="mdTheme"/>
                        <div v-else class="tw-p-8 tw-text-center">
                            <v-icon size="40" color="muted">mdi-text-box-outline</v-icon>
                            <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted tw-mt-2">
                                План урока пустой — нажмите «Редактировать»
                            </div>
                        </div>
                    </div>
                </div>
            </v-window-item>

            <v-window-item value="abstract">
                <div class="tab-pane">
                    <v-md-editor
                        v-if="editing"
                        v-model="lesson.abstract"
                        :mode="editorMode"
                        :left-toolbar="leftToolbar"
                        :right-toolbar="rightToolbar"
                        :height="editorHeight"
                    />
                    <div v-else class="lesson-editor__preview">
                        <MdPreview v-if="lesson.abstract" :modelValue="lesson.abstract" :theme="mdTheme"/>
                        <div v-else class="tw-p-8 tw-text-center">
                            <v-icon size="40" color="muted">mdi-text-box-outline</v-icon>
                            <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted tw-mt-2">
                                Конспект пустой — нажмите «Редактировать»
                            </div>
                        </div>
                    </div>
                </div>
            </v-window-item>

            <v-window-item value="tasks">
                <div class="tw-p-8 tw-text-center">
                    <v-icon size="40" color="muted">mdi-clipboard-text-outline</v-icon>
                    <div class="adf-heading tw-text-lg tw-mt-2 tw-mb-1">Задания на уроке</div>
                    <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted">
                        Раздел ещё не подключён
                    </div>
                </div>
            </v-window-item>

            <v-window-item value="homework">
                <div class="tw-p-8 tw-text-center">
                    <v-icon size="40" color="muted">mdi-home-edit-outline</v-icon>
                    <div class="adf-heading tw-text-lg tw-mt-2 tw-mb-1">Домашнее задание</div>
                    <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted">
                        Раздел ещё не подключён
                    </div>
                </div>
            </v-window-item>
        </v-window>
    </div>
</template>

<style scoped lang="scss">
/* Редактор не должен распирать узкий экран: панель инструментов переносится, а не прокручивает страницу */
.lesson-editor :deep(.v-md-editor) {
    max-width: 100%;
    border-radius: var(--bp-radius-md, 8px);
}

.lesson-editor :deep(.v-md-editor__toolbar) {
    flex-wrap: wrap;
    height: auto;
}

.lesson-editor__bar {
    display: flex;
    align-items: center;
    gap: 8px;
    min-height: 44px;
    padding: 4px 4px 4px 2px;
}

/* Просмотр рендерится тем же MdPreview, которым урок видят учитель и ученик */
.lesson-editor__preview :deep(.md-editor-preview-wrapper) {
    padding: 0;
}

/* Четыре вкладки должны поместиться в 390px без стрелок прокрутки */
.lesson-editor--fill :deep(.v-tab) {
    min-width: 0;
    padding: 0 8px;
}

.tab-pane {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* Телефон: вкладки и строка действий прижаты сверху, содержимое занимает
   всю оставшуюся высоту окна и прокручивается внутри себя */
.lesson-editor--fill {
    display: flex;
    flex-direction: column;
    flex: 1 1 auto;
    min-height: 0;

    /* В колонке flex-элементы сжимаются по умолчанию: без этого строку вкладок
       съедает редактор, который требует всю высоту */
    :deep(.v-tabs),
    :deep(.v-divider),
    .lesson-editor__bar {
        flex: 0 0 auto;
    }

    :deep(.v-window) {
        flex: 1 1 auto;
        min-height: 0;
    }

    :deep(.v-window__container) {
        height: 100%;
    }

    :deep(.v-window-item) {
        height: 100%;
    }

    .tab-pane {
        height: 100%;
        min-height: 0;
    }

    :deep(.v-md-editor) {
        flex: 1 1 auto;
        min-height: 0;
    }

    .lesson-editor__preview {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
    }
}
</style>
