<script setup>
import {computed} from 'vue';

const props = defineProps({
    loading: {type: Boolean, default: false},
    disabled: {type: Boolean, default: false},
    icon: {type: String},
    label: {type: String},
    variant: {
        type: String, default: 'primary', validator(value) {
            return ['primary', 'secondary', 'success', 'warning', 'danger', 'login'].includes(value);
        },
    },
    border: {type: Boolean, default: false},
    badge: {type: [String, Number]},
    size: {
        type: String, default: 'md', validator(value) {
            return ['sm', 'md', 'lg'].includes(value);
        },
    },
    type: {type: String, default: 'button'},
});

const variantClasses = {
    normal: {
        normal: {
            primary: 'text-white bg-slate-800 border-slate-800 hover:bg-slate-700 hover:border-slate-700 dark:hover:bg-slate-100 dark:hover:border-slate-100 dark:bg-slate-200 dark:border-slate-200 dark:text-slate-900',
            secondary: 'text-white bg-gray-600 border-gray-600 hover:bg-gray-500 hover:border-gray-500',
            success: 'text-white bg-green-500 border-green-500 hover:bg-green-400 hover:border-green-400',
            warning: 'text-white bg-yellow-500 border-yellow-500 hover:bg-yellow-400 hover:border-yellow-400',
            danger: 'text-white bg-red-500 border-red-500 hover:bg-red-400 hover:border-red-400',
            login: 'text-white bg-teal-500 border-teal-500 hover:bg-teal-400 hover:border-teal-400',
        },
        loading: {
            primary: 'text-white bg-slate-700 border-slate-700 dark:bg-slate-200 dark:border-slate-200 dark:text-slate-900',
            secondary: 'text-white bg-gray-500 border-gray-500',
            success: 'text-white bg-green-400 border-green-400',
            warning: 'text-white bg-yellow-400 border-yellow-400',
            danger: 'text-white bg-red-400 border-red-400',
            login: 'text-white bg-teal-400 border-teal-400',
        },
    },

    border: {
        normal: {
            primary: 'dark:text-slate-50 text-slate-600 bg-slate-100 border-slate-600 hover:bg-slate-50 dark:bg-slate-700 dark:hover:bg-slate-600 dark:border-slate-400',
            secondary: 'dark:text-gray-50 text-gray-600 bg-gray-100 border-gray-600 hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-400',
            success: 'dark:text-green-50 text-green-600 bg-green-100 border-green-600 hover:bg-green-50 dark:bg-green-700 dark:hover:bg-green-600 dark:border-green-400',
            warning: 'dark:text-yellow-50 text-yellow-600 bg-yellow-100 border-yellow-600 hover:bg-yellow-50 dark:bg-yellow-700 dark:hover:bg-yellow-600 dark:border-yellow-400',
            danger: 'dark:text-red-50 text-red-600 bg-red-100 border-red-600 hover:bg-red-50 dark:bg-red-700 dark:hover:bg-red-600 dark:border-red-400',
        },
        loading: {
            primary: 'dark:text-slate-50 text-slate-600 bg-slate-50 border-slate-600 dark:bg-slate-600 dark:border-slate-400',
            secondary: 'dark:text-gray-50 text-gray-600 bg-gray-50 border-gray-600 dark:bg-gray-600 dark:border-gray-400',
            success: 'dark:text-green-50 text-green-600 bg-green-50 border-green-600 dark:bg-green-600 dark:border-green-400',
            warning: 'dark:text-yellow-50 text-yellow-600 bg-yellow-50 border-yellow-600 dark:bg-yellow-600 dark:border-yellow-400',
            danger: 'dark:text-red-50 text-red-600 bg-red-50 border-red-600 dark:bg-red-600 dark:border-red-400',
        },
    },
};

const setClasses = computed(() => {
    if (props.border) {
        return props.loading ?
            `${variantClasses.border.loading[props.variant]} cursor-not-allowed` :
            variantClasses.border.normal[props.variant];
    } else {
        return props.loading ?
            `${variantClasses.normal.loading[props.variant]} cursor-not-allowed` :
            variantClasses.normal.normal[props.variant];
    }
});

const setSizeClasses = computed(() => {
    if (props.size === 'sm') {
        return 'text-sm px-1.5 py-1';
    } else if (props.size === 'md') {
        return 'px-3 py-1.5';
    }

    return 'p-2.5';
});
</script>

<template>
    <button class="border rounded-lg inline-flex justify-center items-center align-middle"
            :class="[setClasses, setSizeClasses]"
            :disabled="disabled || loading"
            :aria-disabled="disabled || loading"
            :type="type"
    >
        <i v-if="loading" class="pi pi-spin pi-spinner" :class="{'mr-1': label || $slots.default}"></i>
        <i v-if="icon && !loading" :class="[{'mr-1': label || $slots.default}, icon]"></i>
        <span v-if="label">{{ label }}</span>
        <span v-if="$slots.default">
            <slot/>
        </span>
        <span v-if="badge"
              class="ml-2 flex items-center justify-center h-4 p-1 min-w-4 bg-slate-500 text-white text-xs rounded-full"
        >
            {{ badge }}
        </span>
    </button>
</template>
