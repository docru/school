<script>
import draggable from 'vuedraggable'
import ListRow from "../../components/ListRow.vue";

export default {
    name: "ModuleList",
    components: {draggable, ListRow},
    props: {
        getStudyProgram: {type: Array, default: () => []},
        // id выбранного урока — по нему подсвечивается строка
        curLessonId: {default: false},
        isMobile: {type: Boolean, default: false},
    },
    emits: ['openDialogCreateLesson', 'deleteModule', 'chooseLesson', 'deleteLesson'],
    computed: {
        // На телефоне перетаскивание начинается с задержки, иначе жест отбирает прокрутку страницы
        dragDelay() {
            return this.isMobile ? 120 : 0;
        },
    },
    methods: {
        // Элемент программы — это {module, lessons}, своего id у него нет
        moduleKey(item) {
            return item.module.id;
        },
    },
}
</script>

<template>
    <div>
        <!-- Модули не перетаскиваются между собой (pull/put: false), сортируются только уроки внутри -->
        <draggable
            :group="{ pull: false, put: false }"
            :list="getStudyProgram"
            :item-key="moduleKey"
            handle=".drag-handle"
            :delay="dragDelay"
            :delay-on-touch-only="true"
        >
            <template #item="{element: moduleItem}">
                <div class="section-card">
                    <div class="section-card__header">
                        <v-icon size="18" color="muted" class="drag-handle tw-cursor-grab tw-shrink-0">mdi-drag</v-icon>
                        <div class="tw-min-w-0 tw-flex-1">
                            <div class="bp-overline">Модуль</div>
                            <div class="tw-text-[15px] tw-font-medium tw-truncate">{{ moduleItem.module.name }}</div>
                        </div>
                        <v-menu>
                            <template v-slot:activator="{ props }">
                                <v-btn v-bind="props" variant="text" color="muted" icon="mdi-dots-vertical" size="small" class="tw-shrink-0"/>
                            </template>
                            <v-list density="compact">
                                <v-list-item
                                    prepend-icon="mdi-plus"
                                    title="Создать урок"
                                    @click="$emit('openDialogCreateLesson', moduleItem.module.id)"
                                />
                                <v-list-item
                                    v-if="!!moduleItem.module.id"
                                    prepend-icon="mdi-delete-outline"
                                    title="Удалить модуль"
                                    base-color="error"
                                    @click="$emit('deleteModule', moduleItem.module)"
                                />
                            </v-list>
                        </v-menu>
                    </div>

                    <draggable
                        :group="{ name: moduleItem.module.name, put: true}"
                        :list="moduleItem.lessons"
                        item-key="id"
                        handle=".drag-handle"
                        :delay="dragDelay"
                        :delay-on-touch-only="true"
                    >
                        <template #item="{element: lesson}">
                            <ListRow
                                dense
                                clickable
                                :title="lesson.name"
                                :active="lesson.id === curLessonId"
                                @click="$emit('chooseLesson', lesson)"
                            >
                                <template #lead>
                                    <v-icon size="18" color="muted" class="drag-handle tw-cursor-grab tw-shrink-0" @click.stop>mdi-drag</v-icon>
                                </template>
                                <template #subtitle>
                                    {{ parseInt(lesson.order) }} урок<span v-if="lesson.school_day"> · в расписании</span>
                                </template>
                                <template #actions>
                                    <v-btn
                                        icon="mdi-delete-outline"
                                        variant="text"
                                        color="muted"
                                        size="small"
                                        density="comfortable"
                                        title="Удалить урок"
                                        class="tw-shrink-0"
                                        @click.stop="$emit('deleteLesson', lesson)"
                                    />
                                </template>
                            </ListRow>
                        </template>
                    </draggable>

                    <div v-if="!moduleItem.lessons.length" class="tw-px-3 tw-py-2 tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted">
                        В модуле пока нет уроков
                    </div>
                </div>
            </template>
        </draggable>
    </div>
</template>

<style scoped lang="scss">
</style>
