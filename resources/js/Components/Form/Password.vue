<script setup>
import useDropdown from '@/composables/useDropdown.js';
import {computed, onMounted, ref, useTemplateRef} from 'vue';
import InputText from './InputText.vue';
import zxcvbn from 'zxcvbn';
import InputGroup from './InputGroup.vue';

defineOptions({
    inheritAttrs: false,
});

const {
    inputRef,
    dropdownStyle,
    showDropdown,
} = useDropdown('left', 'bottom', true);

const model = defineModel({type: [String, null]});
const inputTextRef = useTemplateRef('inputText');

onMounted(() => {
    if (inputTextRef.value?.inputElement instanceof HTMLElement) {
        inputRef.value = inputTextRef.value.inputElement;
    }
});

const type = ref('password');
const strength = computed(() => zxcvbn(model.value || '').score);
const width = computed(() => {
    return model.value === '' || model.value === null
        ? '0px'
        : ((strength.value + 1) / 5 * 100) + '%';
});
const texts = ['Very weak', 'Weak', 'Fair', 'Good', 'Strong'];
const strengthText = computed(() => !model.value ? 'Enter a password' : texts[strength.value]);
</script>

<template>
    <InputGroup>
        <InputText v-model="model"
                   :type="type"
                   placeholder="••••••••"
                   ref="inputText"
                   v-bind="$attrs"
                   @focus="showDropdown = true"
                   @blur="showDropdown = false"
        />
        <button class="inline-flex bg-gray-200 hover:bg-gray-100 text-gray-700 items-center align-middle px-2"
                type="button"
                @click="type = type === 'text' ? 'password' : 'text'"
        >
            <i v-if="type === 'password'" class="pi pi-eye"></i>
            <i v-if="type === 'text'" class="pi pi-eye-slash"></i>
        </button>
    </InputGroup>

    <Teleport to="body" v-if="showDropdown">
        <div ref="dropdownRef"
             class="dropdown z-350 max-h-60"
             :style="dropdownStyle"
        >
            <div class="bg-gray-200 rounded-full h-4 m-2">
                <div :style="{'width': width}"
                     class="rounded-full h-4"
                     :class="{'bg-red-500': strength < 2, 'bg-amber-500': strength < 4, 'bg-green-500': strength === 4}"
                ></div>
            </div>
            <div class="mx-2 mb-2">{{ strengthText }}<i v-if="strength === 4" class="pi pi-face-smile ml-2"></i></div>
        </div>
    </Teleport>
</template>
