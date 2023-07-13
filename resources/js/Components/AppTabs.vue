<script setup>
import {getCurrentInstance, ref} from 'vue';

const props = defineProps({
    tabList: {
        type: Array,
        required: true,
    },
    variant: {
        type: String,
        required: false,
        default: 'vertical',
        validator: (value) => ['horizontal', 'vertical'].includes(value),
    },
});

const instance = getCurrentInstance();
const uid = ref(instance.uid);
const activeTab = ref(1);
</script>

<template>
    <div :class="{ 'flex space-x-4': variant === 'horizontal' }">
        <ul
            class="list-none bg-blue-900 bg-opacity-30 p-1.5 rounded-lg text-center overflow-auto whitespace-nowrap"
            :class="{'flex items-center mb-6': variant === 'vertical' }"
        >
            <li
                v-for="(tab, index) in tabList"
                :key="index"
                class="w-full px-4 py-1.5 rounded-lg"
                :class="{ 'text-blue-600 bg-white shadow-xl': index + 1 === activeTab, 'text-white': index + 1 !== activeTab }"
            >
                <label :for="`${uid}${index}`" class="cursor-pointer block">
                    {{ tab }}
                </label>
                <input
                    :id="`${uid}${index}`"
                    type="radio"
                    :name="`${uid}-tab`"
                    :value="index + 1"
                    v-model="activeTab"
                    class="hidden"
                />
            </li>
        </ul>
        <template v-for="(tab, index) in tabList">
            <div
                :key="index"
                v-if="index + 1 === activeTab"
                class="flex-grow bg-white rounded-lg shadow-xl p-4"
            >
                <slot :name="`tabPanel-${index + 1}`" />
            </div>
        </template>
    </div>
</template>
