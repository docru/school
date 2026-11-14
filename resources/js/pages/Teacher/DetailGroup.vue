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
                                <span v-if="day.status === 'open'" class="day-badge day-badge--open">идёт</span>
                                <span v-else-if="day.date" class="day-badge">проведён</span>
                            </div>

                            <ListRow
                                v-for="(lesson, index) in day.lessons"
                                :key="lesson.id"
                                dense
                                clickable
                                wrap-title
                                :title="lesson.name"
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
                                    <v-icon size="20" color="muted" class="tw-shrink-0">mdi-chevron-right</v-icon>
                                </template>
                            </ListRow>

                            <div v-if="!day.lessons.length" class="tw-px-3 tw-py-2 tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted">
                                Уроков на этот день нет
                            </div>
                        </div>
                    </div>
                </v-window-item>

                <v-window-item value="group">
                    <div class="tw-p-3 md:tw-p-4">
                        <div class="bp-overline tw-mb-1">Учителя</div>
                        <div v-if="getTeachers.length" class="list-box tw-mb-5">
                            <ListRow v-for="person in getTeachers" :key="person.id" :title="fio(person)">
                                <template #subtitle>
                                    <span v-if="person.phone">{{ fmtPhone(person.phone) }}</span>
                                    <span v-else>телефон не указан</span>
                                </template>
                                <template #actions>
                                    <v-btn
                                        v-if="person.phone"
                                        icon="mdi-phone-outline"
                                        size="small"
                                        density="comfortable"
                                        variant="text"
                                        color="primary"
                                        :href="'tel:+' + person.phone"
                                        title="Позвонить"
                                        class="tw-shrink-0"
                                    />
                                </template>
                            </ListRow>
                        </div>
                        <div v-else class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted tw-mb-5">
                            Учителей нет
                        </div>

                        <div class="bp-overline tw-mb-1">
                            Ученики
                            <span v-if="disciples.length" class="tw-ml-1">{{ activeDisciples.length }}</span>
                            <span v-if="expelledCount" class="tw-ml-1">· отчислено {{ expelledCount }}</span>
                        </div>
                        <div v-if="disciples.length" class="list-box">
                            <ListRow
                                v-for="(person, index) in disciples"
                                :key="person.id"
                                :title="`${index + 1}. ${fio(person)}`"
                                :muted="person.status === 'expelled'"
                                :warning="hasWarning(person)"
                            >
                                <template #subtitle>
                                    <span v-if="person.status === 'expelled'">
                                        отчислен{{ person.expelled_at ? ' ' + fmtDate(person.expelled_at) : '' }}
                                    </span>
                                    <template v-else>
                                        <span>посещено {{ attendedCount(person) }} из {{ closedDaysCount }}</span>
                                        <span class="tw-text-warning tw-font-medium" v-if="hasWarning(person)"> · пропусков {{ missedCount(person) }}</span>
                                    </template>
                                </template>
                                <template #actions>
                                    <v-btn
                                        v-if="person.phone"
                                        icon="mdi-phone-outline"
                                        size="small"
                                        density="comfortable"
                                        variant="text"
                                        color="primary"
                                        :href="'tel:+' + person.phone"
                                        title="Позвонить"
                                        class="tw-shrink-0"
                                    />
                                </template>
                            </ListRow>
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
import ListRow from "../../components/ListRow.vue";

export default {
    name: "DetailGroup",
    components: {ListRow},
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
            const n = this.activeDisciples.length;
            return `${course} · ${n} ${this.plural(n, ['ученик', 'ученика', 'учеников'])}`;
        },
        // Отметки посещения группы: «<id дня>_<id ученика>»
        attendanceSet() {
            return new Set(this.group?.attendanceMarks ?? []);
        },
        closedDaysCount() {
            return Object.values(this.groupSchoolDays ?? {}).filter(d => d.status === 'close').length;
        },
        activeDisciples() {
            return this.getDisciples.filter(d => d.status !== 'expelled');
        },
        expelledCount() {
            return this.getDisciples.length - this.activeDisciples.length;
        },
        // Отчисленные уходят в конец списка, как в разделе «Ученики»
        disciples() {
            const byFio = (a, b) => this.fio(a).localeCompare(this.fio(b), 'ru');
            return [
                ...this.activeDisciples.slice().sort(byFio),
                ...this.getDisciples.filter(d => d.status === 'expelled').sort(byFio),
            ];
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
        // Сколько закрытых занятий посетил ученик
        attendedCount(person) {
            const suffix = '_' + person.id;
            return [...this.attendanceSet].filter(a => a.endsWith(suffix)).length;
        },
        missedCount(person) {
            return Math.max(this.closedDaysCount - this.attendedCount(person), 0);
        },
        // Замечание: активный ученик пропустил больше трёх занятий (порог как в админке)
        hasWarning(person) {
            return person.status !== 'expelled' && this.missedCount(person) > 3;
        },
        fmtPhone(phone) {
            const d = String(phone).replace(/\D/g, '');
            if (d.length === 11) return `+${d[0]} ${d.slice(1, 4)} ${d.slice(4, 7)}-${d.slice(7, 9)}-${d.slice(9)}`;
            return '+' + d;
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

</style>
