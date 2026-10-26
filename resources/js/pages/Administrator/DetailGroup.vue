<template>
    <!-- На мобильном статус и курс вынесены из карточки и прилипают под хлебными крошками при прокрутке -->
    <div class="group-sticky tw-flex tw-items-center tw-gap-2 tw-min-w-0 tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted" v-if="isMobile">
        <v-chip size="small" label :color="statusColor" class="tw-shrink-0">{{ statusTitle }}</v-chip>
        <span class="tw-truncate" v-if="getCourse.name">Курс «{{ getCourse.name }}»</span>
    </div>

    <!-- Сводка по группе -->
    <v-card class="group-section tw-mb-4">
        <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-start md:tw-justify-between tw-gap-3">
            <!-- Название только на десктопе: на мобильном оно уже выведено в хлебных крошках -->
            <div class="tw-min-w-0" v-if="!isMobile">
                <div class="tw-flex tw-flex-wrap tw-items-center tw-gap-2">
                    <h1 class="adf-heading tw-text-2xl tw-leading-8 tw-m-0 tw-break-words">{{ getGroup.name }}</h1>
                    <v-chip size="small" label :color="statusColor">{{ statusTitle }}</v-chip>
                </div>
                <div class="tw-flex tw-items-center tw-gap-2 tw-min-w-0 tw-mt-1 tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted">
                    <span class="tw-truncate" v-if="getCourse.name">Курс «{{ getCourse.name }}»</span>
                </div>
            </div>

            <div class="tw-flex tw-flex-col md:tw-flex-row tw-gap-2 tw-shrink-0">
                <v-btn
                    color="primary"
                    prepend-icon="mdi-calendar-check-outline"
                    @click="closeSchoolDay()"
                    v-if="existsOpenDay"
                >
                    Закрыть день
                </v-btn>
                <v-btn
                    color="primary"
                    prepend-icon="mdi-calendar-plus-outline"
                    @click="dialogDateAdd = true"
                    v-else-if="!finishedCourse"
                >
                    Добавить день
                </v-btn>
                <template v-else>
                    <v-btn
                        variant="outlined"
                        prepend-icon="mdi-archive-arrow-down-outline"
                        @click="toArchive()"
                        v-if="getGroup.status !== 'archived'"
                    >
                        Отправить в архив
                    </v-btn>
                    <v-btn
                        variant="outlined"
                        prepend-icon="mdi-archive-arrow-up-outline"
                        @click="fromArchive()"
                        v-else
                    >
                        Вернуть из архива
                    </v-btn>
                </template>
            </div>
        </div>

        <!-- Ключевые показатели: на мобильном компактно, как в карточке списка -->
        <div class="tw-mt-4 tw-flex tw-flex-col tw-gap-3" v-if="isMobile">
            <div>
                <div class="tw-flex tw-justify-between tw-items-baseline tw-mb-1">
                    <span class="bp-overline">Занятия</span>
                    <span class="tw-text-sm">
                        <span class="tw-font-medium">{{ closedDaysCount }}</span>
                        <span class="tw-text-text-muted dark:tw-text-dark-text-muted"> из {{ totalDaysCount }}</span>
                    </span>
                </div>
                <v-progress-linear :model-value="progress" height="4" rounded color="primary" bg-color="border" bg-opacity="1"/>
            </div>
            <div class="tw-grid tw-grid-cols-3 tw-gap-3">
                <div>
                    <div class="bp-overline">Ученики</div>
                    <div class="tw-text-sm tw-mt-0.5">
                        <span class="tw-font-medium">{{ activeDisciplesCount }}</span>
                        <span class="tw-text-text-muted dark:tw-text-dark-text-muted" v-if="expelledDisciplesCount">
                            · отчисл. {{ expelledDisciplesCount }}
                        </span>
                    </div>
                </div>
                <div>
                    <div class="bp-overline">Старт</div>
                    <div class="tw-text-sm tw-mt-0.5">{{ firstDayDate }}</div>
                </div>
                <div>
                    <div class="bp-overline">{{ existsOpenDay ? 'Текущее' : 'Последнее' }}</div>
                    <div class="tw-text-sm tw-mt-0.5">{{ lastDayDate }}</div>
                </div>
            </div>
        </div>
        <div class="tw-grid tw-grid-cols-4 tw-gap-3 tw-mt-4" v-else>
            <div class="group-stat">
                <div class="bp-overline">Ученики</div>
                <div class="group-stat__value">
                    {{ activeDisciplesCount }}
                    <span class="group-stat__note" v-if="expelledDisciplesCount">· отчисл. {{ expelledDisciplesCount }}</span>
                </div>
            </div>
            <div class="group-stat">
                <div class="bp-overline">Занятия</div>
                <div class="group-stat__value">
                    {{ closedDaysCount }} <span class="group-stat__note">из {{ totalDaysCount }}</span>
                </div>
                <v-progress-linear
                    :model-value="progress"
                    height="4"
                    rounded
                    color="primary"
                    bg-color="border"
                    bg-opacity="1"
                    class="tw-mt-2"
                />
            </div>
            <div class="group-stat">
                <div class="bp-overline">Старт</div>
                <div class="group-stat__value">{{ firstDayDate }}</div>
            </div>
            <div class="group-stat">
                <div class="bp-overline">{{ existsOpenDay ? 'Текущее' : 'Последнее' }}</div>
                <div class="group-stat__value">{{ lastDayDate }}</div>
            </div>
        </div>

        <!-- Учителя -->
        <div class="tw-mt-4 tw-pt-4 tw-border-t tw-border-border dark:tw-border-dark-border">
            <div class="tw-flex tw-items-center tw-justify-between tw-gap-2 tw-mb-2 tw-min-h-[28px]">
                <div class="bp-overline">Учителя</div>
                <v-btn
                    icon="mdi-account-plus-outline"
                    size="small"
                    density="comfortable"
                    variant="text"
                    color="primary"
                    title="Добавить учителя"
                    class="tw--my-1 tw--mr-1"
                    @click="openDialog('dialogTeacher')"
                    v-if="!finishedCourse"
                />
            </div>
            <!-- Телефон учителя не показываем: вместо него кнопка-трубка со ссылкой tel: -->
            <div class="tw-flex tw-flex-wrap tw-gap-2" v-if="Object.keys(getTeachers).length">
                <div class="teacher-pill" v-for="teacher in getTeachers" :key="teacher.id">
                    <span class="tw-truncate">{{ [teacher.surname, teacher.name].join(' ') }}</span>
                    <div class="tw-flex tw-items-center tw-shrink-0">
                        <v-btn
                            v-if="teacher.phone"
                            icon="mdi-phone-outline"
                            size="small"
                            density="comfortable"
                            variant="text"
                            color="primary"
                            :href="'tel:+' + teacher.phone"
                            :title="'Позвонить +' + teacher.phone"
                        />
                        <v-btn
                            v-if="!finishedCourse"
                            icon="mdi-close"
                            size="small"
                            density="comfortable"
                            variant="text"
                            color="muted"
                            title="Убрать из группы"
                            @click="removeTeacher(teacher.id)"
                        />
                    </div>
                </div>
            </div>
            <div class="tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted" v-else>
                В группе пока нет учителей
            </div>
        </div>
    </v-card>

    <!-- Ученики и посещаемость -->
    <v-card class="group-section group-section--table">
        <div class="tw-flex tw-items-center tw-justify-between tw-gap-2 tw-mb-2 tw-min-h-[28px]">
            <h2 class="adf-heading tw-text-xl tw-leading-7 tw-m-0">
                Ученики
                <span class="tw-text-text-muted dark:tw-text-dark-text-muted">{{ activeDisciplesCount }}</span>
                <span class="tw-font-sans tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted tw-ml-1" v-if="expelledDisciplesCount">
                    + {{ expelledDisciplesCount }} отчисл.
                </span>
            </h2>
            <v-btn
                icon="mdi-account-plus-outline"
                size="small"
                density="comfortable"
                variant="text"
                color="primary"
                title="Добавить ученика"
                class="tw--my-1 tw--mr-1"
                @click="openDialog('dialogDisciple')"
                v-if="!finishedCourse"
            />
        </div>
        <div class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted tw-mb-3" v-if="$vuetify.display.name !== 'sm'">
            Отметки о посещении ставятся в открытом дне. Нажмите на номер занятия, чтобы изменить его дату.
        </div>

        <v-data-table
            :items-per-page="-1"
            class="table-sh"
            density="compact"
            :items="disciples"
            :headers="headers"
            :row-props="rowProps"
        >
            <template v-slot:bottom></template>
            <template v-slot:item="{index, item}" v-if="isMobile">
                <!-- На мобильном ученик — строка списка, подробности открываются в отдельном окне снизу -->
                <div
                    class="disciple-item"
                    :class="{
                        'disciple-item--expelled': item.status === 'expelled',
                        'disciple-item--warning': hasWarning(item),
                    }"
                    @click="openDisciple(item)"
                >
                    <div class="tw-min-w-0 tw-flex-1">
                        <div class="tw-text-[15px] tw-font-medium tw-truncate">{{ index + 1 }}. {{ item.name }}</div>
                        <div class="tw-text-xs tw-mt-0.5">
                            <span class="tw-text-text-muted dark:tw-text-dark-text-muted" v-if="item.status === 'expelled'">
                                отчислен{{ item.expelled_at ? ' ' + fmtDate(item.expelled_at) : '' }}
                            </span>
                            <template v-else>
                                <span>посещено {{ attendedCount(item) }} из {{ closedDaysCount }}</span>
                                <span class="tw-text-warning tw-font-medium" v-if="hasWarning(item)"> · пропусков {{ missedCount(item) }}</span>
                            </template>
                        </div>
                    </div>
                    <v-icon color="muted" size="small">mdi-chevron-right</v-icon>
                </div>
            </template>

            <template v-slot:headers v-if="$vuetify.display.name === 'sm'"></template>

            <template v-slot:[`header.name`] v-if="$vuetify.display.name !== 'sm'">
                <span class="tw-pl-2">Ученик</span>
            </template>

            <template v-slot:[`header.day${day?.id}`]="{column}" v-for="(day,index) in headersNotEmpty">
                <div
                    :class="{ activeSlot:groupsSchoolDays[day.id]?.status == 'open' }"
                    @click="openDataEditDialog(day.id)"
                >
                    {{ column.title }} <br>
                    <v-tooltip location="top">
                        <template v-slot:activator="{ props }">
                            <v-icon v-bind="props" color="muted" size="small">mdi-book-open-outline</v-icon>
                        </template>
                        <span v-html="lessons(day.lessons)"></span>
                    </v-tooltip>
                    <div
                        v-if="groupsSchoolDays[day.id]?.date"
                        class="tw-text-[11px] tw-font-sans tw-font-normal"
                        :class="groupsSchoolDays[day.id].status === 'open' ? 'tw-text-accent dark:tw-text-dark-accent' : 'tw-text-text-muted dark:tw-text-dark-text-muted'"
                    >
                        {{ groupsSchoolDays[day.id].date }}
                    </div>
                    <div v-else class="tw-text-[11px] tw-text-text-muted dark:tw-text-dark-text-muted">—</div>
                </div>
            </template>

            <template
                v-slot:[`item.day${day?.id}`]="{item}"
                v-for="(day,index) in headersNotEmpty"
            >
                <template v-if="(gsd=groupsSchoolDays[day.id])">
                    <div
                        :class="{ activeSlot:gsd?.status === 'open' }"
                        v-if="!expelledDay(item, day)"
                        @click="changeOld({groupSchoolDayId: groupsSchoolDays[day.id].id, userId: item.id})"
                    >
                        <v-checkbox
                            color="primary"
                            hide-details
                            density="compact"
                            style="text-align: center"
                            v-model="attendance"
                            :disabled="gsd.status !== 'open' || item.status == 'expelled'"
                            :value="gsd.id + '_' + item.id"
                            @change="actSetAttendance({groupSchoolDayId: groupsSchoolDays[day.id].id, userId: item.id})"
                        />
                    </div>
                    <div v-else>-</div>
                </template>
            </template>

            <template v-slot:item.name="{item, index}">
                <div class="tw-flex tw-items-center tw-justify-between tw-gap-2 tw-pl-2 tw-pr-1 tw-py-1 tw-text-left">
                    <div class="tw-min-w-0">
                        <div>{{ index + 1 }}. {{ item.name }}</div>
                        <div class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted" v-if="item.status === 'expelled'">
                            отчислен{{ item.expelled_at ? ' ' + fmtDate(item.expelled_at) : '' }}
                        </div>
                        <div class="tw-text-xs tw-text-warning tw-font-medium" v-else-if="hasWarning(item)">
                            <v-icon size="14" class="tw-align-text-bottom">mdi-alert-outline</v-icon>
                            пропусков {{ missedCount(item) }}
                        </div>
                        <Code v-if="item.phone" :code="'+' + item.phone" density="compact" class="tw-w-max"/>
                    </div>
                    <v-btn
                        v-if="!finishedCourse && item.status !== 'expelled'"
                        icon="mdi-account-remove-outline"
                        size="small"
                        density="comfortable"
                        variant="text"
                        color="error"
                        title="Отчислить"
                        class="tw-shrink-0"
                        @click="removeDisciple(item.id)"
                    />
                    <v-btn
                        v-else-if="!finishedCourse"
                        icon="mdi-account-reactivate-outline"
                        size="small"
                        density="comfortable"
                        variant="text"
                        color="success"
                        title="Вернуть в группу"
                        class="tw-shrink-0"
                        @click="restoreDisciple(item.id)"
                    />
                </div>
            </template>
        </v-data-table>
    </v-card>
    <!-- Карточка ученика на мобильном: телефон, действия и отметки по занятиям -->
    <v-bottom-sheet v-model="sheetDisciple">
        <!-- Окно можно закрыть свайпом вниз: тянется за пальцем, после 80px закрывается -->
        <v-card
            class="disciple-sheet"
            v-if="selectedDisciple"
            data-swipe-close="sheetDisciple"
            :style="sheetStyle"
            @touchstart.passive="sheetTouchStart"
            @touchmove="sheetTouchMove"
            @touchend="sheetTouchEnd"
            @touchcancel="sheetTouchEnd"
        >
            <div class="sheet-handle"></div>
            <div class="tw-flex tw-items-start tw-justify-between tw-gap-2 tw-px-4 tw-pt-1 tw-pb-2">
                <div class="tw-min-w-0">
                    <div class="adf-heading tw-text-xl tw-leading-7 tw-truncate">{{ selectedDisciple.name }}</div>
                    <div class="tw-text-xs tw-mt-0.5">
                        <span class="tw-text-text-muted dark:tw-text-dark-text-muted" v-if="selectedDisciple.status === 'expelled'">
                            отчислен{{ selectedDisciple.expelled_at ? ' ' + fmtDate(selectedDisciple.expelled_at) : '' }}
                        </span>
                        <template v-else>
                            <span>посещено {{ attendedCount(selectedDisciple) }} из {{ closedDaysCount }}</span>
                            <span class="tw-text-warning tw-font-medium" v-if="hasWarning(selectedDisciple)"> · пропусков {{ missedCount(selectedDisciple) }}</span>
                        </template>
                    </div>
                </div>
                <v-btn
                    icon="mdi-close"
                    size="small"
                    density="comfortable"
                    variant="text"
                    color="muted"
                    class="tw-shrink-0 tw--mr-1"
                    @click="sheetDisciple = false"
                />
            </div>

            <div class="tw-flex tw-items-center tw-justify-between tw-gap-2 tw-px-4 tw-pb-2">
                <Code v-if="selectedDisciple.phone" :code="'+' + selectedDisciple.phone" density="compact" class="tw-flex-1 tw-min-w-0"/>
                <span v-else class="tw-flex-1 tw-text-sm tw-text-text-muted dark:tw-text-dark-text-muted">телефон не указан</span>
                <div class="tw-flex tw-items-center tw-shrink-0">
                    <v-btn
                        v-if="selectedDisciple.phone"
                        icon="mdi-phone-outline"
                        size="small"
                        density="comfortable"
                        variant="text"
                        color="primary"
                        :href="'tel:+' + selectedDisciple.phone"
                        :title="'Позвонить +' + selectedDisciple.phone"
                    />
                    <v-btn
                        v-if="!finishedCourse && selectedDisciple.status !== 'expelled'"
                        icon="mdi-account-remove-outline"
                        size="small"
                        density="comfortable"
                        variant="text"
                        color="error"
                        title="Отчислить"
                        @click="removeDisciple(selectedDisciple.id)"
                    />
                    <v-btn
                        v-else-if="!finishedCourse"
                        icon="mdi-account-reactivate-outline"
                        size="small"
                        density="comfortable"
                        variant="text"
                        color="success"
                        title="Вернуть в группу"
                        @click="restoreDisciple(selectedDisciple.id)"
                    />
                </div>
            </div>

            <!-- Занятия: дата, темы уроков и цветной чекбокс посещения -->
            <div class="disciple-sheet__days swipe-scroll tw-px-4 tw-pb-4">
                <div
                    v-for="day in conductedDays"
                    :key="day.id"
                    class="day-row"
                    :class="{'day-row--open': groupsSchoolDays[day.id]?.status === 'open'}"
                >
                    <v-menu location="top" :disabled="!day.lessons?.length">
                        <template v-slot:activator="{ props }">
                            <div class="tw-flex tw-items-center tw-gap-2 tw-min-w-0 tw-flex-1" v-bind="props">
                                <span class="day-row__num">{{ day.title }}</span>
                                <div class="tw-min-w-0">
                                    <div class="tw-text-sm">
                                        {{ groupsSchoolDays[day.id].date }}
                                        <span class="tw-text-xs tw-text-accent dark:tw-text-dark-accent tw-ml-1" v-if="groupsSchoolDays[day.id].status === 'open'">открыт</span>
                                    </div>
                                    <div class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted tw-truncate" v-if="day.lessons?.length">
                                        {{ day.lessons.join(', ') }}
                                    </div>
                                </div>
                            </div>
                        </template>
                        <v-card class="tw-p-3 tw-text-sm tw-max-w-[300px]">
                            <span v-html="lessons(day.lessons)"></span>
                        </v-card>
                    </v-menu>

                    <!-- Чекбокс: зелёный — был, янтарный — пропуск; у прошедших дней меняется через подтверждение -->
                    <div class="tw-shrink-0 tw-w-10 tw-text-center" v-if="(gsd=groupsSchoolDays[day.id])">
                        <span v-if="expelledDay(selectedDisciple, day)" class="tw-text-text-muted dark:tw-text-dark-text-muted tw-leading-10">—</span>
                        <v-checkbox
                            v-else-if="gsd.status === 'open'"
                            color="primary"
                            hide-details
                            density="compact"
                            class="day-check"
                            v-model="attendance"
                            :disabled="selectedDisciple.status == 'expelled'"
                            :value="gsd.id + '_' + selectedDisciple.id"
                            @change="actSetAttendance({groupSchoolDayId: gsd.id, userId: selectedDisciple.id})"
                        />
                        <div v-else @click="selectedDisciple.status == 'expelled' || changeOld({groupSchoolDayId: gsd.id, userId: selectedDisciple.id})">
                            <v-checkbox
                                hide-details
                                density="compact"
                                readonly
                                class="day-check"
                                :class="attended(gsd, selectedDisciple) ? 'day-check--yes' : 'day-check--no'"
                                :model-value="attended(gsd, selectedDisciple)"
                                :disabled="selectedDisciple.status == 'expelled'"
                            />
                        </div>
                    </div>
                </div>
                <div class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted tw-mt-2" v-if="remainingDaysCount">
                    Впереди ещё {{ remainingDaysCount }} {{ plural(remainingDaysCount, ['занятие', 'занятия', 'занятий']) }}
                </div>
                <div class="tw-text-xs tw-text-text-muted dark:tw-text-dark-text-muted" v-if="!conductedDays.length">
                    Занятий ещё не было
                </div>
            </div>
        </v-card>
    </v-bottom-sheet>

    <v-dialog v-model="dialogDisciple" :fullscreen="isMobile" scrollable>
        <v-card
            class="choose-dialog"
            :class="{'choose-dialog--full': isMobile}"
            data-swipe-close="dialogDisciple"
            :style="isMobile ? sheetStyle : null"
            @touchstart.passive="sheetTouchStart"
            @touchmove="sheetTouchMove"
            @touchend="sheetTouchEnd"
            @touchcancel="sheetTouchEnd"
        >
            <div class="sheet-handle" v-if="isMobile"></div>
            <div class="tw-flex tw-items-center tw-justify-between tw-gap-2 tw-px-4 tw-pt-1 tw-pb-2 tw-shrink-0">
                <div class="adf-heading tw-text-xl tw-leading-7">Добавить ученика</div>
                <v-btn icon="mdi-close" size="small" density="comfortable" variant="text" color="muted" class="tw--mr-1" @click="dialogDisciple = false"/>
            </div>
            <v-card-text class="swipe-scroll tw-px-4 tw-pt-0 tw-pb-4">
                <UserChoose @choosedUser="dialogDisciple = false; loadUsers()" :group="getGroup"/>
            </v-card-text>
        </v-card>
    </v-dialog>
    <v-dialog v-model="dialogTeacher" :fullscreen="isMobile" scrollable>
        <v-card
            class="choose-dialog"
            :class="{'choose-dialog--full': isMobile}"
            data-swipe-close="dialogTeacher"
            :style="isMobile ? sheetStyle : null"
            @touchstart.passive="sheetTouchStart"
            @touchmove="sheetTouchMove"
            @touchend="sheetTouchEnd"
            @touchcancel="sheetTouchEnd"
        >
            <div class="sheet-handle" v-if="isMobile"></div>
            <div class="tw-flex tw-items-center tw-justify-between tw-gap-2 tw-px-4 tw-pt-1 tw-pb-2 tw-shrink-0">
                <div class="adf-heading tw-text-xl tw-leading-7">Добавить учителя</div>
                <v-btn icon="mdi-close" size="small" density="comfortable" variant="text" color="muted" class="tw--mr-1" @click="dialogTeacher = false"/>
            </div>
            <v-card-text class="swipe-scroll tw-px-4 tw-pt-0 tw-pb-4">
                <UserChoose @choosedUser="dialogTeacher = false; loadUsers()" role="teacher" :group="getGroup"/>
            </v-card-text>
        </v-card>
    </v-dialog>

    <v-dialog v-model="dialogDateAdd" width="400">
        <v-card>
            <v-card-title class="tw-pb-0">
                <div class="tw-w-full tw-flex tw-justify-between ">
                    <h6>Добавить учебный день</h6>
                    <v-icon @click="dialogDateAdd=false">mdi-close</v-icon>
                </div>
                <v-card-text class="tw-py-0">
                    <v-date-picker
                        v-model="newDate"
                        show-adjacent-months
                        :weekdays="[1, 2, 3, 4, 5, 6, 0]"
                        weeks-in-month="dynamic"
                        title=""
                        hide-header
                        locale="ru"
                    />
                </v-card-text>
            </v-card-title>
            <v-card-actions>
                <v-btn
                    text="Создать"
                    @click="addSchoolDay"
                />
            </v-card-actions>
        </v-card>
    </v-dialog>

    <v-dialog v-model="dialogDateEdit" width="400">
        <v-card>
            <v-card-title class="tw-pb-0">
                <div class="tw-w-full tw-flex tw-justify-between ">
                    <h6>Изменить учебный день</h6>
                    <v-icon @click="dialogDateEdit=false">mdi-close</v-icon>
                </div>
                <v-card-text class="tw-py-0">
                    <v-date-picker
                        v-model="editDate"
                        show-adjacent-months
                        :weekdays="[1, 2, 3, 4, 5, 6, 0]"
                        weeks-in-month="dynamic"
                        title=""
                        hide-header
                        locale="ru"
                    />
                </v-card-text>
            </v-card-title>
            <v-card-actions>
                <v-btn
                    text="Сохранить"
                    @click="editSchoolDay"
                />
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import UserChoose from './UserChoose.vue';
import Code from "../../components/Code.vue";

export default {
    name: "DetailGroup",
    components: {Code, UserChoose,},
    data() {
        return {
            dialogDisciple: false,
            dialogTeacher: false,
            dialogDateAdd: false,
            newDate: new Date(),
            dialogDateEdit: false,
            editDay: null,
            sheetDisciple: false,
            selectedDiscipleId: null,
            // Состояние свайпа по окну ученика
            sheetDrag: {startY: null, dy: 0, close: null},
        }
    },
    computed: {
        ...mapGetters('administrator', [
            'getGroup',
            'getDisciples',
            'getTeachers',
            'getAttendances',
            'getCourse',
            'getLessons',
            'getStudyProgram',
            'getSchedule'
        ]),
        headers() {
            let headers = [{
                title: 'Имя',
                align: 'start',
                key: 'name',
                width: "300px",
                fixed: true,
                sortable: false,
                divider: true
            }];
            for (const dayId in this.getSchedule) {
                let day = this.getSchedule[dayId];
                headers.push({
                    title: day.order,
                    align: 'center',
                    key: 'day' + day.id,
                    id: day.id,
                    width: "36px",
                    minWidth: "36px",
                    sortable: false
                })
            }
            headers.push({title: '', key: '', sortable: false});
            return headers;
        },
        groupId() {
            return this.$route.params.id;
        },
        headersNotEmpty() {
            return this.headers.map((val) => {
                if (val.key !== 'name' && val.key !== '') {
                    val.lessons = this.lessonsDays[val.id]
                    return val;
                }
            });
        },
        lessonsDays() {
            let res = {};
            for (const l in this.getLessons) {
                let lesson = this.getLessons[l];
                let day = lesson.school_day;
                if (!!day) {
                    if (!res[day]) {
                        res[day] = [];
                    }
                    res[day].push(lesson.name);
                }
            }
            return res;
        },
        disciples() {
            let disciples = [];
            for (const d in this.getDisciples) {
                let disciple = this.getDisciples[d];
                let name = (disciple.surname ?? '') + ' ' + (disciple.name ?? '');
                disciples.push({
                    id: disciple.id,
                    name,
                    phone: disciple.phone,
                    status: disciple.status,
                    expelled_at: disciple.expelled_at,
                });
            }
            // Порядок: сначала с замечанием по пропускам, затем остальные активные, отчисленные в конце.
            // Внутри групп исходный порядок сохраняется (sort стабильный)
            const rank = d => d.status === 'expelled' ? 2 : (this.hasWarning(d) ? 0 : 1);
            return disciples.sort((a, b) => rank(a) - rank(b));
        },
        groupsSchoolDays() {
            return this.getGroup.groups_school_day ?? {};
        },
        editDate: {
            get() {
                return new Date(this.groupsSchoolDays[this.editDay].dateOrigin);
            },
            set(val) {
                this.groupsSchoolDays[this.editDay].dateOrigin = val;
            },
        },
        existsOpenDay() {
            let res = false;
            for (const k in this.groupsSchoolDays) {
                if (this.groupsSchoolDays[k].status === 'open') {
                    res = true;
                    break;
                }
            }
            return res;
        },
        finishedCourse() {
            let cnt = 0;
            for (const k in this.groupsSchoolDays) {
                if (this.groupsSchoolDays[k].status === 'open') {
                    break;
                }
                cnt++;
            }
            return cnt >= Object.keys(this.getSchedule).length;
        },
        attendance: {
            get() {
                return this.getAttendances;
            },
            set() {
            },
        },
        // Мобильная ширина по порогам Vuetify (< 768px)
        isMobile() {
            return this.$vuetify.display.name === 'sm';
        },
        statusTitle() {
            return this.getGroup.status === 'archived' ? 'В архиве' : 'Идёт';
        },
        statusColor() {
            return this.getGroup.status === 'archived' ? 'muted' : 'success';
        },
        activeDisciplesCount() {
            return this.disciples.filter(d => d.status !== 'expelled').length;
        },
        expelledDisciplesCount() {
            return this.disciples.filter(d => d.status === 'expelled').length;
        },
        // Учебные дни группы, отсортированные по дате
        groupDaysSorted() {
            return Object.values(this.groupsSchoolDays)
                .slice()
                .sort((a, b) => String(a.dateOrigin).localeCompare(String(b.dateOrigin)));
        },
        closedDaysCount() {
            return this.groupDaysSorted.filter(d => d.status === 'close').length;
        },
        totalDaysCount() {
            return Object.keys(this.getSchedule).length;
        },
        remainingDaysCount() {
            return Math.max(this.totalDaysCount - this.groupDaysSorted.length, 0);
        },
        progress() {
            if (!this.totalDaysCount) return 0;
            return Math.round(this.closedDaysCount / this.totalDaysCount * 100);
        },
        firstDayDate() {
            return this.fmtDate(this.groupDaysSorted[0]?.dateOrigin);
        },
        lastDayDate() {
            const open = this.groupDaysSorted.find(d => d.status === 'open');
            const last = open ?? this.groupDaysSorted[this.groupDaysSorted.length - 1];
            return this.fmtDate(last?.dateOrigin);
        },
        // Ученик, открытый во всплывающем окне (берём из списка, чтобы статус и отметки обновлялись)
        selectedDisciple() {
            return this.disciples.find(d => d.id === this.selectedDiscipleId) ?? null;
        },
        // Отметки посещения как множество «gsdId_userId» (в ответе API бывают дубли)
        attendanceSet() {
            return new Set(Object.values(this.attendance).map(String));
        },
        sheetStyle() {
            const dragging = this.sheetDrag.startY !== null;
            return {
                transform: this.sheetDrag.dy ? `translateY(${this.sheetDrag.dy}px)` : '',
                transition: dragging ? 'none' : 'transform .2s',
            };
        },
        // Дни курса, по которым у группы уже назначена дата (для мобильной раскладки)
        conductedDays() {
            return this.headersNotEmpty.filter(day => day && this.groupsSchoolDays[day.id]);
        },
    },
    methods: {
        ...mapActions('administrator', [
            'actRequestGroup',
            'actAddGroupSchoolDay',
            'actChangeGroupSchoolDay',
            'actCloseGroupSchoolDay',
            'actRequestGroupUsers',
            'actRemoveUserFromGroup',
            'actRestoreUserToGroup',
            'actRequestAttendances',
            'actSetAttendance',
            'actReqwestCourse',
            'actSetGroupStatus',
        ]),
        changeOld(data) {
            if (confirm('Изменить посещение за прошедшую дату?')) {
                this.actSetAttendance(data);
            }
        },
        async addSchoolDay() {
            this.dialogDateAdd = false;
            await this.actAddGroupSchoolDay({groupId: this.getGroup.id, date: this.newDate});
            this.newDate = new Date();
        },
        async closeSchoolDay() {
            await this.actCloseGroupSchoolDay({groupId: this.getGroup.id});
        },
        openDataEditDialog(id) {
            if (this.groupsSchoolDays[id]) {
                this.editDay = id;
                this.dialogDateEdit = true;
            }
        },
        async editSchoolDay() {
            this.dialogDateEdit = false;
            let gsd = this.groupsSchoolDays[this.editDay];
            await this.actChangeGroupSchoolDay({
                groupId: this.getGroup.id,
                dayId: gsd.id,
                date: gsd.dateOrigin
            });
        },
        // Дата из 'Y-m-d' в 'd.m.Y'
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
        // Был ли ученик на занятии
        attended(gsd, item) {
            return this.attendanceSet.has(gsd.id + '_' + item.id);
        },
        // Сколько занятий посетил ученик
        attendedCount(item) {
            const suffix = '_' + item.id;
            return [...this.attendanceSet].filter(a => a.endsWith(suffix)).length;
        },
        // Сколько закрытых занятий ученик пропустил (дни после отчисления не считаются)
        missedCount(item) {
            return this.conductedDays.filter(day => {
                const gsd = this.groupsSchoolDays[day.id];
                return gsd.status === 'close'
                    && !this.expelledDay(item, day)
                    && !this.attended(gsd, item);
            }).length;
        },
        // Замечание: активный ученик пропустил больше трёх занятий
        hasWarning(item) {
            return item.status !== 'expelled' && this.missedCount(item) > 3;
        },
        openDialog(name) {
            this.sheetDrag = {startY: null, dy: 0, close: null};
            this[name] = true;
        },
        openDisciple(item) {
            this.selectedDiscipleId = item.id;
            this.sheetDrag = {startY: null, dy: 0, close: null};
            this.sheetDisciple = true;
        },
        // Свайп вниз закрывает любое окно с data-swipe-close="<имя флага>" (окно ученика, диалоги добавления)
        sheetTouchStart(e) {
            const scroller = e.currentTarget.querySelector('.swipe-scroll');
            // Жест внутри прокрученной области — это скролл, а не закрытие
            if (!this.isMobile || (scroller && scroller.contains(e.target) && scroller.scrollTop > 0)) {
                this.sheetDrag.startY = null;
                return;
            }
            this.sheetDrag.close = e.currentTarget.dataset.swipeClose;
            this.sheetDrag.startY = e.touches[0].clientY;
            this.sheetDrag.dy = 0;
        },
        sheetTouchMove(e) {
            if (this.sheetDrag.startY === null) return;
            const dy = e.touches[0].clientY - this.sheetDrag.startY;
            if (dy <= 0) {
                this.sheetDrag.dy = 0;
                return;
            }
            e.preventDefault(); // пока тянем окно вниз, список под пальцем не прокручиваем
            this.sheetDrag.dy = dy;
        },
        sheetTouchEnd() {
            if (this.sheetDrag.startY === null) return;
            const close = this.sheetDrag.dy > 80;
            this.sheetDrag.startY = null;
            // При закрытии смещение не сбрасываем: окно уезжает вниз анимацией Vuetify с текущего места
            if (close) {
                this[this.sheetDrag.close] = false;
            } else {
                this.sheetDrag.dy = 0;
            }
        },
        rowProps({item}) {
            return {
                class: {
                    'disciple-row--expelled': item.status === 'expelled',
                    'disciple-row--warning': this.hasWarning(item),
                },
            };
        },
        lessons(lessons) {
            let res = [];
            for (const i in lessons) {
                res.push((1 + parseInt(i)) + '. ' + lessons[i])
            }
            return res.join('<br/>')
        },
        loadUsers() {
            this.actRequestGroupUsers({groupId: this.groupId});
        },
        removeTeacher(id) {
            if (confirm('Удалить учителя?')) {
                this.actRemoveUserFromGroup({groupId: this.groupId, userId: id});
            }
        },
        removeDisciple(id) {
            if (confirm('Отчислить ученика?')) {
                this.actRemoveUserFromGroup({groupId: this.groupId, userId: id});
            }
        },
        restoreDisciple(id) {
            if (confirm('Восстановить ученика?')) {
                this.actRestoreUserToGroup({groupId: this.groupId, userId: id});
            }
        },
        expelledDay(user, day) {
            if (user.status != 'expelled') { // не отчислен
                return false;
            }
            let expelled_at = Date.parse(user.expelled_at);
            let gsdTm = Date.parse(this.groupsSchoolDays[day.id].dateOrigin);
            return gsdTm > expelled_at;
        },
        toArchive() {
            this.actSetGroupStatus({groupId: this.getGroup.id, status: 'archived'});
        },
        fromArchive() {
            this.actSetGroupStatus({groupId: this.getGroup.id, status: 'open'});
        },
    },
    created() {
        this.loadUsers();
        this.actRequestGroup(this.groupId).then(() => {
            this.actReqwestCourse({id: this.getGroup.course_id});
        });
        this.actRequestAttendances({groupId: this.groupId});
    }
}
</script>


<style lang="scss">
.activeSlot {
    background-color: rgba(139, 94, 60, 0.12) !important;
}

/* Строка «статус · курс» на мобильном: прилипает под фиксированными крошками (шапка 64px + крошки 40px).
   Верхние 8px уходят под крошки, поэтому текст в прилипшем состоянии начинается ровно под ними */
.group-sticky {
    position: sticky;
    top: 96px;
    z-index: 5;
    margin-top: -8px;
    padding: 8px 0;
    background: var(--bp-surface-page);
}

/* Диалог выбора пользователя: на мобильном во весь экран без скругления */
.choose-dialog.choose-dialog--full {
    border-radius: 0 !important;
}

.group-section.v-card {
    padding: 16px;

    @media (min-width: 768px) {
        padding: 20px;
    }

    /* &--table здесь склеился бы в .v-card--table, поэтому класс указан явно */
    &.group-section--table {
        /* Таблица без собственного паддинга, чтобы края совпадали с верхней карточкой */
        .table-sh.v-table {
            padding: 0 !important;
        }

        /* На мобильном список учеников — панели, а не таблица: пустую шапку прячем */
        @media (max-width: 767px) {
            .table-sh.v-table thead {
                display: none;
            }
        }
    }
}

/* Учитель: имя + кнопки «позвонить» и «убрать» */
.teacher-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    max-width: 100%;
    min-height: 32px;
    padding-left: 10px;
    font-size: 13px;
    line-height: 20px;
    border: 1px solid var(--bp-border);
    border-radius: var(--bp-radius-sm, 4px);
    background: var(--bp-surface-muted);
}

.group-stat {
    padding: 12px;
    border: 1px solid var(--bp-border);
    border-radius: var(--bp-radius-md, 8px);
    background: var(--bp-surface-muted);

    &__value {
        font-size: 18px;
        line-height: 24px;
        font-weight: 500;
        margin-top: 4px;
    }

    &__note {
        font-size: 13px;
        font-weight: 400;
        color: var(--bp-text-muted);
    }
}

/* Строка ученика в мобильном списке */
.disciple-item {
    display: flex;
    align-items: center;
    gap: 8px;
    min-height: 52px;
    padding: 6px 8px 6px 12px;
    margin-bottom: 8px;
    border: 1px solid var(--bp-border);
    border-radius: var(--bp-radius-md, 8px);
    background: var(--bp-surface-card);
    cursor: pointer;

    &:active {
        background: var(--bp-surface-muted);
    }

    /* Отчисленный: приглушённый текст и фон */
    &--expelled {
        background: var(--bp-surface-muted);
        color: var(--bp-text-muted);
    }

    /* Замечание по пропускам: янтарная полоска слева, паддинг уменьшен на её толщину */
    &--warning {
        border-left: 3px solid var(--bp-warning);
        padding-left: 10px;
    }
}

/* Всплывающее окно ученика: шапка фиксирована, список занятий прокручивается */
.disciple-sheet.v-card {
    display: flex;
    flex-direction: column;
    max-height: 85vh;
    border-radius: var(--bp-radius-md, 8px) var(--bp-radius-md, 8px) 0 0 !important;
}

/* Отдельными правилами: &__days внутри .disciple-sheet.v-card склеился бы в .v-card__days */
.disciple-sheet__days {
    overflow-y: auto;
}

/* Ручка для свайпа */
.sheet-handle {
    width: 36px;
    height: 4px;
    margin: 8px auto 4px;
    border-radius: 2px;
    background: var(--bp-border-control);
    flex-shrink: 0;
}

.day-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    min-height: 44px;
    padding: 4px 4px 4px 8px;
    border-top: 1px solid var(--bp-border);

    &--open {
        background: rgba(139, 94, 60, 0.08);
        border-radius: var(--bp-radius-sm, 4px);
    }

    &__num {
        min-width: 24px;
        font-size: 12px;
        font-weight: 500;
        color: var(--bp-text-muted);
    }
}

/* Чекбокс посещения в окне ученика: отмечен — зелёный, пропуск — янтарный.
   Классы с двойной специфичностью, чтобы перебить служебные цвета Vuetify */
.day-check.v-checkbox {
    display: inline-flex;

    .v-selection-control {
        min-height: 40px;
    }

    .v-selection-control__input > .v-icon {
        opacity: 1;
    }

    &.day-check--yes .v-selection-control__input > .v-icon {
        color: #15803d !important;
    }

    &.day-check--no .v-selection-control__input > .v-icon {
        color: #d97706 !important;
    }
}

.table-sh {
    .v-selection-control {
        align-items: center;
        contain: layout;
        display: flex;
        flex: 1 0;
        grid-area: control;
        position: relative;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
        justify-content: center;
    }

    table thead th {
        padding: 3px !important;
        cursor: pointer;

        &:hover {
            background-color: var(--bp-surface-muted) !important;
        }
    }

    table tbody td {
        padding: 0 !important;

        &:hover {
            background-color: var(--bp-surface-muted) !important;
        }
    }

    /* Отчисленные — приглушённой строкой в конце списка */
    tr.disciple-row--expelled td {
        color: var(--bp-text-muted);
        background-color: var(--bp-surface-muted);
    }

    /* Замечание по пропускам: янтарная полоска у имени */
    tr.disciple-row--warning td:first-child {
        box-shadow: inset 3px 0 0 var(--bp-warning);
    }
}

.v-calendar-month__days > .v-calendar-month__day {
    min-height: auto !important;
}

.v-calendar-weekly__day-alldayevents-container {
    min-height: 0;
}

.v-date-picker-month {
    padding: 0px 12px !important;
}

.v-date-picker-month__day {
    height: 30px !important;
}

</style>
