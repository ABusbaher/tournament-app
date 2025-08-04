<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Obriši Nalog</h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Kada se vaš nalog obriše, svi njegovi resursi i podaci će biti trajno obrisani. Pre brisanja
                naloga, molimo vas da preuzmete sve podatke ili informacije koje želite da sačuvate.
            </p>
        </header>

        <DangerButton @click="confirmUserDeletion">Obriši Nalog</DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Da li ste sigurni da želite da obrišete svoj nalog?
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Kada se vaš nalog obriše, svi njegovi resursi i podaci će biti trajno obrisani. Molimo vas
                    unesite svoju lozinku da potvrdite da želite trajno obrisati svoj nalog.
                </p>

                <div class="mt-6">
                    <InputLabel for="password" value="Lozinka" class="sr-only" />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-3/4"
                        placeholder="Lozinka"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Otkaži </SecondaryButton>

                    <DangerButton
                        class="ml-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Obriši Nalog
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
