<script setup>
import Year from '@/Components/Admin/Calendar/Year.vue';

defineProps({
    calendar: {type: Object, required: true},
    name: {type: String, required: true},
    shifts: {type: Array, required: true},
});
</script>

<template>
    <Year :shifts="shifts" :name="name" :calendar="calendar"/>
</template>
