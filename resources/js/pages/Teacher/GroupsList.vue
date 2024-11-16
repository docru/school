<template>
    <v-card>
        <v-card-title>
            <div class="tw-flex tw-justify-between">
                <div>Группы</div>
            </div>
        </v-card-title>
        <v-data-table
            v-if="groups.length"
            :headers="headers"
            :items="groups || []"
            items-per-page="100"
            :loading="load"
            @click:row="go"
        >
            <template #bottom></template>
            <template v-slot:item.name="{item}">
                {{ item.group.name }}
            </template>
            <template v-slot:item.course="{item}">
                {{ item.course.name }}
            </template>
        </v-data-table>
    </v-card>
</template>

<script>
import {mapActions, mapGetters, mapMutations} from "vuex";

export default {
    name: 'GroupsList',
    data() {
        return {
            course: null,
            headers: [
                {title: 'Группа', key: 'name'},
                {title: 'Курс', key: 'course'},
            ],
        }
    },
    computed: {
        ...mapGetters('teacher', {groups: 'getGroups', load: 'getLoad'})
    },
    methods: {
        go(item, row) {
            this.$router.push({
                name: 'TeacherDetailGroup',
                params: {id: row.item.group.id}
            })
        },
        ...mapMutations('app', ['setSnackBar']),
        ...mapActions('teacher', ['actRequestGroups',]),
    },
    created() {
        this.actRequestGroups();
    }
}
</script>
