<script>
import {MdPreview} from "md-editor-v3";

/**
 * Просмотр урока: заголовок, часы, возврат к списку и содержимое вкладками.
 * Одна страница на учителя и ученика — различается только состав вкладок:
 * учителю доступен план урока, ученику только конспект.
 */
export default {
    name: 'LessonView',
    components: {MdPreview},
    props: {
        // {name, hours, methodical_description, abstract} или null, пока грузится
        lesson: {type: Object, default: null},
        // [{value, title, field}] — field это поле урока с markdown
        tabs: {type: Array, required: true},
        // маршрут кнопки возврата
        backTo: {type: Object, default: null},
    },
    data() {
        return {
            // Вкладка задана сразу: при null окно осталось бы пустым до первого клика
            tab: this.tabs[0]?.value ?? null,
        }
    },
    computed: {
        isMobile() {
            return this.$vuetify.display.name === 'sm';
        },
        subtitle() {
            if (!this.lesson) return 'Загрузка…';
            const hours = Number(this.lesson.hours) || 0;
            return hours ? `${hours} ${this.plural(hours, ['час', 'часа', 'часов'])}` : 'часы не заданы';
        },
        mdTheme() {
            return this.$vuetify.theme.current.dark ? 'dark' : 'light';
        },
    },
    methods: {
        plural(n, forms) {
            const abs = Math.abs(n) % 100;
            const last = abs % 10;
            if (abs > 10 && abs < 20) return forms[2];
            if (last > 1 && last < 5) return forms[1];
            if (last === 1) return forms[0];
            return forms[2];
        },
    },
}
</script>

<template>
    <div class="lesson-view">
        <div class="tw-flex tw-items-start tw-justify-between tw-gap-3 tw-mb-3">
            <div class="tw-min-w-0">
                <h1 class="adf-heading tw-text-xl md:tw-text-2xl tw-leading-7 md:tw-leading-8 tw-m-0">
                    {{ lesson?.name || 'Урок' }}
                </h1>
                <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted">{{ subtitle }}</div>
            </div>
            <v-btn
                v-if="backTo"
                variant="text"
                color="muted"
                size="small"
                :icon="isMobile ? 'mdi-arrow-left' : undefined"
                :prepend-icon="isMobile ? undefined : 'mdi-arrow-left'"
                title="К списку уроков"
                class="tw-shrink-0"
                :to="backTo"
            >{{ isMobile ? '' : 'К урокам' }}</v-btn>
        </div>

        <v-progress-linear v-if="!lesson" indeterminate color="primary"/>

        <v-card v-else>
            <!-- bg-color не задаём: на фоне primary активная вкладка красится тем же цветом и пропадает -->
            <template v-if="tabs.length > 1">
                <v-tabs v-model="tab" :grow="isMobile" color="primary">
                    <v-tab v-for="t in tabs" :key="t.value" :value="t.value">{{ t.title }}</v-tab>
                </v-tabs>
                <v-divider/>
            </template>

            <!-- touch: горизонтальный свайп мешает выделять текст -->
            <v-window v-model="tab" :touch="false">
                <v-window-item v-for="t in tabs" :key="t.value" :value="t.value">
                    <div class="tw-p-3 md:tw-p-4">
                        <MdPreview v-if="lesson[t.field]" :modelValue="lesson[t.field]" :theme="mdTheme"/>
                        <div v-else class="tw-p-8 tw-text-center">
                            <v-icon size="40" color="muted">mdi-text-box-outline</v-icon>
                            <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted tw-mt-2">
                                {{ t.title }} не заполнен
                            </div>
                        </div>
                    </div>
                </v-window-item>
            </v-window>
        </v-card>
    </div>
</template>

<style scoped lang="scss">
/* Отступы берёт на себя обёртка вкладки */
.lesson-view :deep(.md-editor-preview-wrapper) {
    padding: 0;
}
</style>
