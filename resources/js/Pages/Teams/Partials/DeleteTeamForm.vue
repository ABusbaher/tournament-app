<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {ref} from 'vue';
import {useTournamentStore} from "@/stores/Tournament.js";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const confirmingTournamentDeletion = ref(false);
const props = defineProps({
    teamId: {
        type: Number,
        required: true,
    },
});
const tournamentStore = useTournamentStore();
const tournamentId = tournamentStore.getId;

const openModal = () => {
    confirmingTournamentDeletion.value = true;
};

const closeModal = () => {
    errorMsg.value = '';
    confirmingTournamentDeletion.value = false;
};
const errorMsg = ref('');

const emit = defineEmits(['teamDeleted']);

const deleteTeam = () => {
    if (errorMsg.value) return;
    axios.delete(`/api/tournaments/${tournamentId}/teams/${props.teamId}`, {
    }).then(response => {
        emit('teamDeleted', props.tournamentId);
        closeModal();
    })
        .catch(error => {
            console.log(error.response.data.message);
            errorMsg.value = error.response.data.message;
            console.log(error.response);
        });
};
</script>

<template>
    <section class="inline-flex space-x-2">
        <button @click="openModal" class="font-medium text-red-600 dark:text-red-500 hover:underline">
            <font-awesome-icon class="h-5" :icon="['fas', 'trash-can']" />
        </button>

        <Modal :show="confirmingTournamentDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Are you sure you want to delete your team?
                </h2>
                <div v-if="errorMsg" class="mt-2">
                    <p class="text-sm text-red-600 dark:text-red-400 text-center">
                        {{ errorMsg }}
                    </p>
                </div>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>

                    <DangerButton
                        class="ml-3"
                        @click="deleteTeam"
                    >
                        Delete Team
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
