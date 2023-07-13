<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import FileInput from "@/Components/FileInput.vue";
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { reactive, ref } from 'vue';
import {useVuelidate} from '@vuelidate/core'
import {helpers, minLength, required} from '@vuelidate/validators'
import {useTournamentStore} from "@/stores/Tournament.js";

const addTeam = ref(false);
const tournamentStore = useTournamentStore();
const tournamentId = tournamentStore.getId;

const state = reactive({
    name: '',
    image: null,
});
const imageExtensions = ['jpeg', 'png', 'jpg', 'webp'];
const fileSizeLimit = 2 * 1024 * 1024;
const validImg = (value) => {
    if (!value) return true;
    const extension = value.name.split('.').pop();
    return imageExtensions.includes(extension.toLowerCase());
}

const validImgSize = (value) => {
    if (!value) return true;
    return value.size <= fileSizeLimit;
}

const rules = {
    name: { required, minLength: minLength(3) },
    image: {
        validImg: helpers.withMessage('File type not supported', validImg),
        validImgSize: helpers.withMessage('Image can not bigger than ' + fileSizeLimit + ' bytes', validImgSize)
    }
}

const v$ = useVuelidate(rules, state)
const maxTeamError = ref('');
const openModal = () => {
    addTeam.value = true;
};
const emit = defineEmits(['teamCreated']);

const config = {
    headers: {
        'Content-Type': 'multipart/form-data',
    },
};

const submitForm = () => {
    v$.value.$touch();
    if (v$.value.$invalid) {
        return;
    }
    axios.post(`/api/tournaments/${tournamentId}/teams`, {
        name: state.name,
        tournament_id: tournamentId,
        ...(state.image && { image: state.image }),
    }, config).then(response => {
        emit('teamCreated', response.data);
        closeModal();
    })
        .catch(error => {
            if (error.response && error.response.status === 422) {
                maxTeamError.value = error.response.data.errors.tournament_id[0];
            }
            else if (error.response && error.response.status === 403) {
                maxTeamError.value = error.response.data.message;
            }
            else {
                console.log(error.response.data);
            }
        });
};

const closeModal = () => {
    addTeam.value = false;
    state.name = '';
    state.image = null;
};
</script>

<template>
    <section class="space-y-6">
        <PrimaryButton @click="openModal">Add Team</PrimaryButton>

        <Modal :show="addTeam" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Add Team
                </h2>
                <div :class="['mt-6', { error: v$.name.$errors.length }]">
                    <InputLabel for="name" value="Team name" />

                    <TextInput
                        id="name"
                        ref="nameInput"
                        v-model="state.name"
                        type="text"
                        class="mt-1 block w-3/4"
                        placeholder="Team name"
                    />
                    <div class="input-errors mt-2" v-for="error of v$.name.$errors" :key="error.$uid">
                        <InputError :message="error.$message" class="mt-2" />
                    </div>
                </div>

                <div :class="['mt-6', { error: v$.image.$errors.length }]">
                    <FileInput
                        label-name="Upload team logo"
                        help-text="Supported formats JPEG, JPG, PNG, WEBP."
                        v-model="state.image"
                    />
                    <div class="input-errors mt-2" v-for="error of v$.image.$errors" :key="error.$uid">
                        <InputError :message="error.$message" class="mt-2" />
                    </div>
                </div>
                <div v-if="maxTeamError" class="mt-2">
                    <p class="text-sm text-red-600 dark:text-red-400">
                        {{ maxTeamError }}
                    </p>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>

                    <PrimaryButton
                        class="ml-3"
                        @click="submitForm"
                    >
                        Add Team
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
