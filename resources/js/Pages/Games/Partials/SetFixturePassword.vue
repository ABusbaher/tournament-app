<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {computed, reactive, ref, toRefs} from 'vue';
import {useVuelidate} from '@vuelidate/core';
import {helpers, minLength, sameAs} from '@vuelidate/validators';
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

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

const passwordRule = helpers.withMessage(
    'Nova lozinka može biti prazna ili mora imati najmanje 3 karaktera.',
    value => !value || minLength(3).$validator(value)
);

const state = reactive({
    current_password: '',
    new_password: '',
    confirm_new_password: '',
});

const rules = {
    new_password : { passwordRule },
    confirm_new_password: { sameAsPassword: sameAs(computed(() => state.new_password))},
}

const v$ = useVuelidate(rules, state)
const passwordError = ref('');
const showPassword = ref (false);
const toggleShow = () => {
    showPassword.value = !showPassword.value;
};
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
        <!-- Modern Button -->
        <button
            @click="openModal"
            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-medium rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl border border-green-500/30 hover:border-green-400/50"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            Postavi lozinku za kolo
        </button>

        <Modal :show="modalOpened" @close="closeModal">
            <!-- Modern Modal Content -->
            <div class="relative bg-gradient-to-br from-gray-800 via-gray-700 to-gray-800 shadow-2xl border border-gray-600/30 backdrop-blur-sm overflow-hidden">
                <!-- Animated Background -->
                <div class="absolute inset-0 bg-gradient-to-r from-green-500/10 via-emerald-500/10 to-teal-500/10 animate-pulse"></div>

                <!-- Header -->
                <div class="relative p-8 border-b border-gray-600/30">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-green-600 to-emerald-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">
                                    Postavi lozinku za kolo
                                </h2>
                                <p class="text-gray-400 text-sm">Konfigurišite pristup za ovu rundu</p>
                            </div>
                        </div>
                        <button
                            @click="closeModal"
                            class="text-gray-400 hover:text-white transition-colors duration-200 p-2 rounded-lg hover:bg-gray-700/50"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="relative p-8 space-y-6">
                    <!-- Current Password -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-gray-300">
                            Trenutna lozinka
                        </label>
                        <p class="text-xs text-gray-400 mb-3">Ostavite prazno ako nije još postavljena</p>
                        <div class="relative">
                            <input
                                v-model="state.current_password"
                                :type="showPassword ? 'text' : 'password'"
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500/50 focus:border-green-500/50 transition-all duration-300"
                                placeholder="Unesite trenutnu lozinku"
                            />
                            <button
                                type="button"
                                @click="toggleShow"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-white transition-colors duration-200"
                            >
                                <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                </svg>
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- New Password -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-gray-300">
                            Nova lozinka
                        </label>
                        <div class="relative">
                            <input
                                v-model="state.new_password"
                                :type="showPassword ? 'text' : 'password'"
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500/50 focus:border-green-500/50 transition-all duration-300"
                                placeholder="Nova lozinka"
                            />
                            <button
                                type="button"
                                @click="toggleShow"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-white transition-colors duration-200"
                            >
                                <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                </svg>
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="input-errors" v-for="error of v$.new_password.$errors" :key="error.$uid">
                            <InputError :message="error.$message" />
                        </div>
                    </div>

                    <!-- Confirm New Password -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-gray-300">
                            Potvrdi novu lozinku
                        </label>
                        <div class="relative">
                            <input
                                v-model="state.confirm_new_password"
                                :type="showPassword ? 'text' : 'password'"
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500/50 focus:border-green-500/50 transition-all duration-300"
                                placeholder="Potvrdi novu lozinku"
                            />
                            <button
                                type="button"
                                @click="toggleShow"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-white transition-colors duration-200"
                            >
                                <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                </svg>
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="input-errors" v-for="error of v$.confirm_new_password.$errors" :key="error.$uid">
                            <InputError message="Nova lozinka i potvrda nove lozinke se ne poklapaju" />
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div v-if="passwordError" class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-red-400">{{ passwordError }}</p>
                        </div>
                    </div>

                    <!-- Password Requirements Info -->
                    <div class="p-4 bg-blue-500/10 border border-blue-500/30 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-blue-400">Lozinka može biti prazna ili mora imati najmanje 3 karaktera</p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="relative p-8 border-t border-gray-600/30 bg-gray-800/50">
                    <div class="flex justify-end space-x-3">
                        <button
                            @click="closeModal"
                            class="px-6 py-3 text-gray-300 hover:text-white bg-gray-700/50 hover:bg-gray-600/50 border border-gray-600/50 hover:border-gray-500/50 rounded-lg font-medium transition-all duration-300 transform hover:scale-105"
                        >
                            Otkaži
                        </button>
                        <button
                            @click="submitForm"
                            class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-medium rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl border border-green-500/30 hover:border-green-400/50 flex items-center space-x-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Postavi lozinku</span>
                        </button>
                    </div>
                </div>
            </div>
        </Modal>
    </section>
</template>

<style scoped>
/* Custom scrollbar for modal */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #1f2937;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: #4b5563;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}

/* Input focus animations - improved */
input:focus {
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
    border-color: #22c55e !important;
    background: #374151 !important;
}

/* Remove white background on input hover/focus */
input:hover {
    background: #374151 !important;
    border-color: #4b5563 !important;
}

input {
    background: #374151 !important;
    color: #f3f4f6 !important;
}

input::placeholder {
    color: #9ca3af !important;
}

/* Button hover effects */
button:hover {
    transform: translateY(-1px);
}

/* Modal backdrop blur */
:deep(.fixed) {
    backdrop-filter: blur(8px);
    background-color: rgba(0, 0, 0, 0.5);
}
</style>
