<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import {reactive, onMounted, ref, toRefs} from 'vue';
import { useVuelidate } from '@vuelidate/core'
import { required, minLength } from '@vuelidate/validators'
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const props = defineProps({
    tournamentId: {
        type: String,
        required: true,
    },
    fixtureId: {
        type: String,
        required: true,
    },
});

const { fixtureId } = toRefs(props);
const { tournamentId } = toRefs(props);

const state = reactive({
    password: ''
});
const rulesFixtureLogin = () => ( {
    password: { required, minLength: minLength(3) },
});

const v$ = useVuelidate(rulesFixtureLogin(), state);
const emit = defineEmits(['passwordSubmitted']);
const errorMsg = ref('');
const showPassword = ref (false);
const toggleShow = () => {
    showPassword.value = !showPassword.value;
};

const submitFixturePassword = () => {
    v$.value.$touch();
    if (v$.value.$invalid) {
        return;
    }
    axios.post(`/api/tournaments/${tournamentId.value}/fixtures/${fixtureId.value}/check-password`, {
        password: state.password,
    }).then(response => {
            emit('passwordSubmitted');
        })
        .catch(error => {
            errorMsg.value = error.response.data.message;
            console.log(error.response.data);
        });
}

onMounted(async() => {
    errorMsg.value = '';
    state.password = '';
});
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
        <!-- Logo Section -->
        <div class="mb-8">
            <a href="/tournaments" class="group">
                <div class="transform transition-all duration-300 group-hover:scale-105">
                    <ApplicationLogo class="w-24 h-24 fill-current text-gray-300 group-hover:text-white" />
                </div>
            </a>
        </div>

        <!-- Login Card -->
        <div class="w-full sm:max-w-md px-8 py-8 bg-gray-800/95 backdrop-blur-sm border border-gray-700/50 shadow-2xl overflow-hidden sm:rounded-xl">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <font-awesome-icon class="w-8 h-8 text-white" :icon="['fas', 'lock']" />
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">
                    Zaštićena stranica
                </h2>
                <p class="text-gray-400 text-sm leading-relaxed">
                    Ova stranica je zaštićena lozinkom. Molimo unesite ispravnu lozinku da biste pregledali utakmice.
                </p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submitFixturePassword" class="space-y-6">
                <!-- Password Field -->
                <div :class="['space-y-2', { 'error': v$.password.$errors.length }]">
                    <InputLabel for="password" value="Lozinka za utakmicu" class="text-gray-300" />
                    <div class="relative">
                        <TextInput
                            id="password"
                            ref="nameInput"
                            v-model="state.password"
                            :type="showPassword ? 'text' : 'password'"
                            class="mt-1 block w-full bg-gray-700/50 border-gray-600/50 text-white placeholder-gray-400 focus:border-blue-500/50 focus:ring-blue-500/50"
                            placeholder="Unesite lozinku..."
                        />
                        <button 
                            type="button" 
                            @click="toggleShow" 
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white transition-colors duration-200 p-1 rounded-md hover:bg-gray-700/50"
                        >
                            <font-awesome-icon v-if="showPassword" :icon="['fas', 'eye-slash']" class="w-4 h-4" />
                            <font-awesome-icon v-else :icon="['fas', 'eye']" class="w-4 h-4" />
                        </button>
                    </div>
                    
                    <!-- Validation Errors -->
                    <div class="space-y-1" v-for="error of v$.password.$errors" :key="error.$uid">
                        <InputError :message="error.$message" />
                    </div>
                </div>

                <!-- Server Error -->
                <div v-if="errorMsg" class="p-4 bg-red-500/10 border border-red-500/20 rounded-lg">
                    <div class="flex items-center">
                        <font-awesome-icon class="w-4 h-4 text-red-400 mr-2" :icon="['fas', 'exclamation-triangle']" />
                        <p class="text-sm text-red-400">
                            {{ errorMsg }}
                        </p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <PrimaryButton
                        type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl"
                        :disabled="v$.$invalid"
                    >
                        <font-awesome-icon class="w-4 h-4 mr-2" :icon="['fas', 'unlock']" />
                        Potvrdi lozinku
                    </PrimaryButton>
                </div>
            </form>

            <!-- Footer -->
            <div class="mt-8 pt-6 border-t border-gray-700/50 text-center">
                <p class="text-xs text-gray-500">
                    <font-awesome-icon class="w-3 h-3 mr-1" :icon="['fas', 'shield-alt']" />
                    Vaši podaci su sigurni
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom scrollbar */
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

/* Smooth transitions */
* {
    transition: all 0.2s ease-in-out;
}

/* Focus states */
input:focus, button:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Card hover effect */
.sm\:rounded-xl {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.sm\:rounded-xl:hover {
    transform: translateY(-2px);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}

/* Button loading state */
button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
}

/* Error state styling */
.error input {
    border-color: #ef4444;
    box-shadow: 0 0 0 1px #ef4444;
}

/* Backdrop blur support */
.backdrop-blur-sm {
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
</style>
