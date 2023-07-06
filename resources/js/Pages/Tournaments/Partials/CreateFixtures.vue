<script setup>
import BigButton from "@/Components/BigButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {defineEmits, ref} from "vue";
import {useTournamentStore} from "@/stores/Tournament.js";
import Modal from "@/Components/Modal.vue";

const modalOpened = ref(false);
const tournamentStore = useTournamentStore();
const tournamentId = tournamentStore.getId;
const error403 = ref('');

const openModal = () => {
    modalOpened.value = true;
};

const closeModal = () => {
    modalOpened.value = false;
};

const emit = defineEmits(['fixturesCreated']);

const createFixtures = () => {
    if (error403.value) return;
    axios.post(`/api/tournaments/${tournamentId}/games`, {
        tournament_id: tournamentId
    }).then(response => {
        console.log()
        emit('fixturesCreated', response.data);
        closeModal();
    })
        .catch(error => {
            if (error.response && error.response.status === 422) {
                error403.value = error.response.data.errors.tournament_id[0];
            } else {
                console.log(error.response.data);
            }
        });
};

</script>
<template>
    <div class="w-3/5 mx-auto mt-10">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 text-center">
            Proceed with creation of random schedule.
        </h2>
        <div class="mt-6 pb-10 flex justify-center">
            <BigButton class="ml-3" @click="openModal">
                Create fixtures
            </BigButton>
        </div>
        <Modal :show="modalOpened" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Once you confirmed you want be able to add more/delete teams or change number of tournament rounds and type of tournament.
                </h2>
                <div v-if="error403" class="mt-2">
                    <p class="text-sm text-red-600 dark:text-red-400">
                        {{ error403 }}
                    </p>
                </div>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
                    <PrimaryButton class="ml-3" @click="createFixtures">Proceed</PrimaryButton>
                </div>
            </div>
        </Modal>
    </div>
</template>
