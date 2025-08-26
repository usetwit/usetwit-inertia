<script setup>
import useDropdown from '../../composables/useDropdown.js';
import {computed, toRaw} from 'vue';
import {isEqual} from 'lodash';

const props = defineProps({
    placeholder: {type: String, default: 'Select an option'},
    options: {type: Array, required: true},
    optionLabel: {type: String, required: true},
    optionValue: {type: String, default: null},
    loading: {type: Boolean, default: false},
    disabled: {type: Boolean, default: false},
    invalid: {type: Boolean, default: false},
    showClear: {type: Boolean, default: false},
    dropdownClass: {type: String},
});

const model = defineModel();
const emit = defineEmits(['selected']);

defineOptions({
    inheritAttrs: false,
});

const text = computed(() => {
    let foundOption;

    if (typeof model.value === 'object') {
        foundOption = props.options.find(option => isEqual(option, toRaw(model.value)));
    } else if (['string', 'number'].includes(typeof model.value)) {
        foundOption = props.options.find(option => option[props.optionValue] === model.value);
    }

    return foundOption ? foundOption[props.optionLabel] : props.placeholder;
});

const {
    inputRef,
    dropdownStyle,
    showDropdown,
} = useDropdown('left', 'bottom', true);

const optionSelected = option => {
    if (props.optionValue) {
        model.value = option[props.optionValue];
    } else {
        model.value = option;
    }

    showDropdown.value = false;
    emit('selected', option);
};

const clear = () => {
    showDropdown.value = false;
    model.value = null;
};

const toggleDropdown = () => {
    showDropdown.value = !props.loading && !props.disabled && showDropdown.value === false;
};

const setClasses = computed(() => {
    const disabled = 'bg-gray-100 dark:bg-gray-600 border-gray-400 cursor-not-allowed';
    const invalid = 'bg-white dark:bg-gray-900 border-red-600 focus:outline-red-600/50 hover:border-red-500';
    const normal = 'bg-white dark:bg-gray-900 border-gray-300 hover:border-gray-400 focus:outline-slate-400/50';

    return props.disabled ? disabled : props.invalid ? invalid : normal;
});
</script>

<template>
    <div @click="toggleDropdown"
         class="inline-flex cursor-pointer bg-white rounded-md leading-5 border align-middle px-1 py-0.5 text-gray-800 dark:text-gray-300"
         :class="setClasses"
         ref="inputRef"
         v-bind="$attrs"
    >
        <span class="px-2 py-1.5 flex-1 select-none">{{ text }}</span>
        <span v-if="showClear && model && !loading" @click.stop="clear" class="inline-flex items-center p-2">
            <i class="pi pi-times"></i>
        </span>
        <span v-if="!loading" class="inline-flex items-center p-2"><i class="pi pi-angle-down"></i></span>
        <span v-if="loading" class="inline-flex items-center p-2"><i class="pi pi-spinner pi-spin"></i></span>
    </div>

    <Teleport to="body" v-if="showDropdown">
        <div ref="dropdownRef"
             class="dropdown z-1050 max-h-60"
             :class="dropdownClass"
             :style="dropdownStyle"
        >
            <ul v-if="options.length">
                <li v-for="option in options"
                    @click.stop="optionSelected(option)"
                    class="select-none flex cursor-pointer hover:bg-gray-100 text-gray-700 items-center px-2 py-1.5 rounded-sm text-nowrap"
                >
                    <slot name="option" :option="option">
                        {{ option[optionLabel] }}
                    </slot>
                </li>
            </ul>
        </div>
    </Teleport>
</template>
