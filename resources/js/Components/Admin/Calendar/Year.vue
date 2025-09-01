<script setup>
import {computed, ref} from 'vue';
import Month from '@/Components/Admin/Calendar/Month.vue';
import ShiftInput from '@/Components/Admin/Calendar/ShiftInput.vue';
import useAxios from '@/composables/useAxios.js';
import Select from '@/Components/Admin/Calendar/Select.vue';
import Button from '@/components/Form/Button.vue';
import {toast} from 'vue3-toastify';
import {route} from 'ziggy-js';
import {DateTime} from 'luxon';

const dateList = ref([]);
const year = ref(new Date().getFullYear());
const dayTexts = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
const monthNumbers = Array.from(Array(12).keys());
const lastDateClicked = ref(null);
const loading = ref(false);

const props = defineProps({
    shifts: {
        type: Array,
        required: true,
    },
    calendar: {
        type: Object,
        required: true,
    },
});

const updateShifts = (shifts) => {
    let activeDates = activeList.value.map(a => a.id);

    dateList.value.forEach(a => {
            if (activeDates.includes(a.id)) {
                a.nwd = shifts.nwd;
                a.shift1_start = shifts.shift1_start;
                a.shift1_end = shifts.shift1_end;
                a.shift2_start = shifts.shift2_start;
                a.shift2_end = shifts.shift2_end;
                a.shift3_start = shifts.shift3_start;
                a.shift3_end = shifts.shift3_end;
                a.shift4_start = shifts.shift4_start;
                a.shift4_end = shifts.shift4_end;
                a.isModified = getIsModified(a);
            }
        },
    );

    save();
};

const clearActive = () => {
    activeList.value.forEach(a => {
        a.active = false;
    });
};

const singleClick = (date) => {
    if (!(date.active && activeList.value.length === 1)) {
        clearActive();

        date.active = true;
        date.lastClicked = Date.now();

        lastDateClicked.value = date;
    }
};

const shiftClick = (date) => {
    if (activeList.value.length === 0) {
        singleClick(date);
    } else {
        let minDate = null;
        let maxDate = null;

        minDate = Math.min(date.timestamp, lastDateClicked.value.timestamp);
        maxDate = Math.max(date.timestamp, lastDateClicked.value.timestamp);

        dateList.value.forEach(a => {
            if (a.timestamp >= minDate && a.timestamp <= maxDate) {
                a.active = true;

                if (lastDateClicked.value.timestamp !== a.timestamp) {
                    a.lastClicked = Date.now();
                }
            }
        });
    }
};

const ctrlClick = (date) => {
    date.active = !date.active;
    date.lastClicked = Date.now();

    if (date.active === true) {
        lastDateClicked.value = date;
    }
};

const changeYear = direction => {
    if (direction === 'decrease' && year.value > 2020) {
        year.value--;
    } else if (direction === 'increase' && year.value < 2050) {
        year.value++;
    }

    getDates();
};

const save = async () => {
    loading.value = true;

    const {status, data, getResponse} = useAxios(
        route('admin.calendar-shifts.update', props.calendar),
        {
            dates: dateList.value,
            year: year.value,
        },
        'put',
    );
    await getResponse();

    if (status.value === 200) {
        toast.success(data.value.success);
    }

    loading.value = false;
};

const getShifts = async () => {
    const {status, data, getResponse} = useAxios(
        route('admin.calendar-shifts.get-shifts', props.calendar),
        {
            year: year.value,
        },
    );
    await getResponse();

    if (status.value === 200) {
        return data.value.shifts;
    }

    return [];
};

const filterMonth = (month) => {
    return dateList.value.filter(a => a.month === month);
};

function getIsModified(date) {
    return date.shift1_start !== '00:00' || date.shift1_end !== '00:00';
}

const getDates = async () => {
    loading.value = true;

    let dateArray = [];
    let currentDate = DateTime.utc(year.value, 1, 1);
    const endDate = DateTime.utc(year.value, 12, 31);
    const calendarShifts = await getShifts();

    while (currentDate <= endDate) {
        let id = currentDate.toISODate();

        let dateProperties = {
            id: id,
            timestamp: currentDate.toMillis(),
            year: currentDate.year,
            month: currentDate.month - 1,
            day: currentDate.day,
            dayOfWeek: currentDate.weekday % 7,
            active: false,
            lastClicked: 0,
            shift_date: id,
        };

        let merged = null;

        if (typeof calendarShifts[id] !== 'undefined') {
            merged = {...calendarShifts[id], ...dateProperties};
        } else {
            let temp = {
                'shift1_start': '00:00',
                'shift1_end': '00:00',
                'shift2_start': '',
                'shift2_end': '',
                'shift3_start': '',
                'shift3_end': '',
                'shift4_start': '',
                'shift4_end': '',
                'nwd': false,
            };
            merged = {...temp, ...dateProperties};
        }

        dateArray.push({...merged, ...{isModified: getIsModified(merged)}});

        currentDate = currentDate.plus({days: 1});
    }

    dateList.value = dateArray;
    loading.value = false;
};

getDates();
const selectDays = (day) => {
    dateList.value.forEach(a => {
        if (a.dayOfWeek === day) {
            a.active = true;

            if (lastDateClicked.value !== a.timestamp) {
                a.lastClicked = Date.now();
            }
        }
    });
};

const activeList = computed(() => {
    return dateList.value.filter(a => a.active === true).sort(function (a, b) {
        return a.lastClicked - b.lastClicked;
    });
});
</script>

<template>
    <div class="p-4">

        <Select :calendar-list="props.shifts"/>

        <Button v-for="(day, key) in dayTexts"
                @click="selectDays(key + 1 === 7 ? 0 : key + 1)"
                :key="day"
                :label="day"
                class="mr-1 mt-1"
        />
        <Button @click="clearActive"
                variant="danger"
                label="Clear"
                class="mt-1"
        />

        <div class="flex items-center justify-center text-gray-900 text-3xl mb-2 mt-4 lg:mt-8">
            <Button @click="changeYear('decrease')"
                    :disabled="loading || year <= 2020"
                    :loading="loading"
                    size="sm"
                    icon="pi pi-chevron-left"
            />

            <span class="mx-6 font-bold">{{ year }}</span>

            <Button @click="changeYear('increase')"
                    :disabled="loading || year >= 2050"
                    :loading="loading"
                    size="sm"
                    icon="pi pi-chevron-right"
            />
        </div>

        <div class="flex">
            <div class="grow grid xl:grid-cols-2 2xl:grid-cols-3 gap-4">
                <Month
                    v-if="dateList.length"
                    v-for="monthNumber in monthNumbers"
                    :monthNumber="monthNumber"
                    :dates="filterMonth(monthNumber)"
                    :key="monthNumber"
                    :dayTexts="dayTexts"
                    @single-click="singleClick"
                    @shift-click="shiftClick"
                    @ctrl-click="ctrlClick"
                />
            </div>

            <ShiftInput :date="activeList[0]"
                        v-if="activeList.length"
                        @updateShifts="updateShifts"
                        v-model:loading="loading"
            />

        </div>
    </div>
</template>
