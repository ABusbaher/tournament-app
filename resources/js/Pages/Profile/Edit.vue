<script setup>
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import Navigation from "@/Layouts/Navigation.vue";
import AdminNavigation from "@/Layouts/AdminNavigation.vue";

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profil" />

    <div v-if="$page.props.auth.user && $page.props.auth.user.role === 'admin'">
        <admin-navigation></admin-navigation>
        <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black">
            <!-- Modern Header Section -->
            <div class="text-center mb-12 pt-8">
                <div class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full mb-6">
                    <span class="text-white font-semibold text-sm uppercase tracking-wider">Profil</span>
                </div>
                <div class="relative">
                    <!-- Darker background for better contrast -->
                    <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-800 to-black rounded-3xl blur-sm"></div>
                    <div class="relative bg-gradient-to-br from-gray-800/90 via-gray-700/90 to-gray-900/90 rounded-3xl p-8 border border-gray-600/30 backdrop-blur-sm">
                        <h1 class="text-5xl md:text-6xl lg:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-gray-100 to-gray-200 leading-none tracking-tight drop-shadow-lg">
                            Profil
                        </h1>
                        <p class="text-gray-300 mt-4 text-lg font-medium">Upravljajte svojim nalogom i postavkama</p>
                    </div>
                </div>
            </div>

            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                <!-- Profile Information Card -->
                <div class="relative overflow-hidden bg-gradient-to-br from-gray-800 via-gray-700 to-gray-800 rounded-2xl shadow-2xl border border-gray-600/30 backdrop-blur-sm">
                    <!-- Animated Background -->
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-indigo-500/10 to-purple-500/10 animate-pulse"></div>
                    <div class="relative p-8">
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                        />
                    </div>
                </div>

                <!-- Update Password Card -->
                <div class="relative overflow-hidden bg-gradient-to-br from-gray-800 via-gray-700 to-gray-800 rounded-2xl shadow-2xl border border-gray-600/30 backdrop-blur-sm">
                    <!-- Animated Background -->
                    <div class="absolute inset-0 bg-gradient-to-r from-green-500/10 via-emerald-500/10 to-teal-500/10 animate-pulse"></div>
                    <div class="relative p-8">
                        <UpdatePasswordForm />
                    </div>
                </div>

                <!-- Delete Account Card -->
                <div class="relative overflow-hidden bg-gradient-to-br from-gray-800 via-gray-700 to-gray-800 rounded-2xl shadow-2xl border border-gray-600/30 backdrop-blur-sm">
                    <!-- Animated Background -->
                    <div class="absolute inset-0 bg-gradient-to-r from-red-500/10 via-pink-500/10 to-rose-500/10 animate-pulse"></div>
                    <div class="relative p-8">
                        <DeleteUserForm />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <navigation v-else>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Profil</h2>
        </template>

        <template #main>
            <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black">
                <!-- Modern Header Section -->
                <div class="text-center mb-12 pt-8">
                    <div class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full mb-6">
                        <span class="text-white font-semibold text-sm uppercase tracking-wider">Profil</span>
                    </div>
                    <div class="relative">
                        <!-- Darker background for better contrast -->
                        <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-800 to-black rounded-3xl blur-sm"></div>
                        <div class="relative bg-gradient-to-br from-gray-800/90 via-gray-700/90 to-gray-900/90 rounded-3xl p-8 border border-gray-600/30 backdrop-blur-sm">
                            <h1 class="text-5xl md:text-6xl lg:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-gray-100 to-gray-200 leading-none tracking-tight drop-shadow-lg">
                                Profil
                            </h1>
                            <p class="text-gray-300 mt-4 text-lg font-medium">Upravljajte svojim nalogom i postavkama</p>
                        </div>
                    </div>
                </div>

                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                    <!-- Profile Information Card -->
                    <div class="relative overflow-hidden bg-gradient-to-br from-gray-800 via-gray-700 to-gray-800 rounded-2xl shadow-2xl border border-gray-600/30 backdrop-blur-sm">
                        <!-- Animated Background -->
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-indigo-500/10 to-purple-500/10 animate-pulse"></div>
                        <div class="relative p-8">
                            <UpdateProfileInformationForm
                                :must-verify-email="mustVerifyEmail"
                                :status="status"
                            />
                        </div>
                    </div>

                    <!-- Update Password Card -->
                    <div class="relative overflow-hidden bg-gradient-to-br from-gray-800 via-gray-700 to-gray-800 rounded-2xl shadow-2xl border border-gray-600/30 backdrop-blur-sm">
                        <!-- Animated Background -->
                        <div class="absolute inset-0 bg-gradient-to-r from-green-500/10 via-emerald-500/10 to-teal-500/10 animate-pulse"></div>
                        <div class="relative p-8">
                            <UpdatePasswordForm />
                        </div>
                    </div>

                    <!-- Delete Account Card -->
                    <div class="relative overflow-hidden bg-gradient-to-br from-gray-800 via-gray-700 to-gray-800 rounded-2xl shadow-2xl border border-gray-600/30 backdrop-blur-sm">
                        <!-- Animated Background -->
                        <div class="absolute inset-0 bg-gradient-to-r from-red-500/10 via-pink-500/10 to-rose-500/10 animate-pulse"></div>
                        <div class="relative p-8">
                            <DeleteUserForm />
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </navigation>
</template>

<style scoped>
/* Animation for cards appearing */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.relative.overflow-hidden {
    animation: fadeInUp 0.6s ease-out forwards;
    opacity: 0;
}

.relative.overflow-hidden:nth-child(1) { animation-delay: 0.1s; }
.relative.overflow-hidden:nth-child(2) { animation-delay: 0.2s; }
.relative.overflow-hidden:nth-child(3) { animation-delay: 0.3s; }

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
</style>
