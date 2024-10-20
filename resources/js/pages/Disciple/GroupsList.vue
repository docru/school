<template>
    <v-card>
        <v-card-title>
            <div class="tw-flex tw-justify-between">
                <div>Группы</div>
            </div>
        </v-card-title>
        <v-data-table
            :mobile="true"
            v-if="groups.length"
            :headers="headers"
            :items="groups || []"
            :loading="load"
            @click:row="go"
        >
          <template #bottom></template>
          <template #item="{ item,columns }" v-if="$vuetify.display.name === 'sm'"  >
            <div @click="goFun(item.group.id)" style="border: 1px dashed #c7c7d1; padding: 5px; cursor: pointer;">
                {{item.group.name}} / {{item.course.name}}
            </div>
<!--            {{item}}-->
          </template>
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
        ...mapGetters('disciple', {groups: 'getGroups', load: 'getLoad'})
    },
    methods: {
        go(item, row) {
            this.$router.push({
                name: 'DiscipleDetailGroup',
                params: {id: row.item.group.id}
            })
        },
      goFun(id) {
        this.$router.push({
          name: 'DiscipleDetailGroup',
          params: {id:id}
        })
      },
        ...mapMutations('app', ['setSnackBar']),
        ...mapActions('disciple', ['actRequestGroups',]),
    },
    created() {
        this.actRequestGroups();
    }
}
</script>
