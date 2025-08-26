<script setup>
const props = defineProps({
    label: {type: String, default: null},
    id: {type: String, default: null},
});

const model = defineModel();
</script>

<template>
    <label class="inline-flex items-center hover:bg-gray-100 rounded-lg" :for="id">
        <input type="checkbox"
               v-model="model"
               :name="id"
               :id="id"
               :indeterminate="typeof !!model !== 'boolean'"
               class="select-none form-checkbox h-4 w-4 accent-teal-600 rounded-md hover:accent-teal-600 hover:ring-3 hover:ring-gray-500 hover:ring-opacity-25 focus:ring-3 focus:ring-teal-500 focus:ring-opacity-25">
        <slot><span class="ml-2 pr-1 text-gray-700">{{ label }}</span></slot>
    </label>
</template>
