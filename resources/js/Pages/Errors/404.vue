<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black">
        <!-- Navigation -->
        <AdminNavigation v-if="isAdmin" />
        <Navigation v-else />
        
        <!-- 404 Content -->
        <div class="flex items-center justify-center px-4 min-h-[calc(100vh-4rem)]">
            <div class="text-center">
                <!-- Logo -->
                <div class="mb-8 flex justify-center">
                    <ApplicationLogo class="h-32 md:h-40 lg:h-48 w-auto" />
                </div>
                
                <!-- Error Message -->
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4">
                    Tražena stranica ne postoji
                </h1>
                
                <!-- Home Link -->
                <div class="mt-8">
                    <Link 
                        :href="route('tournament.all')" 
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl border border-blue-500/30 hover:border-blue-400/50"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Nazad na početnu stranicu
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Navigation from '@/Layouts/Navigation.vue';
import AdminNavigation from '@/Layouts/AdminNavigation.vue';
import { Link } from '@inertiajs/vue3';

const user = computed(() => {
    return window.Laravel?.user || usePage()?.props?.auth?.user;
});

const isAdmin = computed(() => {
    return user.value && (user.value.role === 'admin' || user.value.isAdmin?.());
});
</script>
