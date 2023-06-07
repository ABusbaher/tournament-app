<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from "@/Components/SelectInput.vue";
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref, defineEmits } from 'vue';

const addTournament = ref(false);
const nameInput = ref(null);
const selectedType = ref('');

const form = useForm({
    name: '',
    rounds: '',
    types: [
        { value: 'league', label: 'League' },
        { value: 'knockout', label: 'Knockout' },
        { value: 'league+knockout', label: 'League+Knockout' }
    ],
});

const data = {
    name: '',
    rounds: '',
    type: '',
    types: [
        { value: 'league', label: 'League' },
        { value: 'elimination', label: 'Elimination (Cup)' },
        { value: 'group+elimination', label: 'Group+Elimination' }
    ],
};

const openForm = () => {
    addTournament.value = true;

    nextTick(() => nameInput.value.focus());
};
const emit = defineEmits(['tournamentCreated']);

const submitForm = () => {
    axios.post('tournaments', {
        name: data.name,
        rounds: data.rounds,
        type: selectedType.value,
    }).then(response => {
        emit('tournamentCreated', response.data['tournament']);
        closeModal();
    })
        .catch(error => {
            console.log(error);
            // Handle error
        });
};

const closeModal = () => {
    addTournament.value = false;
    data.rounds = '';
    data.name = '';
    selectedType.value = ''
    form.reset();
};

const errorBehavior = () => {
    console.log(selectedType)
    // addTournament.value = false;
    nameInput.value.focus();
    // form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <PrimaryButton @click="openForm">Add Tournament</PrimaryButton>

        <Modal :show="addTournament" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Add Tournament
                </h2>

                <div class="mt-6">
                    <InputLabel for="name" value="Tournament name" />

                    <TextInput
                        id="name"
                        ref="nameInput"
                        v-model="data.name"
                        type="text"
                        class="mt-1 block w-3/4"
                        placeholder="Tournament name"
                    />

                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <div class="mt-6">
                    <InputLabel for="rounds" value="Number of rounds" />

                    <TextInput
                        id="rounds"
                        ref="nameInput"
                        v-model="data.rounds"
                        type="number"
                        min="1"
                        max="4"
                        class="mt-1 block w-3/4"
                        placeholder="1 - 4"
                    />

                    <InputError :message="form.errors.rounds" class="mt-2" />
                </div>

                <div class="mt-6">
                    <InputLabel for="types" value="Choose type of tournament"/>

                    <SelectInput
                        id="types"
                        name="types"
                        v-model="selectedType"
                        :options="data.types"
                        class="mt-1 block w-3/4"
                    />

                    <InputError :message="form.errors.types" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>

                    <PrimaryButton
                        class="ml-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="submitForm"
                    >
                        Add Tournament
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
