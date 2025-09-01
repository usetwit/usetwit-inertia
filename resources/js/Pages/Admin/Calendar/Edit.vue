<script setup>
defineProps({
    calendar: {type: Object, required: true},
    name: {type: String, required: true},
});
</script>

<template>
    {{ name }}
</template>
