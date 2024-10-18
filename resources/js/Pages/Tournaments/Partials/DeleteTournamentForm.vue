<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {ref} from 'vue';
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const confirmingTournamentDeletion = ref(false);
const props = defineProps({
    tournamentId: {
        type: Number,
        required: true,
    },
});

const openModal = () => {
    confirmingTournamentDeletion.value = true;
};

const closeModal = () => {
    confirmingTournamentDeletion.value = false;
};

const emit = defineEmits(['tournamentDeleted']);

const deleteTournament = () => {
    axios.delete(`/api/tournaments/${props.tournamentId}`, {
    }).then(response => {
        emit('tournamentDeleted', props.tournamentId);
        closeModal();
    })
    .catch(error => {
        console.log(error.response);
    });
};
</script>

<template>
    <section>
        <button @click="openModal" class="font-medium text-red-600 dark:text-red-500 hover:underline">
            <font-awesome-icon :icon="['fas', 'trash-can']" />
        </button>

        <Modal :show="confirmingTournamentDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Are you sure you want to delete your tournament?
                </h2>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>

                    <DangerButton
                        class="ml-3"
                        @click="deleteTournament"
                    >
                        Delete Tournament
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
