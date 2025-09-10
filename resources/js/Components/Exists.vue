<script setup>
const props = defineProps({
    exists: Boolean,
});
</script>

<template>
    <p v-if="!exists" class="input-success-msg">
        <i class="pi pi-check mr-1"></i>Available
    </p>
    <p v-else-if="exists" class="input-error-msg">
        <i class="pi pi-times mr-1"></i>Already in use
    </p>
</template>
