<script setup>
import {computed} from 'vue';
import {DateTime} from 'luxon';

const props = defineProps({
    dates: {required: true, type: Array},
    monthNumber: {required: true, type: Number},
    dayTexts: {required: true, type: Array},
});

const monthTexts = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

function getMonday(date) {
    // date is a Luxon DateTime
    const weekday = date.weekday; // 1=Mon … 7=Sun
    return date.minus({days: weekday - 1});
}

const preDates = computed(() => {
    let date = props.dates[0];
    let firstDayOfMonth = DateTime.utc(date.year, date.month + 1, 1); // month+1 since Luxon months are 1–12
    let monday = getMonday(firstDayOfMonth);
    let dates = [];

    while (monday < firstDayOfMonth) {
        dates.push(monday.day);
        monday = monday.plus({days: 1});
    }

    return dates;
});
</script>

<template>
    <div class="border-gray-300 border bg-gray-200 rounded-sm">
        <div class="text-center text-lg bg-slate-900 text-white p-1 rounded-t">
            {{ monthTexts[monthNumber] }}
        </div>
        <div class="grid grid-cols-7">

            <span v-for="day in dayTexts" class="text-xs text-white text-center bg-slate-700" :key="day">{{
                    day
                }}</span>

            <span v-for="day in preDates" class="text-opacity-0 text-xs" :key="day"></span>

            <button v-for="day in dates"
                    :key="day.id"
                    @click.ctrl="$emit('ctrlClick', day)"
                    @click.exact="$emit('singleClick', day)"
                    @click.shift="$emit('shiftClick', day)"
                    class="text-xs py-1 text-center"
                    :class="{
                    'bg-sky-500 text-white': day.active,
                    'bg-gray-100 text-gray-900': !day.active && !day.nwd && !day.isModified,
                    'bg-green-500 text-white': !day.active && day.nwd,
                    'bg-gray-100 text-red-500': !day.active && !day.nwd && day.isModified
                }"
            >
                {{ day.day }}
            </button>
        </div>
    </div>
</template>
