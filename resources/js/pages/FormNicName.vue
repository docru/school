<template>
    <div class="tw-m-auto tw-w-full tw-h-[90vh]
              tw-flex tw-justify-center
              tw-items-start
">

        <div class="tw-w-[300px] tw-flex tw-flex-col tw-gap-2">
            <v-text-field v-model="name" type="text" label="Имя"></v-text-field>
            <v-text-field v-model="nickname" type="text" label="Псевдоним"></v-text-field>
            <v-textarea v-model="nickname_description" label="Описание псевдонима"></v-textarea>
            <v-btn @click="save()">Сохранить</v-btn>
        </div>

    </div>
</template>
<script>
import {mapActions, mapGetters, mapMutations} from "vuex";

export default {
    data: () => {
        return {}
    },
    computed: {
        ...mapGetters('profile', ['getProfile']),
        name: {
            get() {
                return this.getProfile.name;
            },
            set(data) {
                let profile = this.getProfile;
                profile.name = data;
                this.setProfile(profile);
            },
        },
        nickname: {
            get() {
                return this.getProfile.nickname;
            },
            set(data) {
                let profile = this.getProfile;
                profile.nickname = data;
                this.setProfile(profile);
            },
        },
        nickname_description: {
            get() {
                return this.getProfile.nickname_description;
            },
            set(data) {
                let profile = this.getProfile;
                profile.nickname_description = data;
                this.setProfile(profile);
            },
        },
    },
    methods: {
        ...mapMutations('profile', ['setProfile']),
        ...mapActions('profile', ['actSaveProfile']),
        async save() {
            await this.actSaveProfile();
            if (!this.validProfile) {
                this.$router.push({
                    name: 'Home'
                })
            }
        },
    }

}

</script>
