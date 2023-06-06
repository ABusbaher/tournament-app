<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const addTournament = ref(false);
const nameInput = ref(null);

const form = useForm({
    name: '',
});

const confirmUserDeletion = () => {
    addTournament.value = true;

    nextTick(() => nameInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => nameInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    addTournament.value = false;

    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <PrimaryButton @click="confirmUserDeletion">Add Tournament</PrimaryButton>

        <Modal :show="addTournament" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Add Tournament
                </h2>

                <div class="mt-6">
                    <InputLabel for="name" value="Name" class="sr-only" />

                    <TextInput
                        id="name"
                        ref="nameInput"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-3/4"
                        placeholder="Tournament name"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>

                    <PrimaryButton
                        class="ml-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Add Tournament
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
