<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {computed, reactive, ref, toRefs} from 'vue';
import {useVuelidate} from '@vuelidate/core'
import {sameAs} from '@vuelidate/validators'

const props = defineProps({
    fixtureId: {
        type: String,
        required: true,
    },
    tournamentId: {
        type: String,
        required: true,
    },
});

const { fixtureId } = toRefs(props);
const { tournamentId } = toRefs(props);

const modalOpened = ref(false);

const state = reactive({
    current_password: '',
    new_password: '',
    confirm_new_password: '',
});

const rules = {
    confirm_new_password: { sameAsPassword: sameAs(computed(() => state.new_password))},
}

const v$ = useVuelidate(rules, state)
const passwordError = ref('');
const openModal = () => {
    modalOpened.value = true;
};
const emit = defineEmits(['passwordUpdated']);

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
    axios.patch(`/api/tournaments/${tournamentId.value}/fixtures/${fixtureId.value}/set-password`, {
        current_password: state.current_password,
        new_password: state.new_password,
        confirm_new_password: state.confirm_new_password,
    }).then(response => {
        emit('passwordUpdated', response.data);
        closeModal();
    }).catch(error => {
        passwordError.value = error.response.data.message;
        console.log(error.response.data);
    });
};

const closeModal = () => {
    modalOpened.value = false;
    state.current_password = '';
    state.new_password = '';
    state.confirm_new_password = '';
    passwordError.value = '';
};
</script>

<template>
    <section class="space-y-6">
        <PrimaryButton @click="openModal">Set fixture password</PrimaryButton>

        <Modal :show="modalOpened" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Set fixture password
                </h2>
                <div class="mt-6">
                    <InputLabel for="current_password" value="Current password, leave blank if not set yet" />
                    <TextInput
                        id="current_password"
                        ref="nameInput"
                        v-model="state.current_password"
                        type="password"
                        class="mt-1 block w-full"
                        placeholder="Enter current password"
                    />
                </div>

                <div class="mt-6">
                    <InputLabel for="new_password" value="New password" />
                    <TextInput
                        id="new_password"
                        ref="nameInput"
                        v-model="state.new_password"
                        type="password"
                        class="mt-1 block w-full"
                        placeholder="New password"
                    />
                </div>

                <div :class="['mt-6', { error: v$.confirm_new_password.$errors.length }]">
                    <InputLabel for="confirm_new_password" value="Confirm new password" />
                    <TextInput
                        id="confirm_new_password"
                        ref="nameInput"
                        v-model="state.confirm_new_password"
                        type="password"
                        class="mt-1 block w-full"
                        placeholder="Confirm new password"
                    />
                    <div class="input-errors mt-2" v-for="error of v$.confirm_new_password.$errors" :key="error.$uid">
                        <InputError message="New password and confirm new password are not the same" class="mt-2" />
                    </div>
                </div>

                <div v-if="passwordError" class="mt-2">
                    <p class="text-sm text-red-600 dark:text-red-400">
                        {{ passwordError }}
                    </p>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>

                    <PrimaryButton
                        class="ml-3"
                        @click="submitForm"
                    >
                        Set fixture password
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
