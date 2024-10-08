<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, reactive } from 'vue';
import { useVuelidate } from '@vuelidate/core'
import { required, minLength } from '@vuelidate/validators'

const editTournament = ref(false);
const props = defineProps({
    tournamentId: {
        type: Number,
        required: true,
    },
});

const state = reactive({
    name: '',
    type: '',
    rounds: '',
    id: null,
})
const rules = {
    name: { required, minLength: minLength(3) },
}

const v$ = useVuelidate(rules, state)

const openModal = () => {
    editTournament.value = true;
    axios.get(`/api/tournaments/${props.tournamentId}`)
        .then(response => {
            state.name = response.data['name'];
            state.type = response.data['type'];
            state.rounds = response.data['rounds'].toString();
            state.id = response.data['id'];
        })
        .catch(error => {
            console.log(error);
        });
};
const emit = defineEmits(['tournamentEdited']);

const submitForm = () => {
    v$.value.$touch();
    if (v$.value.$invalid) {
        return;
    }
    axios.patch(`/api/tournaments/${props.tournamentId}`, {
        name: state.name,
    }).then(response => {
        const updatedTournament = response.data;
        emit('tournamentEdited', updatedTournament);
        closeModal();
    })
   .catch(error => {
       console.log(error.response);
   });
};

const closeModal = () => {
    editTournament.value = false;
};
</script>

<template>
    <section class="space-y-6">
        <button @click="openModal" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</button>
        <Modal :show="editTournament" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Edit Tournament
                </h2>

                <div :class="['mt-6', { error: v$.name.$errors.length }]">
                    <InputLabel for="name" value="Tournament name" />

                    <TextInput
                        id="name"
                        ref="nameInput"
                        v-model="state.name"
                        type="text"
                        class="mt-1 block w-3/4"
                        placeholder="Tournament name"
                    />
                    <div class="input-errors mt-2" v-for="error of v$.name.$errors" :key="error.$uid">
                        <InputError :message="error.$message" class="mt-2" />
                    </div>
                </div>

                <div class="mt-6">
                    <InputLabel for="rounds" value="Number of rounds" />

                    <TextInput
                        readonly
                        id="rounds"
                        ref="nameInput"
                        v-model="state.rounds"
                        type="number"
                        class="mt-1 block w-3/4"
                    />
                </div>

                <div class="mt-6">
                    <InputLabel for="types" value="Type of tournament"/>

                    <TextInput
                        readonly
                        id="types"
                        ref="nameInput"
                        v-model="state.type"
                        type="text"
                        class="mt-1 block w-3/4"
                    />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>

                    <PrimaryButton
                        class="ml-3"
                        @click="submitForm"
                    >
                        Edit Tournament
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
