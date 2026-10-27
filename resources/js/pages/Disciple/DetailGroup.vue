<template>
    <div class="disciple-group">
        <!-- Шапка: на мобильном в крошках только «Уроки», название группы нужно здесь -->
        <div class="tw-flex tw-items-start tw-justify-between tw-gap-3 tw-mb-3">
            <div class="tw-min-w-0">
                <h1 class="adf-heading tw-text-xl md:tw-text-2xl tw-leading-7 md:tw-leading-8 tw-m-0 tw-truncate">
                    {{ group?.group?.name || 'Группа' }}
                </h1>
                <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted tw-truncate">
                    {{ subtitle }}
                </div>
            </div>
        </div>

        <v-progress-linear v-if="!group?.group" indeterminate color="primary"/>

        <v-card v-else>
            <div class="tw-p-3 md:tw-p-4">
                <div v-if="!days.length" class="tw-p-8 tw-text-center">
                    <v-icon size="40" color="muted">mdi-calendar-blank-outline</v-icon>
                    <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted tw-mt-2">
                        Учебных дней пока нет
                    </div>
                </div>

                <div v-for="day in days" :key="day.id" class="section-card">
                    <div class="section-card__header">
                        <div class="tw-min-w-0 tw-flex-1">
                            <div class="tw-text-sm tw-font-medium">
                                {{ day.order }}. {{ day.date ? fmtDate(day.date) : 'дата не назначена' }}
                            </div>
                            <div class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted">
                                {{ day.lessons.length }} {{ plural(day.lessons.length, ['урок', 'урока', 'уроков']) }}
                            </div>
                        </div>
                        <!-- Посещение показываем только по состоявшимся дням -->
                        <span v-if="day.date && day.attended" class="day-badge day-badge--ok">были</span>
                        <span v-else-if="day.date && day.status === 'close'" class="day-badge day-badge--miss">пропуск</span>
                        <span v-else-if="day.status === 'open'" class="day-badge day-badge--open">идёт</span>
                    </div>

                    <!-- Урок открывается только после занятия -->
                    <ListRow
                        v-for="(lesson, index) in day.lessons"
                        :key="lesson.id"
                        dense
                        wrap-title
                        :title="lesson.name"
                        :muted="day.status !== 'close'"
                        :clickable="day.status === 'close'"
                        @click="openLesson(lesson)"
                    >
                        <template #lead><span class="row-num">{{ index + 1 }}</span></template>
                        <template #actions>
                            <v-icon
                                v-if="lesson.homework_count"
                                size="18"
                                color="primary"
                                class="tw-shrink-0"
                                title="Есть домашнее задание"
                            >mdi-home-edit-outline</v-icon>
                            <v-icon
                                v-if="day.status !== 'close'"
                                size="18"
                                color="muted"
                                class="tw-shrink-0"
                                title="Откроется после занятия"
                            >mdi-lock-outline</v-icon>
                            <v-icon v-else size="20" color="muted" class="tw-shrink-0">mdi-chevron-right</v-icon>
                        </template>
                    </ListRow>

                    <div v-if="!day.lessons.length" class="tw-px-3 tw-py-2 tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted">
                        Уроков на этот день нет
                    </div>
                </div>
            </div>
        </v-card>
    </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import ListRow from "../../components/ListRow.vue";

export default {
    name: "DetailGroup",
    components: {ListRow},
    computed: {
        ...mapGetters('disciple', {
            group: 'getGroup',
            courseSchoolDays: 'getCourseSchoolDay',
            groupSchoolDays: 'getGroupSchoolDay',
            attendances: 'getAttendances',
        }),
        subtitle() {
            const course = this.group?.course?.name;
            if (!course) return 'курс не указан';
            const visited = this.days.filter(d => d.attended).length;
            const closed = this.days.filter(d => d.status === 'close').length;
            return `${course} · посещено ${visited} из ${closed}`;
        },
        // День курса, назначенный ему день группы и отметка посещения — в одном объекте
        days() {
            return (this.courseSchoolDays ?? []).map(day => {
                const groupDay = this.groupSchoolDays?.[day.id] ?? {};
                return {
                    id: day.id,
                    order: day.order,
                    lessons: day.lessons ?? [],
                    date: groupDay.date ?? null,
                    status: groupDay.status ?? null,
                    attended: groupDay.id ? this.attendances.includes(groupDay.id) : false,
                };
            });
        },
    },
    methods: {
        ...mapActions('disciple', ['actRequestGroup']),
        fmtDate(value) {
            if (!value) return '—';
            const [y, m, d] = String(value).slice(0, 10).split('-');
            return `${d}.${m}.${y}`;
        },
        plural(n, forms) {
            const abs = Math.abs(n) % 100;
            const last = abs % 10;
            if (abs > 10 && abs < 20) return forms[2];
            if (last > 1 && last < 5) return forms[1];
            if (last === 1) return forms[0];
            return forms[2];
        },
        openLesson(lesson) {
            this.$router.push({
                name: 'DiscipleLesson',
                params: {groupId: this.group.group.id, lessonId: lesson.id},
            });
        },
    },
    created() {
        this.actRequestGroup(this.$route.params.id);
    }
}
</script>

<style scoped lang="scss">
.day-badge {
    flex: 0 0 auto;
    font-size: 12px;
    line-height: 16px;
    padding: 0 8px;
    border-radius: 999px;
    background: var(--bp-surface-card);
    color: var(--bp-text-muted);

    &--open {
        color: var(--bp-accent);
    }

    &--ok {
        color: var(--bp-success);
    }

    &--miss {
        color: var(--bp-warning);
    }
}
</style>
