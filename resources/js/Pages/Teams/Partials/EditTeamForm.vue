<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, reactive } from 'vue';
import { useVuelidate } from '@vuelidate/core'
import {required, minLength, helpers, maxLength} from '@vuelidate/validators'
import {useTournamentStore} from "@/stores/Tournament.js";
import FileInput from "@/Components/FileInput.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const editTeam = ref(false);
const props = defineProps({
    teamId: {
        type: Number,
        required: true,
    },
});
const tournamentStore = useTournamentStore();
const tournamentId = tournamentStore.getId;

const state = reactive({
    name: '',
    shorten_name: '',
    image: null,
    previous_image: null,
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
    shorten_name: { required, minLength: minLength(2), maxLength: maxLength(4) },
    image: {
        validImg: helpers.withMessage('File type not supported', validImg),
        validImgSize: helpers.withMessage('Image can not bigger than ' + fileSizeLimit + ' bytes', validImgSize)
    }
}

const errorMsg = ref('');

const v$ = useVuelidate(rules, state)

const openModal = () => {
    editTeam.value = true;
    axios.get(`/api/tournaments/${tournamentId}/teams/${props.teamId}`)
        .then(response => {
            state.name = response.data['name'];
            state.shorten_name = response.data['shorten_name'];
            state.previous_image = response.data['image_path'];
        })
        .catch(error => {
            console.log(error);
        });
};
const emit = defineEmits(['teamUpdated']);

const config = {
    headers: {
        'Content-Type': 'multipart/form-data',
        'Accept': 'application/json',
    },
};

const submitForm = () => {
    v$.value.$touch();
    if (v$.value.$invalid) {
        return;
    }
    let data = new FormData();
    data.append('_method', 'put');
    data.append('name', state.name);
    data.append('shorten_name', state.shorten_name);
    data.append('image', state.image ? state.image : '');
    axios.post(`/api/tournaments/${tournamentId}/teams/${props.teamId}`, data, config).then(response => {
        emit('teamUpdated', response.data);
        closeModal();
    })
        .catch(error => {
            errorMsg.value = error.response.data.message;
            console.log(error.response.data);
        });
};

const closeModal = () => {
    editTeam.value = false;
    state.name = '';
    state.shorten_name = '';
    state.previous_image = null;
    state.image = null;
};</script>

<template>
    <section class="inline-flex space-x-2 mr-3">
        <button @click="openModal" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
            <font-awesome-icon class="h-5" :icon="['fas', 'pen-to-square']" />
        </button>
        <Modal :show="editTeam" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Edit Team
                </h2>

                <div :class="['mt-6', { error: v$.name.$errors.length }]">
                    <InputLabel for="name" value="Team name" />

                    <TextInput
                        id="name"
                        ref="nameInput"
                        v-model="state.name"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Team name"
                    />
                    <div class="input-errors mt-2" v-for="error of v$.name.$errors" :key="error.$uid">
                        <InputError :message="error.$message" class="mt-2" />
                    </div>
                </div>

                <div :class="['mt-6', { error: v$.shorten_name.$errors.length }]">
                    <InputLabel for="shorten_name" value="Shorten team name" />
                    <TextInput
                        id="shorten_name"
                        ref="nameInput"
                        v-model="state.shorten_name"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Short team name (2 to 4 letters)"
                    />
                    <div class="input-errors mt-2" v-for="error of v$.shorten_name.$errors" :key="error.$uid">
                        <InputError :message="error.$message" class="mt-2" />
                    </div>
                </div>

                <div :class="['mt-6', { error: v$.image.$errors.length }]">
                    <template  v-if="state.previous_image">
                        <p>Current logo</p>
                        <img :src="state.previous_image" alt="Team Image" width="100" height="100"/>
                    </template>
                    <FileInput
                        label-name="Change team logo"
                        help-text="Supported formats JPEG, JPG, PNG, WEBP."
                        v-model="state.image"
                    />
                    <div class="input-errors mt-2" v-for="error of v$.image.$errors" :key="error.$uid">
                        <InputError :message="error.$message" class="mt-2" />
                    </div>
                </div>

                <div v-if="errorMsg" class="mt-2">
                    <p class="text-sm text-red-600 dark:text-red-400">
                        {{ errorMsg }}
                    </p>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
                    <PrimaryButton class="ml-3" @click="submitForm">Edit Team</PrimaryButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
