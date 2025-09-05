<script setup>
import useDropdown from '@/composables/useDropdown';
import useDates from '@/composables/useDates';
import {computed, onMounted, ref, useTemplateRef, watch} from 'vue';
import InputGroup from '@/components/Form/InputGroup.vue';
import InputText from '@/components/Form/InputText.vue';
import DatepickerHeader from '@/components/Form/DatepickerHeader.vue';
import {DateTime} from 'luxon';
import {usePage} from '@inertiajs/vue3';

const props = defineProps({
    disabled: {type: Boolean, default: false},
    required: {type: Boolean, default: false},
    dropdown: {type: Boolean, default: false},
    numberOfMonths: {type: Number, default: 1},
    containerClass: {type: [String, Object]},
    positionY: {type: String, default: 'bottom'},
    positionX: {type: String, default: 'center'},
});

defineOptions({
    inheritAttrs: false,
});

const model = defineModel();
const page = usePage();
const dateSettings = computed(() => page.props.dateSettings);

let initialTextValue = '';
let initialDate = DateTime.utc();

if (model.value) {
    const test = DateTime.fromFormat(model.value, 'yyyy-MM-dd');

    if (test.isValid) {
        initialDate = test;
        initialTextValue = initialDate.toFormat(dateSettings.value.format.replace(/-/g, dateSettings.value.separator));
    }
}

const inputModel = ref(initialTextValue);
const mode = ref('day');

const {
    isToday,
    month,
    year,
    changeMonth,
    monthTexts,
    dayTexts,
    months,
    changeYear,
    yearRounded,
    minYear,
    maxYear,
} = useDates(initialDate);

const {
    inputRef,
    dropdownStyle,
    showDropdown,
    toggleDropdown,
    updateDropdownPosition,
} = useDropdown(props.positionX, props.positionY);

const inputTextRef = useTemplateRef('inputTextComponent');

onMounted(() => {
    if (inputTextRef.value?.inputElement instanceof HTMLElement) {
        inputRef.value = inputTextRef.value.inputElement;
    }
});

const changeMode = newMode => {
    mode.value = newMode;
    updateDropdownPosition();
};

const selectMonth = newMonth => {
    month.value = newMonth;
    changeMode('day');
};

const selectYear = newYear => {
    year.value = newYear;
    changeMode('month');
};

const setDate = day => {
    const selectedDate = DateTime.utc(day.year, day.month, day.day);

    inputModel.value = selectedDate.toFormat(dateSettings.value.format.replace(/-/g, dateSettings.value.separator));
    showDropdown.value = false;
};

const isSelected = day => {
    if (!inputModel.value) {
        return false;
    }

    const parsedDate = DateTime.fromFormat(inputModel.value.replace(/[./]/g, '-'), dateSettings.value.format);

    return parsedDate.isValid && parsedDate.hasSame(day, 'day');
};

watch(inputModel, (newValue) => {
    const formattedValue = newValue.replace(/[./]/g, '-');

    const parsedDate = DateTime.fromFormat(formattedValue, dateSettings.value.format.replace(/[./]/g, '-'));

    if (parsedDate.isValid) {
        model.value = parsedDate.toFormat('yyyy-MM-dd');
        year.value = parsedDate.year;
        month.value = parsedDate.month;
        inputRef.value.setCustomValidity('');
    } else {
        model.value = null;

        if (newValue) {
            inputRef.value.setCustomValidity('Please specify a valid date');
        }
    }
});
</script>

<template>
    <input type="hidden" :name="$attrs.name" :value="model ?? ''">

    <InputGroup :class="containerClass">
        <InputText ref="inputTextComponent"
                   v-bind="{ ...$attrs, name: undefined }"
                   v-model="inputModel"
                   :disabled="disabled"
                   :aria-disabled="disabled"
                   :required="required"
                   :aria-required="required"
                   :placeholder="dateSettings.display"
                   :pattern="dateSettings.regex"
                   @focus="showDropdown = true"
        />
        <button v-if="dropdown"
                class="inline-flex bg-gray-200 hover:bg-gray-100 text-gray-700 items-center align-middle px-2"
                @click="toggleDropdown"
                type="button"
                ref="buttonRef"
        >
            <i class="pi pi-calendar"></i>
        </button>
    </InputGroup>

    <Teleport to="body" v-if="showDropdown">
        <div ref="dropdownRef"
             class="dropdown z-250 min-w-[250px]"
             :style="dropdownStyle"
        >
            <div v-if="mode === 'year'">
                <DatepickerHeader @change="direction => changeYear(direction, 10)">
                    <span class="mx-4 p-1">{{ yearRounded }} - {{ yearRounded + 9 }}</span>
                </DatepickerHeader>

                <div class="grid grid-cols-2 w-full gap-1">
                    <button v-for="i in 10"
                            :key="i"
                            class="px-1.5 py-0.5 text-sm rounded-sm hover:bg-gray-100 text-gray-800"
                            @click.stop="selectYear(yearRounded + i - 1)"
                    >
                        {{ yearRounded + i - 1 }}
                    </button>
                </div>
            </div>

            <div v-if="mode === 'month'">
                <DatepickerHeader @change="direction => changeYear(direction)">
                    <button type="button" class="p-1 hover:bg-gray-100 rounded-sm" @click.stop="changeMode('year')">
                        {{ year }}
                    </button>
                </DatepickerHeader>

                <div class="grid grid-cols-3 w-full gap-1">
                    <button v-for="i in 12"
                            :key="i"
                            class="px-1.5 py-0.5 text-sm rounded-sm hover:bg-gray-100 text-gray-800"
                            @click.stop="selectMonth(i)"
                    >
                        {{ monthTexts[i - 1].substring(0, 3) }}
                    </button>
                </div>
            </div>

            <div v-else-if="mode === 'day'" class="month-wrapper">
                <div v-for="(month, monthIndex) in months" :key="monthIndex">

                    <DatepickerHeader v-if="monthIndex === 0" @change="direction => changeMonth(direction)">
                        <div>
                            <button type="button" class="mr-2 p-1 rounded-sm hover:bg-gray-100"
                                    @click.stop="changeMode('month')">
                                {{ monthTexts[month.month - 1] }}
                            </button>
                            <button type="button" class="p-1 rounded-sm hover:bg-gray-100"
                                    @click.stop="changeMode('year')">
                                {{ year }}
                            </button>
                        </div>
                    </DatepickerHeader>

                    <DatepickerHeader v-else :buttons="false">
                        <span class="mr-2 p-1">
                            {{ monthTexts[month.month - 1] }}
                        </span>
                        <span class="p-1">
                            {{ month.year }}
                        </span>
                    </DatepickerHeader>

                    <div class="grid grid-cols-7 w-full gap-1">
                        <span v-for="(day, dayIndex) in dayTexts" class="font-bold text-xs text-center text-gray-600"
                              :key="dayIndex">
                            {{ day }}
                        </span>
                        <template v-for="day in month.dates" :key="day">
                            <button v-if="day.month === month.month && day.year >= minYear && day.year <= maxYear"
                                    class="text-gray-800 p-1.5 text-xs flex items-center justify-center rounded-full hover:bg-gray-100"
                                    :class="{'bg-gray-200': isToday(day) && !isSelected(day), 'bg-blue-200': isSelected(day)}"
                                    @click.stop="setDate(day)"
                            >
                                {{ day.day }}
                            </button>
                            <span v-else class="text-gray-300 p-1.5 text-xs flex items-center justify-center">
                                {{ day.day }}
                            </span>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
@reference "../../../css/app.css";

.month-wrapper {
    @apply w-full;

    & > :not(:first-child) {
        @apply ml-3 pl-3 border-l border-gray-200;
    }
}
</style>
