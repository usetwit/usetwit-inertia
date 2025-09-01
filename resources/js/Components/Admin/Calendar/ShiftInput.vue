<script setup>
import {computed, ref, watch} from 'vue';
import Button from '@/components/Form/Button.vue';
import InputText from '@/components/Form/InputText.vue';
import {toast} from 'vue3-toastify';

const props = defineProps({
    date: {
        type: [Object, null],
        required: true,
    },
});

const loading = defineModel('loading', {
    type: Boolean,
    default: false,
});

const emit = defineEmits(['updateShifts']);

const localDate = ref({
    'nwd': props.date.nwd,
    'shift1_start': props.date.shift1_start,
    'shift1_end': props.date.shift1_end,
    'shift2_start': props.date.shift2_start,
    'shift2_end': props.date.shift2_end,
    'shift3_start': props.date.shift3_start,
    'shift3_end': props.date.shift3_end,
    'shift4_start': props.date.shift4_start,
    'shift4_end': props.date.shift4_end,
});

const isModified = computed(() => {
    return localDate.value.nwd !== false
        || localDate.value.shift1_start !== '00:00'
        || localDate.value.shift1_end !== '00:00'
        || localDate.value.shift2_start !== ''
        || localDate.value.shift2_end !== ''
        || localDate.value.shift3_start !== ''
        || localDate.value.shift3_end !== ''
        || localDate.value.shift4_start !== ''
        || localDate.value.shift4_end !== '';
});

const submitShifts = () => {
    if (localDate.value.nwd) {
        localDate.value.shift1_start = '00:00';
        localDate.value.shift1_end = '00:00';
        localDate.value.shift2_start = '';
        localDate.value.shift2_end = '';
        localDate.value.shift3_start = '';
        localDate.value.shift3_end = '';
        localDate.value.shift4_start = '';
        localDate.value.shift4_end = '';

    } else if (errors.value.shift1 || errors.value.shift2 || errors.value.shift3 || errors.value.shift4) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            group: 'br',
            detail: 'Please correct all errors',
            life: 3000,
        });
        return;
    }

    emit('updateShifts', localDate.value);
};

const errors = computed(() => {
    let shift1 = null;
    let shift2 = null;
    let shift3 = null;
    let shift4 = null;
    let regex = /^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/;

    if (!localDate.value.nwd) {
        if (localDate.value.shift1_start && !regex.test(localDate.value.shift1_start)) {
            shift1 = 'Start must be a valid time';
        } else if (localDate.value.shift1_end && !regex.test(localDate.value.shift1_end)) {
            shift1 = 'End must be a valid time';
        } else if (localDate.value.shift1_end !== '00:00' && localDate.value.shift1_end <= localDate.value.shift1_start) {
            shift1 = 'End must be greater than start';
        }

        if (localDate.value.shift1_end && !localDate.value.shift1_start) {
            shift1 = 'Start must be entered';
        }

        if (localDate.value.shift2_start) {
            if (localDate.value.shift1_end === '00:00') {
                shift2 = '24 hours have been specified';
            } else if (localDate.value.shift2_start && !regex.test(localDate.value.shift2_start)) {
                shift2 = 'Start must be a valid time';
            } else if (localDate.value.shift2_end && !regex.test(localDate.value.shift2_end)) {
                shift2 = 'End must be a valid time';
            } else if (!localDate.value.shift1_start || !localDate.value.shift1_end) {
                shift2 = 'Previous shifts must be specified';
            } else if (localDate.value.shift2_start <= localDate.value.shift1_end) {
                shift2 = 'Start must be greater than Shift 1 end';
            } else if (localDate.value.shift2_end !== '00:00' && (localDate.value.shift2_end <= localDate.value.shift2_start || !localDate.value.shift2_end)) {
                shift2 = 'End must be greater than start';
            }
        }

        if (localDate.value.shift2_end && !localDate.value.shift2_start) {
            shift2 = 'Start must be entered';
        }

        if (localDate.value.shift3_start) {
            if (localDate.value.shift2_end === '00:00') {
                shift3 = '24 hours have been specified';
            } else if (localDate.value.shift2_end === '00:00') {
                shift3 = '24 hours have been specified';
            } else if (localDate.value.shift3_start && !regex.test(localDate.value.shift3_start)) {
                shift3 = 'Start must be a valid time';
            } else if (localDate.value.shift3_end && !regex.test(localDate.value.shift3_end)) {
                shift3 = 'End must be a valid time';
            } else if (localDate.value.shift2_end === '00:00') {
                shift3 = '24 hours have been specified';
            } else if (!localDate.value.shift2_start || !localDate.value.shift2_end) {
                shift3 = 'Previous shifts must be specified';
            } else if (localDate.value.shift3_start <= localDate.value.shift2_end) {
                shift3 = 'Start must be greater than Shift 2 end';
            } else if (localDate.value.shift3_end !== '00:00' && (localDate.value.shift3_end <= localDate.value.shift3_start || !localDate.value.shift3_end)) {
                shift3 = 'End must be greater than start';
            }
        }

        if (localDate.value.shift3_end && !localDate.value.shift3_start) {
            shift3 = 'Start must be entered';
        }

        if (localDate.value.shift4_start) {
            if (localDate.value.shift3_end === '00:00') {
                shift4 = '24 hours have been specified';
            } else if (localDate.value.shift4_start && !regex.test(localDate.value.shift4_start)) {
                shift4 = 'Start must be a valid time';
            } else if (localDate.value.shift4_end && !regex.test(localDate.value.shift4_end)) {
                shift4 = 'End must be a valid time';
            } else if (localDate.value.shift3_end === '00:00') {
                shift4 = '24 hours have been specified';
            } else if (!localDate.value.shift3_start || !localDate.value.shift3_end) {
                shift4 = 'Previous shifts must be specified';
            } else if (localDate.value.shift4_start <= localDate.value.shift3_end) {
                shift4 = 'Start must be greater than Shift 3 end';
            } else if (localDate.value.shift4_end !== '00:00' && (localDate.value.shift4_end <= localDate.value.shift4_start || !localDate.value.shift4_end)) {
                shift4 = 'End must be greater than start';
            }
        }

        if (localDate.value.shift4_end && !localDate.value.shift4_start) {
            shift4 = 'Start must be entered';
        }
    }

    return {
        shift1: shift1,
        shift2: shift2,
        shift3: shift3,
        shift4: shift4,
    };
});

watch(() => props.date, (newValue, oldValue) => {
    if (newValue.id !== oldValue.id) {
        localDate.value = {
            nwd: newValue.nwd,
            shift1_start: newValue.shift1_start,
            shift1_end: newValue.shift1_end,
            shift2_start: newValue.shift2_start,
            shift2_end: newValue.shift2_end,
            shift3_start: newValue.shift3_start,
            shift3_end: newValue.shift3_end,
            shift4_start: newValue.shift4_start,
            shift4_end: newValue.shift4_end,
        };
    }
});

const setTo24Hours = () => {
    localDate.value = {
        nwd: false,
        shift1_start: '00:00',
        shift1_end: '00:00',
        shift2_start: '',
        shift2_end: '',
        shift3_start: '',
        shift3_end: '',
        shift4_start: '',
        shift4_end: '',
    };
};
</script>

<template>
    <div class="ml-4 w-60">

        <form method="post" @submit.prevent="submitShifts">


            <div class="flex items-center justify-center">
                <h2 class="text-slate-700 text-xl">Shifts</h2>
            </div>


            <div
                class="p-2 bg-gray-50 rounded-sm border border-gray-200 text-sm text-center items-center flex justify-between"
            >
                <label for="nwd">Non-Working Day</label>
                <div class="checkbox-wrapper">
                    <input type="checkbox" class="checkbox-toggle" id="nwd" name="nwd" v-model="localDate.nwd">
                </div>
            </div>

            <div
                class="p-2 bg-gray-50 rounded-sm border border-gray-200 flex mt-2"
            >
                <Button @click="setTo24Hours"
                        label="Set to 24 hours"
                        class="mx-auto"
                        size="sm"
                        :loading="loading"
                        :disabled="!isModified"
                />
            </div>


            <div class="p-2 bg-gray-50 rounded-sm border border-gray-200 text-sm text-center mt-2 text-gray-800">
                Shift 1

                <div class="flex items-center justify-center">
                    <InputText name="shift1_start"
                               pattern="^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$"
                               minlength="5"
                               maxlength="5"
                               class="w-[60px] p-1! text-center rounded-md"
                               :invalid="errors.shift1 !== null"
                               v-model="localDate.shift1_start"
                               :disabled="localDate.nwd"
                               :required="!localDate.nwd
                           || localDate.shift1_end !== ''
                           || localDate.shift2_start !== ''
                           || localDate.shift2_end !== ''
                           || localDate.shift3_start !== ''
                           || localDate.shift3_end !== ''
                           || localDate.shift4_start !== ''
                           || localDate.shift4_end !== ''"

                    />
                    <span class="mx-2">to</span>
                    <InputText name="shift1_end"
                               pattern="^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$"
                               minlength="5"
                               maxlength="5"
                               class="w-[60px] p-1! text-center rounded-md"
                               :invalid="errors.shift1 !== null"
                               v-model="localDate.shift1_end"
                               :disabled="localDate.nwd"
                               :required="!localDate.nwd
                           || localDate.shift1_start !== ''
                           || localDate.shift2_start !== ''
                           || localDate.shift2_end !== ''
                           || localDate.shift3_start !== ''
                           || localDate.shift3_end !== ''
                           || localDate.shift4_start !== ''
                           || localDate.shift4_end !== ''"
                    />
                </div>

                <span v-if="errors.shift1" class="text-red-500 text-xs">{{ errors.shift1 }}</span>
            </div>

            <div class="p-2 bg-gray-50 rounded-sm border border-gray-200 text-sm text-center mt-2 text-gray-800">
                <span>Shift 2</span>

                <div class="flex items-center justify-center">
                    <InputText name="shift2_start"
                               pattern="^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$"
                               minlength="5"
                               maxlength="5"
                               class="w-[60px] p-1! text-center rounded-md"
                               :invalid="errors.shift2 !== null"
                               v-model="localDate.shift2_start"
                               :disabled="localDate.nwd"
                               :required="localDate.shift2_end !== ''
                           || localDate.shift3_start !== ''
                           || localDate.shift3_end !== ''
                           || localDate.shift4_start !== ''
                           || localDate.shift4_end !== ''"
                    />
                    <span class="mx-2">to</span>
                    <InputText name="shift2_end"
                               pattern="^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$"
                               minlength="5"
                               maxlength="5"
                               class="w-[60px] p-1! text-center rounded-md"
                               :invalid="errors.shift2 !== null"
                               v-model="localDate.shift2_end"
                               :disabled="localDate.nwd"
                               :required="localDate.shift2_start !== ''
                           || localDate.shift3_start !== ''
                           || localDate.shift3_end !== ''
                           || localDate.shift4_start !== ''
                           || localDate.shift4_end !== ''"
                    />
                </div>

                <span v-if="errors.shift2" class="text-red-500 text-xs">{{ errors.shift2 }}</span>
            </div>

            <div class="p-2 bg-gray-50 rounded-sm border border-gray-200 text-sm text-center mt-2 text-gray-800">
                <span>Shift 3</span>

                <div class="flex items-center justify-center">
                    <InputText name="shift3_start"
                               pattern="^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$"
                               minlength="5"
                               maxlength="5"
                               class="w-[60px] p-1! text-center rounded-md"
                               :invalid="errors.shift3 !== null"
                               v-model="localDate.shift3_start"
                               :disabled="localDate.nwd"
                               :required="localDate.shift3_end !== ''
                           || localDate.shift4_start !== ''
                           || localDate.shift4_end !== ''"
                    />
                    <span class="mx-2">to</span>
                    <InputText name="shift3_end"
                               pattern="^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$"
                               minlength="5"
                               maxlength="5"
                               class="w-[60px] p-1! text-center rounded-md"
                               :invalid="errors.shift3 !== null"
                               v-model="localDate.shift3_end"
                               :disabled="localDate.nwd"
                               :required="localDate.shift3_start !== ''
                           || localDate.shift4_start !== ''
                           || localDate.shift4_end !== ''"
                    />
                </div>

                <span v-if="errors.shift3" class="text-red-500 text-xs">{{ errors.shift3 }}</span>
            </div>

            <div class="p-2 bg-gray-50 rounded-sm border border-gray-200 text-sm text-center mt-2 text-gray-800">
                <span>Shift 4</span>

                <div class="flex items-center justify-center">
                    <InputText name="shift4_start"
                               pattern="^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$"
                               minlength="5"
                               maxlength="5"
                               class="w-[60px] p-1! text-center rounded-md"
                               :invalid="errors.shift4 !== null"
                               v-model="localDate.shift4_start"
                               :disabled="localDate.nwd"
                               :required="localDate.shift4_end !== ''"
                    />
                    <span class="mx-2">to</span>
                    <InputText name="shift4_end"
                               pattern="^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$"
                               minlength="5"
                               maxlength="5"
                               class="w-[60px] p-1! text-center rounded-md"
                               :invalid="errors.shift4 !== null"
                               v-model="localDate.shift4_end"
                               :disabled="localDate.nwd"
                               :required="localDate.shift4_start !== ''"
                    />
                </div>

                <span v-if="errors.shift4" class="text-red-500 text-xs">{{ errors.shift4 }}</span>
            </div>

            <div class="text-center">
                <Button severity="success"
                        type="submit"
                        aria-label="Apply"
                        :loading="loading"
                        :disabled="loading"
                        label="Apply"
                        icon="pi pi-save"
                        class="mx-auto mt-2"
                />
            </div>
        </form>
    </div>
</template>
