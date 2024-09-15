<script>
import draggable from 'vuedraggable'

export default {
    name: "ModuleList",
    props: {
        getStudyProgram: {default: []},
    },
    components: {draggable},
    data() {
        return {
            drag: false,
            controlOnStart: true,
        }
    },
    watch: {
        getStudyProgram: {
            handler(val) {
                if (val) {
                    this.$emit('setStudyProgram', val)
                }

            }, deep: true
        }
    },
}
</script>

<template>
    <div>
        <draggable
            :group="{ pull: false, put: false }"
            :list="getStudyProgram"
            item-key=""
        >
            <template #item="{element}">
                <div class="courseList">
                    <div class="course-header">
                        <h2>Модуль {{ element.module.name }} </h2>
                        <v-spacer/>
                        <v-menu>
                            <template v-slot:activator="{ props }">
                                <v-btn v-bind="props" variant="text" icon="mdi-dots-vertical" size="small"/>
                            </template>
                            <v-list>
                                <v-list-item>
                                    <v-list-item-title>
                                        <v-btn
                                            @click="$emit('openDialogCreateLesson',element.module.id)"
                                            text="создать урок"
                                            density="compact"
                                            variant="text"
                                        />
                                    </v-list-item-title>
                                    <v-divider v-if="!!element.module.id"/>
                                    <v-list-item-title v-if="!!element.module.id">
                                        <v-btn
                                            @click=""
                                            text="редактировать модуль"
                                            density="compact"
                                            variant="text"
                                        />
                                    </v-list-item-title>
                                    <v-list-item-title v-if="!!element.module.id">
                                        <v-btn
                                            @click="$emit('deleteModule',element.module)"
                                            text="удалить модуль"
                                            density="compact"
                                            variant="text"
                                            color="red"
                                        />
                                    </v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </v-menu>
                    </div>
                    <div>
                        <draggable
                            :group="{ name: element.module.name, put: true}"
                            :list="element.lessons"
                            item-key=""
                        >
                            <template #item="{element}">
                                <div @click="$emit('chooseLesson',element)"
                                     class="lesson">
                                    <div class="tw-flex tw-justify-between">
                                        <div>{{ element.order }} урок: {{ element.name }}</div>
                                        <div>
                                            <div @click="$emit('deleteLesson',element)">
                                                <v-icon size="small" color="grey">mdi-trash-can-outline</v-icon>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </draggable>
                    </div>
                </div>
            </template>
        </draggable>
    </div>
</template>

<style scoped lang="scss">
.courseList {
    border: 1px solid rgba(128, 128, 128, 0.27);
    border-radius: 4px;
    padding: 5px;
    margin-bottom: 5px;

    &:hover {
        background-color: rgba(187, 189, 193, 0.05);
    }

}

.lesson {
    @apply tw-w-[98%]   tw-m-2 tw-p-2;
    border: 1px solid rgba(128, 128, 128, 0.42);
    border-radius: 4px;

    &:hover {
        background-color: rgba(187, 189, 193, 0.2);
    }

}

.course-header {
    @apply tw-flex tw-justify-between tw-items-center
}

.active-lesson {
    background-color: #d0d0d0;
}
</style>
