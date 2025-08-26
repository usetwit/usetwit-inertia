<script setup>
import Select from '../Form/Select.vue';

const model = defineModel();

const options = [
    {label: 'Match All', value: 'and'},
    {label: 'Match Any', value: 'or'},
];
</script>

<template>
    <Select :options="options"
            v-model="model"
            option-label="label"
            option-value="value"
            class="text-sm"
    >
        <template #option="{ option }">
            <span class="text-sm">{{ option.label }}</span>
        </template>
    </Select>
</template>
