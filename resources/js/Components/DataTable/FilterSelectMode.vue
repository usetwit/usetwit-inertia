<script setup>
import Select from '../Form/Select.vue';

const props = defineProps({
    type: {type: String, required: true},
});

const mode = defineModel();

const modeTexts = {
    contains: 'Contains',
    starts_with: 'Starts with',
    ends_with: 'Ends with',
    equals: 'Equals',
    not_equals: 'Does not equal',
    gt: 'Greater than',
    gte: 'Greater than or equal',
    lt: 'Less than',
    lte: 'Less than or equal',
    date_equals: 'Date equals',
    date_not_equals: 'Date does not equal',
    date_gt: 'Date greater than',
    date_gte: 'Date greater than or equal',
    date_lt: 'Date less than',
    date_lte: 'Date less than or equal',
};

const stringModes = [
    'contains',
    'starts_with',
    'ends_with',
    'equals',
    'not_equals',
    'gt',
    'gte',
    'lt',
    'lte',
];

const dateModes = [
    'date_equals',
    'date_not_equals',
    'date_gt',
    'date_gte',
    'date_lt',
    'date_lte',
];

const modes = {
    string: stringModes.map(mode => ({mode, label: modeTexts[mode]})),
    date: dateModes.map(mode => ({mode, label: modeTexts[mode]})),
};
</script>

<template>
    <Select :options="modes[type]"
            v-model="mode"
            option-label="label"
            option-value="mode"
            class="mt-1 text-sm"
            dropdown-classes="max-h-64"
    >
        <template #option="{ option }">
            <span class="text-sm">{{ option.label }}</span>
        </template>
    </Select>
</template>
