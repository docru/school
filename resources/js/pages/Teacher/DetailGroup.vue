<template>
    <div class="teacher-group">
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
            <v-tabs v-model="tab" :grow="isMobile" color="primary">
                <v-tab value="lessons">Уроки</v-tab>
                <v-tab value="group">Группа</v-tab>
            </v-tabs>
            <v-divider/>

            <!-- touch: свайп переключал бы вкладку при обычной прокрутке списка -->
            <v-window v-model="tab" :touch="false">
                <v-window-item value="lessons">
                    <div class="tw-p-3 md:tw-p-4">
                        <div v-if="!days.length" class="tw-p-8 tw-text-center">
                            <v-icon size="40" color="muted">mdi-calendar-blank-outline</v-icon>
                            <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted tw-mt-2">
                                Учебных дней пока нет
                            </div>
                        </div>

                        <div v-for="day in days" :key="day.id" class="day-card">
                            <div class="day-card__header">
                                <div class="tw-min-w-0 tw-flex-1">
                                    <div class="tw-text-sm tw-font-medium">
                                        {{ day.order }}. {{ day.date ? fmtDate(day.date) : 'дата не назначена' }}
                                    </div>
                                    <div class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted">
                                        {{ day.lessons.length }} {{ plural(day.lessons.length, ['урок', 'урока', 'уроков']) }}
                                    </div>
                                </div>
                                <span v-if="day.status === 'open'" class="day-badge day-badge--open">идёт</span>
                                <span v-else-if="day.date" class="day-badge">проведён</span>
                            </div>

                            <div
                                v-for="(lesson, index) in day.lessons"
                                :key="lesson.id"
                                class="lesson-row"
                                @click="openLesson(lesson)"
                            >
                                <span class="lesson-row__num">{{ index + 1 }}</span>
                                <div class="tw-min-w-0 tw-flex-1 tw-text-sm">{{ lesson.name }}</div>
                                <v-icon size="20" color="muted" class="tw-shrink-0">mdi-chevron-right</v-icon>
                            </div>

                            <div v-if="!day.lessons.length" class="tw-px-3 tw-py-2 tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted">
                                Уроков на этот день нет
                            </div>
                        </div>
                    </div>
                </v-window-item>

                <v-window-item value="group">
                    <div class="tw-p-3 md:tw-p-4">
                        <div class="bp-overline tw-mb-2">Учителя</div>
                        <div v-if="getTeachers.length" class="tw-mb-5">
                            <div v-for="teacher in getTeachers" :key="teacher.id" class="person-row">
                                {{ fio(teacher) }}
                            </div>
                        </div>
                        <div v-else class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted tw-mb-5">
                            Учителей нет
                        </div>

                        <div class="bp-overline tw-mb-2">
                            Ученики
                            <span class="tw-ml-1">{{ getDisciples.length || '' }}</span>
                        </div>
                        <div v-if="getDisciples.length">
                            <div v-for="disciple in getDisciples" :key="disciple.id" class="person-row">
                                {{ fio(disciple) }}
                            </div>
                        </div>
                        <div v-else class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted">
                            Учеников нет
                        </div>
                    </div>
                </v-window-item>
            </v-window>
        </v-card>
    </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";

export default {
    name: "DetailGroup",
    data() {
        return {
            tab: 'lessons',
        }
    },
    computed: {
        ...mapGetters('teacher', {
            group: 'getGroup',
            courseSchoolDays: 'getCourseSchoolDay',
            groupSchoolDays: 'getGroupSchoolDay',
            getDisciples: 'getDisciples',
            getTeachers: 'getTeachers',
        }),
        isMobile() {
            return this.$vuetify.display.name === 'sm';
        },
        subtitle() {
            const course = this.group?.course?.name;
            if (!course) return 'курс не указан';
            return `${course} · ${this.getDisciples.length} ${this.plural(this.getDisciples.length, ['ученик', 'ученика', 'учеников'])}`;
        },
        // День курса и назначенный ему день группы — в одном объекте
        days() {
            return (this.courseSchoolDays ?? []).map(day => {
                const groupDay = this.groupSchoolDays?.[day.id] ?? {};
                return {
                    id: day.id,
                    order: day.order,
                    lessons: day.lessons ?? [],
                    date: groupDay.date ?? null,
                    status: groupDay.status ?? null,
                };
            });
        },
    },
    methods: {
        ...mapActions('teacher', ['actRequestGroup']),
        fio(user) {
            return [user.surname, user.name, user.patronymic].filter(Boolean).join(' ') || 'без имени';
        },
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
                name: 'TeacherLesson',
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
.day-card {
    border: 1px solid var(--bp-border);
    border-radius: var(--bp-radius-md, 8px);
    margin-bottom: 8px;
    overflow: hidden;
}

.day-card__header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 10px;
    background: var(--bp-surface-muted);
}

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
}

.lesson-row {
    display: flex;
    align-items: center;
    gap: 8px;
    min-height: 48px;
    padding: 6px 8px 6px 10px;
    border-top: 1px solid var(--bp-border);
    cursor: pointer;

    &:active {
        background: var(--bp-surface-muted);
    }
}

.lesson-row__num {
    flex: 0 0 auto;
    min-width: 20px;
    font-size: 12px;
    color: var(--bp-text-muted);
}

.person-row {
    padding: 8px 2px;
    font-size: 14px;
    border-bottom: 1px solid var(--bp-border);

    &:last-child {
        border-bottom: 0;
    }
}
</style>
