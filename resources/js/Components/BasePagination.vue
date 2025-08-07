<script setup>
defineProps({
    currentPage: {
        type: Number,
        required: true,
    },
    from: {
        type: Number,
    },
    to: {
        type: Number,
    },
    total: {
        type: Number,
        required: true
    },
});
const emit = defineEmits(['emitPrevPage','emitNextPage']);
const prevPageEmit = (value) => {
    emit('emitPrevPage', value);
};
const nextPageEmit = (value) => {
    emit('emitNextPage', value);
};
</script>

<template>
    <!-- Modern Dark Pagination -->
    <div class="relative overflow-hidden bg-gradient-to-r from-gray-800 via-gray-700 to-gray-800 rounded-2xl shadow-2xl border border-gray-600/30 backdrop-blur-sm">
        <!-- Animated Background -->
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/10 via-cyan-500/10 to-blue-500/10 animate-pulse"></div>
        
        <div class="relative px-6 py-4">
            <!-- Mobile Pagination -->
            <div class="flex flex-1 justify-between sm:hidden">
                <button 
                    @click="prevPageEmit" 
                    class="relative inline-flex items-center rounded-lg border border-gray-600/50 bg-gray-700/50 px-4 py-2 text-sm font-medium text-gray-300 hover:bg-gray-600/50 hover:text-white transition-all duration-300 transform hover:scale-105"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Prethodna
                </button>
                <button 
                    @click="nextPageEmit" 
                    class="relative ml-3 inline-flex items-center rounded-lg border border-gray-600/50 bg-gray-700/50 px-4 py-2 text-sm font-medium text-gray-300 hover:bg-gray-600/50 hover:text-white transition-all duration-300 transform hover:scale-105"
                >
                    Sledeća
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>

            <!-- Desktop Pagination -->
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <!-- Info Section -->
                <div>
                    <p v-if="from" class="text-sm text-gray-300">
                        Prikazano
                        <span class="font-semibold text-white">{{ from }}</span>
                        do
                        <span class="font-semibold text-white">{{ to }}</span>
                        od
                        <span class="font-semibold text-white">{{ total }}</span>
                        rezultata
                    </p>
                    <p v-else class="text-sm text-gray-300">
                        Prikazano
                        <span class="font-semibold text-white">{{ currentPage }}</span> od
                        <span class="font-semibold text-white">{{ total }}</span>
                    </p>
                </div>

                <!-- Navigation -->
                <div>
                    <nav class="isolate inline-flex -space-x-px rounded-lg shadow-lg" aria-label="Pagination">
                        <!-- Previous Button -->
                        <button 
                            @click="prevPageEmit" 
                            class="relative inline-flex items-center rounded-l-lg px-3 py-2 text-gray-400 ring-1 ring-inset ring-gray-600/50 hover:bg-gray-700/50 hover:text-white focus:z-20 focus:outline-offset-0 transition-all duration-300 transform hover:scale-105"
                        >
                            <span class="sr-only">Prethodna</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <!-- Current Page -->
                        <button 
                            aria-current="page" 
                            class="relative z-10 inline-flex items-center bg-gradient-to-r from-indigo-600 to-cyan-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 shadow-lg"
                        >
                            {{ currentPage }}
                        </button>

                        <!-- Next Button -->
                        <button 
                            @click="nextPageEmit" 
                            class="relative inline-flex items-center rounded-r-lg px-3 py-2 text-gray-400 ring-1 ring-inset ring-gray-600/50 hover:bg-gray-700/50 hover:text-white focus:z-20 focus:outline-offset-0 transition-all duration-300 transform hover:scale-105"
                        >
                            <span class="sr-only">Sledeća</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom scrollbar for pagination */
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

/* Button hover effects */
button:hover {
    transform: translateY(-1px);
}

/* Animation for pagination appearing */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.relative {
    animation: fadeInUp 0.4s ease-out forwards;
}
</style>
