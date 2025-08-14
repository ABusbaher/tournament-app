<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Prijava" />

        <!-- Modern Login Container -->
        <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black flex items-center justify-center p-4">
            <div class="w-full max-w-2xl">
                <!-- Header Section -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full mb-6">
                        <span class="text-white font-semibold text-sm uppercase tracking-wider">Prijava</span>
                    </div>
                    <div class="relative">
                        <!-- Darker background for better contrast -->
                        <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-800 to-black rounded-3xl blur-sm"></div>
                        <div class="relative bg-gradient-to-br from-gray-800/90 via-gray-700/90 to-gray-900/90 rounded-3xl p-8 border border-gray-600/30 backdrop-blur-sm">
                            <h1 class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-gray-100 to-gray-200 leading-none tracking-tight drop-shadow-lg">
                                Dobrodošli
                            </h1>
                            <p class="text-gray-300 mt-4 text-lg font-medium">Prijavite se na svoj nalog</p>
                        </div>
                    </div>
                </div>

                <!-- Status Message -->
                <div v-if="status" class="mb-6 p-4 bg-green-500/10 border border-green-500/30 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-green-400 font-medium">{{ status }}</span>
                    </div>
                </div>

                <!-- Login Form Card -->
                <div class="relative overflow-hidden bg-gradient-to-br from-gray-800 via-gray-700 to-gray-800 rounded-2xl shadow-2xl border border-gray-600/30 backdrop-blur-sm">
                    <!-- Animated Background -->
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-purple-500/10 to-pink-500/10 animate-pulse"></div>
                    
                    <div class="relative p-8">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Email Field -->
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-300 mb-2">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                        </svg>
                                        Email adresa
                                    </div>
                                </label>
                                <div class="relative">
                                    <input
                                        id="email"
                                        type="email"
                                        v-model="form.email"
                                        required
                                        autofocus
                                        autocomplete="username"
                                        class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all duration-200"
                                        placeholder="Unesite vašu email adresu"
                                    />
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                        </svg>
                                    </div>
                                </div>
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <!-- Password Field -->
                            <div>
                                <label for="password" class="block text-sm font-semibold text-gray-300 mb-2">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        Lozinka
                                    </div>
                                </label>
                                <div class="relative">
                                    <input
                                        id="password"
                                        type="password"
                                        v-model="form.password"
                                        required
                                        autocomplete="current-password"
                                        class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 transition-all duration-200"
                                        placeholder="Unesite vašu lozinku"
                                    />
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <InputError class="mt-2" :message="form.errors.password" />
                            </div>

                            <!-- Remember Me & Forgot Password -->
                            <div class="flex items-center justify-between">
                                <label class="flex items-center group cursor-pointer">
                                    <div class="relative">
                                        <input
                                            type="checkbox"
                                            v-model="form.remember"
                                            class="sr-only"
                                        />
                                        <div class="w-5 h-5 bg-gray-700/50 border border-gray-600/50 rounded flex items-center justify-center group-hover:border-blue-500/50 transition-all duration-200">
                                            <svg v-if="form.remember" class="w-3 h-3 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <span class="ml-3 text-sm text-gray-300 group-hover:text-white transition-colors duration-200">Zapamti me</span>
                                </label>

                                <Link
                                    v-if="canResetPassword"
                                    :href="route('password.request')"
                                    class="text-sm text-blue-400 hover:text-blue-300 transition-colors duration-200 hover:underline"
                                >
                                    Zaboravili ste lozinku?
                                </Link>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-4">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl border border-blue-500/30 hover:border-blue-400/50 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center space-x-2"
                                >
                                    <svg v-if="form.processing" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                    </svg>
                                    <span>{{ form.processing ? 'Prijavljivanje...' : 'Prijavi se' }}</span>
                                </button>
                            </div>
                        </form>

                        <!-- Divider -->
                        <div class="mt-8 pt-6 border-t border-gray-600/30">
                            <div class="text-center">
                                <p class="text-sm text-gray-400">
                                    Nemate nalog? 
                                    <Link :href="route('register')" class="text-blue-400 hover:text-blue-300 transition-colors duration-200 hover:underline font-medium">
                                        Registrujte se
                                    </Link>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Info -->
                <div class="mt-8 text-center">
                    <p class="text-xs text-gray-500">
                        Sigurno i pouzdano prijavljivanje
                    </p>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
/* Custom scrollbar for dark theme */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #1f2937;
}

::-webkit-scrollbar-thumb {
    background: #374151;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #4b5563;
}

/* Input focus effects */
input:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Checkbox custom styling */
input[type="checkbox"]:checked + div {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    border-color: #3b82f6;
}

/* Button hover animation */
button:hover:not(:disabled) {
    transform: translateY(-1px);
}

/* Pulse animation for background */
@keyframes pulse {
    0%, 100% {
        opacity: 0.1;
    }
    50% {
        opacity: 0.2;
    }
}

.animate-pulse {
    animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Smooth transitions */
* {
    transition: all 0.2s ease-in-out;
}
</style>
