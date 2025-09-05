<script setup>
import {computed, useTemplateRef} from 'vue';

const props = defineProps({
    disabled: {type: Boolean, default: false},
    invalid: {type: Boolean, default: false},
    rounded: {type: Boolean, default: false},
    showErrors: {type: Boolean, default: false},
    type: {type: String, default: 'text'},
    errors: {type: Object, default: () => ({})},
    name: {type: String, default: ''},
});

defineOptions({
    inheritAttrs: false,
});

const hasError = computed(() => !!props.errors?.[props.name]);

const setClasses = computed(() => {
    const disabled = 'bg-gray-100 dark:bg-gray-600 border-gray-400 cursor-not-allowed text-gray-400';
    const invalid = 'bg-white dark:bg-gray-900 border-red-600 focus:outline-red-600/50 hover:border-red-500 text-gray-800 dark:text-gray-300';
    const normal = 'bg-white dark:bg-gray-900 border-gray-300 hover:border-gray-400 focus:outline-slate-400/50 text-gray-800 dark:text-gray-300';

    return props.disabled ? disabled : (props.invalid || hasError.value) ? invalid : normal;
});

const model = defineModel();

const inputElement = useTemplateRef('input');

defineExpose({
    inputElement,
});
</script>

<template>
    <input :type="type"
           :name="name"
           class="ring-0 outline-offset-0 focus:outline focus:invalid:outline-red-600/50 hover:invalid:border-red-500 invalid:border-red-600 focus:invalid:border-red-600 leading-5 border align-middle py-2 px-2"
           :class="[{'rounded-md': rounded}, setClasses]"
           :disabled="disabled"
           v-model="model"
           ref="input"
           v-bind="$attrs"
    >
    <p v-if="hasError && showErrors" class="input-error-msg">
        {{ errors[name] }}
    </p>
</template>
