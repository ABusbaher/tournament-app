<script setup>
import {defineEmits, onMounted, ref, watch} from "vue";

const props = defineProps({
    labelName: {
        type: String,
    },
    helpText: {
        type: String
    },
});
const fileInput = ref(null);
const file = ref(null);
const imagePreview = ref('');
const showPreview = ref(false);

const emit = defineEmits(['update:modelValue']);

const fileChangeEmit = (event) => {
    file.value = event.target.files[0];
    if (file.value) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
            showPreview.value = true;
        };
        reader.readAsDataURL(file.value);
    } else {
        imagePreview.value = '';
        showPreview.value = false;
    }
    emit('update:modelValue', file.value);
}

</script>

<template>
    <div class="relative w-3/4">
        <p v-if="helpText" class="mt-1 text-center text-sm text-gray-500 dark:text-gray-300" id="file_input_help">{{ helpText }}</p>
        <input type="file" class="hidden" id="fileInput" ref="fileInput" @change="fileChangeEmit">
        <label
            for="fileInput"
            class="flex items-center justify-center w-full px-4 py-2 bg-gray-200 border border-gray-300 mb-4 rounded-md cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
            <span class="mr-2 text-gray-700">{{ labelName }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
            </svg>
        </label>
        <span v-if="file" class="whitespace-pre-wrap">New image if you save:<br> {{ file.name }}</span>
        <img v-show="showPreview"  :src="imagePreview"  alt="Team Image tmp" width="100" height="100"/>
    </div>
</template>
