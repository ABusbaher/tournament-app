<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import FileInput from "@/Components/FileInput.vue";
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {computed, onMounted, reactive, ref, watch} from 'vue';
import {useVuelidate} from '@vuelidate/core'
import {helpers, integer, maxLength, maxValue, minLength, minValue, required} from '@vuelidate/validators'
import {useTournamentStore} from "@/stores/Tournament.js";

const addTeam = ref(false);
const tournamentStore = useTournamentStore();
const tournamentId = tournamentStore.getId;
tournamentStore.getByTournamentById(tournamentId);
const tournamentType = computed(() => tournamentStore.getType);

const state = reactive({
    name: '',
    shorten_name: '',
    negative_points: '',
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
    shorten_name: { required, minLength: minLength(2), maxLength: maxLength(4) },
    negative_points: { integer, minValue: minValue(-100), maxValue: maxValue(0) },
    image: {
        validImg: helpers.withMessage('Tip fajla nije podržan', validImg),
        validImgSize: helpers.withMessage('Slika ne može biti veća od ' + fileSizeLimit + ' bajtova', validImgSize)
    }
}

const v$ = useVuelidate(rules, state)
const maxTeamError = ref('');
const openModal = async () => {
    await tournamentStore.getByTournamentById(tournamentId);
    addTeam.value = true;
};
const emit = defineEmits(['teamCreated']);

const config = {
    headers: {
        'Content-Type': 'multipart/form-data',
    },
};

onMounted(async () => {
    try {
        await tournamentStore.getByTournamentById(tournamentId);
    } catch (error) {
        console.log(error);
    }
});

const submitForm = () => {
    v$.value.$touch();
    if (v$.value.$invalid) {
        return;
    }
    axios.post(`/api/tournaments/${tournamentId}/teams`, {
        name: state.name,
        shorten_name: state.shorten_name,
        negative_points: tournamentType.value === 'league' ? state.negative_points : null,
        tournament_id: tournamentId,
        ...(state.image && { image: state.image }),
    }, config).then(response => {
        emit('teamCreated', response.data);
        closeModal();
    })
        .catch(error => {
                maxTeamError.value = error.response.data.message;
                console.log(error.response.data);
        });
};

const closeModal = () => {
    addTeam.value = false;
    state.name = '';
    state.shorten_name = '';
    state.negative_points = '';
    maxTeamError.value = '';
    state.image = null;
};
</script>

<template>
    <section class="space-y-6">
        <PrimaryButton @click="openModal">Dodaj Tim</PrimaryButton>

        <Modal :show="addTeam" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Dodaj Tim
                </h2>
                <div :class="['mt-6', { error: v$.name.$errors.length }]">
                    <InputLabel for="name" value="Ime tima" />

                    <TextInput
                        id="name"
                        ref="nameInput"
                        v-model="state.name"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Ime tima"
                    />
                    <div class="input-errors mt-2" v-for="error of v$.name.$errors" :key="error.$uid">
                        <InputError :message="error.$message" class="mt-2" />
                    </div>
                </div>

                <div :class="['mt-6', { error: v$.shorten_name.$errors.length }]">
                    <InputLabel for="shorten_name" value="Skraćeno ime tima" />
                    <TextInput
                        id="shorten_name"
                        ref="nameInput"
                        v-model="state.shorten_name"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Kratko ime tima (2 do 4 slova)"
                    />
                    <div class="input-errors mt-2" v-for="error of v$.shorten_name.$errors" :key="error.$uid">
                        <InputError :message="error.$message" class="mt-2" />
                    </div>
                </div>

                <div v-if="tournamentType === 'league'" :class="['mt-6', { error: v$.negative_points.$errors.length }]">
                    <InputLabel for="negative_points" value="Negativni poeni (opciono)" />
                    <TextInput
                        id="negative_points"
                        ref="nameInput"
                        v-model="state.negative_points"
                        type="number"
                        min="-100"
                        max="0"
                        class="mt-1 block w-full"
                        placeholder="Unesite negativne poene"
                    />
                    <div class="input-errors mt-2" v-for="error of v$.negative_points.$errors" :key="error.$uid">
                        <InputError :message="error.$message" class="mt-2" />
                    </div>
                </div>

                <div :class="['mt-6', { error: v$.image.$errors.length }]">
                    <FileInput
                        label-name="Otpremi logo tima"
                        help-text="Podržani formati JPEG, JPG, PNG, WEBP."
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
                    <SecondaryButton @click="closeModal"> Otkaži </SecondaryButton>

                    <PrimaryButton
                        class="ml-3"
                        @click="submitForm"
                    >
                        Dodaj Tim
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
